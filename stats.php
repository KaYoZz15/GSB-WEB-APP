<?php
require_once 'config.php'; // Include database connection
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: Connexion/connexionForm.php");
    exit();
}

$pdo_base = getPDO($array_config);
$id_utilisateur = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];

// Fetch total visits for the user
$sql = "SELECT COUNT(*) as total_visites FROM compte_rendu WHERE id_utilisateur = ?";
$stmt = $pdo_base->prepare($sql);
$stmt->execute([$id_utilisateur]);
$total_visites = $stmt->fetchColumn();

// Fetch monthly visits for the user
$sql = "SELECT MONTH(date_visite) as mois, COUNT(*) as total_visites FROM compte_rendu WHERE id_utilisateur = ? GROUP BY MONTH(date_visite)";
$stmt = $pdo_base->prepare($sql);
$stmt->execute([$id_utilisateur]);
$visites_par_mois = $stmt->fetchAll(PDO::FETCH_ASSOC);
$all_visites = array_fill(1, 12, 0);
foreach ($visites_par_mois as $row) {
    $all_visites[$row['mois']] = $row['total_visites'];
}

// Global statistics for admins and responsables
if ($role === 'admin' || $role === 'responsable' || $role === 'delegue') {
    if ($role === 'delegue') {
        // For délégué role: total CR count in their region
        $region_id = $_SESSION['region_id'] ?? null;
        if ($region_id) {
            $sql = "SELECT COUNT(*) as region_visites FROM compte_rendu 
                    JOIN utilisateurs ON compte_rendu.id_utilisateur = utilisateurs.id 
                    WHERE utilisateurs.region_id = ?";
            $stmt = $pdo_base->prepare($sql);
            $stmt->execute([$region_id]);
            $region_visites = $stmt->fetchColumn();
        }
    } else {
        // For responsable/admin roles: CR count by region and sample distribution
        $sql = "SELECT regions.name AS region_name, COUNT(*) as total_visites 
                FROM compte_rendu 
                JOIN utilisateurs ON compte_rendu.id_utilisateur = utilisateurs.id 
                JOIN regions ON utilisateurs.region_id = regions.id 
                GROUP BY regions.name";
        $stmt = $pdo_base->query($sql);
        $visites_par_region = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total sample distribution by individual sample and by region
        $sql = "SELECT echantillons.nom AS sample_name, COUNT(compte_rendu.id) as distribution_count 
                FROM compte_rendu 
                JOIN echantillons ON FIND_IN_SET(echantillons.id, compte_rendu.echantillons_distribues) 
                GROUP BY echantillons.nom 
                ORDER BY distribution_count DESC";
        $stmt = $pdo_base->query($sql);
        $samples_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT regions.name AS region_name, 
                       SUM(LENGTH(compte_rendu.echantillons_distribues) - LENGTH(REPLACE(compte_rendu.echantillons_distribues, ',', '')) + 1) AS total_samples
                FROM compte_rendu 
                JOIN utilisateurs ON compte_rendu.id_utilisateur = utilisateurs.id
                JOIN regions ON utilisateurs.region_id = regions.id
                WHERE compte_rendu.echantillons_distribues IS NOT NULL
                GROUP BY regions.name";
        $stmt = $pdo_base->query($sql);
        $samples_by_region = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="icon" href="favicon.ico" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Statistiques - GSB</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <a class="navbar-brand" href="espacemembre.php">
        <img src="https://i.imgur.com/kZxkmAy.png" alt="Logo GSB" style="height: 70px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto ml-auto">
            <li class="nav-item"><a class="nav-link mr-4" href="espacemembre.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link mr-4" href="cr.php">CR</a></li>
            <li class="nav-item"><a class="nav-link mr-4" href="stats.php">Stats</a></li>
            <?php if ($role === 'delegue' || $role === 'responsable' || $role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link mr-4" href="echantillons.php">Échantillons</a></li>
                <li class="nav-item"><a class="nav-link mr-4" href="creer_praticien.php">Praticien</a></li>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link mr-4" href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="mb-4 text-dark">Statistiques Personnelles</h1>

    <div class="row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
            <div class="card text-left shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Statistiques des Visites</h3>
                </div>
                <div class="card-body">
                    <h2 class="card-title"><?php echo htmlspecialchars($total_visites); ?></h2>
                    <p class="card-text">Nombre total de visites enregistrées.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-left">
                    <h4 class="card-title">Visites par Mois</h4>
                </div>
                <div class="card-body">
                    <canvas id="visitesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php if ($role === 'delegue' && isset($region_visites)): ?>
        <div class="container mt-5">
            <h2 class="text-dark">Statistiques pour Délégué</h2>
            <div class="card text-left shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>Nombre de Comptes Rendus dans votre Région</h4>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($region_visites); ?></h3>
                    <p>Comptes rendus dans votre région.</p>
                </div>
            </div>
        </div>
    <?php elseif (($role === 'responsable' || $role === 'admin') && !empty($visites_par_region)): ?>
        <div class="container mt-5">
            <h2 class="text-dark">Statistiques Globales</h2>

            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white text-left">
                            <h4>Visites par Région</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="regionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white text-left">
                            <h4>Échantillons Distribués par Région</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php foreach ($samples_by_region as $region_sample): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($region_sample['region_name']); ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($region_sample['total_samples']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white text-left">
                            <h4>Distribution des Échantillons</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php foreach ($samples_distribution as $sample): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($sample['sample_name']); ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($sample['distribution_count']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const ctx = document.getElementById('visitesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Visites par Mois',
                data: <?php echo json_encode(array_values($all_visites)); ?>,
                backgroundColor: 'rgba(56, 182, 255, 0.6)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    <?php if ($role === 'responsable' || $role === 'admin'): ?>
    const regionCtx = document.getElementById('regionChart').getContext('2d');
    new Chart(regionCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($visites_par_region, 'region_name')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($visites_par_region, 'total_visites')); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });
    <?php endif; ?>
</script>

<footer class="bg-light text-center text-lg-start mt-5">
    <div class="container p-4">
        <img src="https://i.imgur.com/kZxkmAy.png" alt="Logo GSB" style="height: 50px;">
    </div>
    <div class="text-center p-3 bg-dark text-white">
        © 2024 GSB-VisitTrack. Tous droits réservés.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





