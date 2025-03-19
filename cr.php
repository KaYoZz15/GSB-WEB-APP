<?php

session_start();



// Désactiver la mise en cache du navigateur

header("Cache-Control: no-cache, must-revalidate");

header("Pragma: no-cache");

header("Expires: 0");



// Vérifier si l'utilisateur est connecté

if (!isset($_SESSION['id_utilisateur'])) {

    header("Location: Connexion/connexionForm.php");

    exit();

}



require_once 'config.php'; // Connexion à la base de données

$pdo_base = getPDO($array_config);



// Récupérer les informations de l'utilisateur connecté

$id_utilisateur = $_SESSION['id_utilisateur'];

$role = $_SESSION['role'];



// Récupérer la région de l'utilisateur

$query = "

    SELECT utilisateurs.*, regions.name AS region_name 

    FROM utilisateurs 

    LEFT JOIN regions ON utilisateurs.region_id = regions.id 

    WHERE utilisateurs.id = ?";

$stmt = $pdo_base->prepare($query);

$stmt->execute([$id_utilisateur]);

$user = $stmt->fetch();



$is_delegue = ($role === 'delegue');

$is_responsable = ($role === 'responsable');

$is_admin = ($role === 'admin');

$region_id = $user['region_id'];

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

    <title>Gestion des Comptes Rendus - GSB</title>

</head>

<body>



    <!-- Barre de navigation -->

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

                <?php if ($is_delegue || $is_responsable || $is_admin): ?>

                    <li class="nav-item">

                        <a class="nav-link mr-4" href="echantillons.php">Échantillons</a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link mr-4" href="creer_praticien.php">Praticien</a>

                    </li>

                <?php endif; ?>

                <li class="nav-item">

                    <a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a>

                </li>

            </ul>

        </div>

    </nav>  



    <div class="container mt-5">

        <h1 class="mb-4">Gestion des Comptes Rendus</h1>



        <!-- Formulaire de création de compte rendu pour tous les utilisateurs connectés -->

        <div class="card mb-4">

            <div class="card-header">Nouveau Compte Rendu</div>

            <div class="card-body">

                <form action="cr.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="action" value="create">

                    <div class="form-row">

                        <div class="col-md-6 mb-3">

                            <label for="date_visite" class="form-label">Date de visite :</label>

                            <input type="date" class="form-control" id="date_visite" name="date_visite" required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="praticien_id" class="form-label">Praticien :</label>

                            <select id="praticien_id" name="praticien_id" class="form-control" required>

                                <option value="">Sélectionnez un praticien</option>

                                <?php

                                $stmt = $pdo_base->query("SELECT id, nom, specialite FROM praticiens WHERE actif = 1");

                                while ($praticien = $stmt->fetch()) {

                                    echo "<option value='" . htmlspecialchars($praticien['id']) . "'>" . htmlspecialchars($praticien['nom']) . " - " . htmlspecialchars($praticien['specialite']) . "</option>";

                                }

                                ?>

                            </select>

                        </div>

                    </div>

                    

                    <!-- Multi-select dropdown for active samples -->

                    <div class="mb-3">

                        <label for="echantillons" class="form-label">Échantillons distribués :</label>

                        <select id="echantillons" name="echantillons[]" class="form-control select2" multiple="multiple">

                            <?php

                            $stmt = $pdo_base->query("SELECT * FROM echantillons WHERE actif = 1");

                            while ($row = $stmt->fetch()) {

                                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom']) . "</option>";

                            }

                            ?>

                        </select>

                    </div>



                    <div class="mb-3">

                        <label for="piece_jointe" class="form-label">Pièce jointe :</label>

                        <input type="file" class="form-control" id="piece_jointe" name="piece_jointe">

                    </div>

                    <div class="mb-3">

                        <label for="commentaires" class="form-label">Commentaires :</label>

                        <textarea class="form-control" id="commentaires" name="commentaires" rows="3"></textarea>

                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Enregistrer le compte rendu</button>

                </form>

            </div>

        </div>



        <?php

        // Traitement de la création de compte rendu

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {

            $date_visite = $_POST['date_visite'];

            $praticien_id = $_POST['praticien_id'];

            $commentaires = $_POST['commentaires'];

            

            $echantillons_distribues = !empty($_POST['echantillons']) ? implode(',', $_POST['echantillons']) : null;



            // Gestion de l'upload de fichier

            $piece_jointe = null;

            if (!empty($_FILES['piece_jointe']['name'])) {

                $piece_jointe = 'uploads/' . basename($_FILES['piece_jointe']['name']);

                move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $piece_jointe);

            }



            $stmt = $pdo_base->prepare("INSERT INTO compte_rendu (id_utilisateur, date_visite, praticien_id, commentaires, piece_jointe, echantillons_distribues) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id_utilisateur, $date_visite, $praticien_id, $commentaires, $piece_jointe, $echantillons_distribues]);



            echo '<div class="alert alert-success">Compte rendu enregistré avec succès !</div>';

        }



        // Affichage des comptes rendus propres à chaque utilisateur avec le nom du praticien

        echo "<h2>Mes Comptes Rendus</h2>";

        echo '<table class="table table-bordered">';

        echo '<thead class="thead-light"><tr><th>Date de visite</th><th>Praticien</th><th>Actions</th></tr></thead>';

        echo '<tbody>';



        $stmt = $pdo_base->prepare("

            SELECT compte_rendu.*, praticiens.nom AS praticien_nom 

            FROM compte_rendu 

            JOIN praticiens ON compte_rendu.praticien_id = praticiens.id

            WHERE compte_rendu.id_utilisateur = ?

        ");

        $stmt->execute([$id_utilisateur]);



        while ($row = $stmt->fetch()) {

            echo "<tr>";

            echo "<td>" . htmlspecialchars($row['date_visite']) . "</td>";

            echo "<td>" . htmlspecialchars($row['praticien_nom']) . "</td>";

            echo "<td>";

            echo "<a href='consulter_compte_rendu.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-info btn-sm'>Consulter</a> ";

            echo "<a href='modifier_compte_rendu.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-warning btn-sm'>Modifier</a>";

            echo "</td>";

            echo "</tr>";

        }



        echo '</tbody>';

        echo '</table>';



        // Affichage des comptes rendus pour les responsables et délégués

        if ($is_delegue || $is_responsable || $is_admin) {

            echo "<h2>Comptes Rendus des Visiteurs</h2>";

            echo '<table class="table table-bordered">';

            echo '<thead class="thead-light"><tr><th>Date de visite</th><th>Visiteur</th><th>Région</th><th>Praticien</th><th>Actions</th></tr></thead>';

            echo '<tbody>';



            $query = "SELECT compte_rendu.*, utilisateurs.nom AS utilisateur_nom, utilisateurs.prenom AS utilisateur_prenom, regions.name AS region_name, praticiens.nom AS praticien_nom

                      FROM compte_rendu

                      JOIN utilisateurs ON compte_rendu.id_utilisateur = utilisateurs.id

                      LEFT JOIN regions ON utilisateurs.region_id = regions.id

                      JOIN praticiens ON compte_rendu.praticien_id = praticiens.id ";

            if ($is_delegue) {

                $query .= "WHERE utilisateurs.region_id = ? AND utilisateurs.role = 'visiteur'";

                $stmt = $pdo_base->prepare($query);

                $stmt->execute([$region_id]);

            } else {

                $stmt = $pdo_base->prepare($query);

                $stmt->execute();

            }



            while ($row = $stmt->fetch()) {

                echo "<tr>";

                echo "<td>" . htmlspecialchars($row['date_visite']) . "</td>";

                echo "<td>" . htmlspecialchars($row['utilisateur_prenom'] . ' ' . $row['utilisateur_nom']) . "</td>";

                echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";

                echo "<td>" . htmlspecialchars($row['praticien_nom']) . "</td>";

                echo "<td>";

                echo "<a href='consulter_compte_rendu.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-info btn-sm'>Consulter</a>";

                echo "</td>";

                echo "</tr>";

            }



            echo '</tbody>';

            echo '</table>';

        }

        ?>

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



    <!-- JavaScript Libraries -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>

        $(document).ready(function() {

            $('.select2').select2({

                placeholder: "Sélectionnez des échantillons",

                width: '100%'

            });

        });

    </script>

</body>

</html>

