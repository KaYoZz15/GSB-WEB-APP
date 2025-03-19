<?php

session_start();



// Check if the user is logged in and has the required role

if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] !== 'delegue' && $_SESSION['role'] !== 'responsable' && $_SESSION['role'] !== 'admin')) {

    header("Location: Connexion/connexionForm.php");

    exit();

}



require_once 'config.php'; // Include the database connection

$pdo_base = getPDO($array_config);



// Process form submission to add a new practitioner

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_praticien'])) {

    $nom = trim($_POST['nom']);

    $specialite = trim($_POST['specialite']);

    $region_id = $_POST['region_id'];



    // Insert new practitioner into the database

    $stmt = $pdo_base->prepare("INSERT INTO praticiens (nom, specialite, region_id, actif) VALUES (?, ?, ?, 1)");

    $stmt->execute([$nom, $specialite, $region_id]);



    echo '<div class="alert alert-success">Praticien ajouté avec succès !</div>';

}



// Process form submission to update a practitioner

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_praticien'])) {

    $praticien_id = $_POST['praticien_id'];

    $nom = $_POST['nom'];

    $specialite = $_POST['specialite'];

    $region_id = $_POST['region_id'];



    // Update the practitioner in the database

    $stmt = $pdo_base->prepare("UPDATE praticiens SET nom = ?, specialite = ?, region_id = ? WHERE id = ?");

    $stmt->execute([$nom, $specialite, $region_id, $praticien_id]);



    echo '<div class="alert alert-info">Praticien mis à jour avec succès !</div>';

}



// Process status toggle (activate/deactivate) for a practitioner

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {

    $praticien_id = $_POST['praticien_id'];

    $new_status = $_POST['new_status'];



    // Update practitioner's status in the database

    $stmt = $pdo_base->prepare("UPDATE praticiens SET actif = ? WHERE id = ?");

    $stmt->execute([$new_status, $praticien_id]);



    echo '<div class="alert alert-info">Statut du praticien mis à jour avec succès !</div>';

}



// Fetch existing practitioners

$stmt = $pdo_base->query("SELECT praticiens.*, regions.name AS region_name FROM praticiens LEFT JOIN regions ON praticiens.region_id = regions.id");

$praticiens = $stmt->fetchAll();

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/styles.css">

    <title>Créer un Praticien - GSB</title>

</head>

<body>



    <!-- Navigation bar -->

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">

    <a class="navbar-brand" href="espacemembre.php">

        <img src="assets/images/GSB-logo.png" alt="Logo GSB" style="height: 70px;">

    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">

        <span class="navbar-toggler-icon"></span>

    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <ul class="navbar-nav mr-auto ml-auto">

            <li class="nav-item">

                <a class="nav-link mr-4" href="espacemembre.php">Accueil</a>

            </li>

            <li class="nav-item">

                <a class="nav-link mr-4" href="cr.php">CR</a>

            </li>

            <li class="nav-item">

                <a class="nav-link mr-4" href="stats.php">Stats</a>

            </li>

            <?php if ($_SESSION['role'] === 'delegue' || $_SESSION['role'] === 'responsable' || $_SESSION['role'] === 'admin'): ?>

                    <li class="nav-item">

                        <a class="nav-link mr-4" href="echantillons.php">Échantillons</a>

                    </li>

                    <li class="nav-item">

                    <a class="nav-link mr-4" href="creer_praticien.php">Praticien</a>

                </li>

                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>

                <li class="nav-item">

                    <a class="nav-link mr-4" href="admin.php">Admin</a>

                </li>

                <?php endif; ?>

            <li class="nav-item">

                <a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a>

            </li>

        </ul>

    </div>

</nav>



    <div class="container mt-5">

        <h1 class="mb-4">Créer un Praticien</h1>



        <!-- Form to add a new practitioner -->

        <div class="card mb-4">

            <div class="card-header">Ajouter un Nouveau Praticien</div>

            <div class="card-body">

                <form action="creer_praticien.php" method="POST">

                    <div class="form-row">

                        <div class="col-md-4 mb-3">

                            <label for="nom" class="form-label">Nom :</label>

                            <input type="text" class="form-control" id="nom" name="nom" required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label for="specialite" class="form-label">Spécialité :</label>

                            <input type="text" class="form-control" id="specialite" name="specialite" required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label for="region_id" class="form-label">Région :</label>

                            <select class="form-control" id="region_id" name="region_id" required>

                                <?php

                                $stmt = $pdo_base->query("SELECT id, name FROM regions");

                                while ($region = $stmt->fetch()) {

                                    echo "<option value='" . $region['id'] . "'>" . htmlspecialchars($region['name']) . "</option>";

                                }

                                ?>

                            </select>

                        </div>

                    </div>

                    <button type="submit" name="add_praticien" class="btn btn-primary">Ajouter le Praticien</button>

                </form>

            </div>

        </div>



        <!-- Table to display existing practitioners -->

        <h2>Liste des Praticiens</h2>

        <table class="table table-bordered">

            <thead class="thead-light">

                <tr>

                    <th>Nom</th>

                    <th>Spécialité</th>

                    <th>Région</th>

                    <th>Statut</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($praticiens as $praticien): ?>

                    <tr>

                        <td><?php echo htmlspecialchars($praticien['nom']); ?></td>

                        <td><?php echo htmlspecialchars($praticien['specialite']); ?></td>

                        <td><?php echo htmlspecialchars($praticien['region_name']); ?></td>

                        <td><?php echo $praticien['actif'] ? 'Actif' : 'Inactif'; ?></td>

                        <td>

                            <!-- Toggle active/inactive status -->

                            <form action="creer_praticien.php" method="POST" class="d-inline">

                                <input type="hidden" name="praticien_id" value="<?php echo $praticien['id']; ?>">

                                <input type="hidden" name="new_status" value="<?php echo $praticien['actif'] ? 0 : 1; ?>">

                                <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $praticien['actif'] ? 'btn-danger' : 'btn-success'; ?>">

                                    <?php echo $praticien['actif'] ? 'Désactiver' : 'Activer'; ?>

                                </button>

                            </form>



                            <!-- Edit practitioner button -->

                            <button class="btn btn-warning btn-sm" onclick="editPraticien(<?php echo $praticien['id']; ?>, '<?php echo htmlspecialchars($praticien['nom']); ?>', '<?php echo htmlspecialchars($praticien['specialite']); ?>', <?php echo $praticien['region_id']; ?>)">

                                Modifier

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>



    <!-- Modal for editing practitioner details -->

    <div class="modal fade" id="editPraticienModal" tabindex="-1" role="dialog" aria-labelledby="editPraticienModalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="editPraticienModalLabel">Modifier Praticien</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <form action="creer_praticien.php" method="POST">

                        <input type="hidden" id="praticien_id" name="praticien_id">

                        <div class="form-group">

                            <label for="nom">Nom :</label>

                            <input type="text" class="form-control" id="edit_nom" name="nom" required>

                        </div>

                        <div class="form-group">

                            <label for="specialite">Spécialité :</label>

                            <input type="text" class="form-control" id="edit_specialite" name="specialite" required>

                        </div>

                        <div class="form-group">

                            <label for="region_id">Région :</label>

                            <select class="form-control" id="edit_region_id" name="region_id" required>

                                <?php

                                $stmt = $pdo_base->query("SELECT id, name FROM regions");

                                while ($region = $stmt->fetch()) {

                                    echo "<option value='" . $region['id'] . "'>" . htmlspecialchars($region['name']) . "</option>";

                                }

                                ?>

                            </select>

                        </div>

                        <button type="submit" name="update_praticien" class="btn btn-primary">Enregistrer les modifications</button>

                    </form>

                </div>

            </div>

        </div>

    </div>



    <!-- Footer -->

    <footer class="bg-light text-center text-lg-start mt-5">

        <div class="container p-4">

            <div class="row">

                <div class="col-lg-12 col-md-12 mb-4 mb-md-0">

                    <img src="assets/images/GSB-logo.png" alt="Logo GSB" style="height: 50px;">

                </div>

            </div>

        </div>

        <div class="text-center p-3 bg-dark text-white">

            © 2024 GSB-VisitTrack. Tous droits réservés.

        </div>

    </footer>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        function editPraticien(id, nom, specialite, region_id) {

            document.getElementById('praticien_id').value = id;

            document.getElementById('edit_nom').value = nom;

            document.getElementById('edit_specialite').value = specialite;

            document.getElementById('edit_region_id').value = region_id;

            $('#editPraticienModal').modal('show');

        }

    </script>

</body>

</html>

