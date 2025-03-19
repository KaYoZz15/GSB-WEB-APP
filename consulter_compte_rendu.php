<?php

session_start();



// Vérifier si l'utilisateur est connecté

if (!isset($_SESSION['id_utilisateur'])) {

    header("Location: Connexion/connexionForm.php");

    exit();

}



require_once 'config.php'; // Inclure la connexion à la base de données

$pdo_base = getPDO($array_config);



// Récupérer l'ID du compte rendu depuis l'URL

if (!isset($_GET['id'])) {

    echo "ID de compte rendu manquant.";

    exit();

}

$compte_rendu_id = $_GET['id'];



// Rechercher le compte rendu dans la base de données, incluant le nom du praticien

$stmt = $pdo_base->prepare("

    SELECT compte_rendu.*, utilisateurs.nom AS utilisateur_nom, utilisateurs.prenom AS utilisateur_prenom, 

           utilisateurs.role AS utilisateur_role, regions.name AS region_name, praticiens.nom AS praticien_nom, 

           praticiens.specialite AS praticien_specialite

    FROM compte_rendu 

    JOIN utilisateurs ON compte_rendu.id_utilisateur = utilisateurs.id

    LEFT JOIN regions ON utilisateurs.region_id = regions.id

    LEFT JOIN praticiens ON compte_rendu.praticien_id = praticiens.id

    WHERE compte_rendu.id = ?

");

$stmt->execute([$compte_rendu_id]);

$compte_rendu = $stmt->fetch();



// Vérifier si le compte rendu existe

if (!$compte_rendu) {

    echo "Compte rendu introuvable.";

    exit();

}

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/styles.css">

    <title>Consultation du Compte Rendu - GSB</title>

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

                    <a class="nav-link mr-4" href="cr.php">Compte Rendu</a>

                </li>

                <li class="nav-item">

                    <a class="nav-link mr-4" href="stats.php">Statistiques</a>

                </li>

                <li class="nav-item">

                    <a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a>

                </li>

            </ul>

        </div>

    </nav>



    <div class="container mt-5">

        <h1 class="mb-4">Détails du Compte Rendu</h1>



        <!-- Affichage des informations du compte rendu -->

        <div class="card mb-4">

            <div class="card-header">Informations du Compte Rendu</div>

            <div class="card-body">

                <p><strong>Date de visite :</strong> <?php echo htmlspecialchars($compte_rendu['date_visite']); ?></p>

                <p><strong>Praticien :</strong> <?php echo htmlspecialchars($compte_rendu['praticien_nom']); ?> - <?php echo htmlspecialchars($compte_rendu['praticien_specialite']); ?></p>

                <p><strong>Échantillons distribués :</strong> 

                    <?php 

                    if ($compte_rendu['echantillons_distribues']) {

                        $echantillons_list = [];

                        $echantillons_ids = explode(',', $compte_rendu['echantillons_distribues']);

                        foreach ($echantillons_ids as $id) {

                            $stmt_echantillon = $pdo_base->prepare("SELECT nom FROM echantillons WHERE id = ?");

                            $stmt_echantillon->execute([$id]);

                            $echantillon = $stmt_echantillon->fetch();

                            if ($echantillon) {

                                $echantillons_list[] = htmlspecialchars($echantillon['nom']);

                            }

                        }

                        echo implode(', ', $echantillons_list);

                    } else {

                        echo "Aucun";

                    }

                    ?>

                </p>

                <p><strong>Commentaires :</strong> <?php echo nl2br(htmlspecialchars($compte_rendu['commentaires'])); ?></p>

                <p><strong>Pièce jointe :</strong> 

                    <?php 

                    if ($compte_rendu['piece_jointe']) {

                        echo "<a href='" . htmlspecialchars($compte_rendu['piece_jointe']) . "' target='_blank'>Voir la pièce jointe</a>";

                    } else {

                        echo "Aucune";

                    }

                    ?>

                </p>

            </div>

        </div>



        <!-- Affichage des informations de l'auteur du compte rendu -->

        <div class="card mb-4">

            <div class="card-header">Informations de l'Auteur</div>

            <div class="card-body">

                <p><strong>Nom :</strong> <?php echo htmlspecialchars($compte_rendu['utilisateur_prenom'] . ' ' . $compte_rendu['utilisateur_nom']); ?></p>

                <p><strong>Rôle :</strong> <?php echo htmlspecialchars($compte_rendu['utilisateur_role']); ?></p>

                <p><strong>Région :</strong> <?php echo htmlspecialchars($compte_rendu['region_name']); ?></p>

            </div>

        </div>



        <a href="cr.php" class="btn btn-secondary">Retour à la liste des Comptes Rendus</a>

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

</body>

</html>

