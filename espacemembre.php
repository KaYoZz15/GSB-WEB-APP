<?php

session_start();



// Vérifier si l'utilisateur est connecté et s'il a le rôle "visiteur"

if (!isset($_SESSION['pseudo']) || !isset($_SESSION['email'])) {

    // Rediriger vers la page de connexion s'il n'est pas authentifié

    header("Location: Connexion/connexionForm.php");

    exit();

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

    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Lien vers le fichier CSS -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Espace Membre - GSB</title>

</head>

<body>

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

                <!-- <li class="nav-item">

                    <a class="nav-link mr-4" href="modifier_profil.php">Profil</a>

                </li> -->

                <li class="nav-item">

                <a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a>

                </li>

            </ul>

        </div>

    </nav>  



    <div class="container mt-4">

        <div class="card-wrapper">

            <!-- Carré bleu en arrière-plan -->

            <div class="blue-background"></div>



            <!-- Carte blanche avec contenu et icône -->

            <div class="card-custom">

                <!-- Icône et titre collés ensemble -->

                <h1 class="card-title">

                    <span class="info-icon">

                        <i class="fas fa-info-circle"></i>

                    </span>

                    Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']); ?> sur GSB-VisitTrack !

                </h1>

                <p class="card-text">

                    Gérez efficacement vos comptes rendus de visite médicale et suivez vos performances avec l’application GSB-VisitTrack intuitive et sécurisée.

                </p>

                <p class="card-text">

                    Grâce à GSB-VisitTrack, vous pouvez :

                </p>

                <ul>

                    <li>Enregistrer et consulter facilement vos comptes rendus.</li>

                    <li>Suivre vos statistiques de visites et vos échantillons distribués.</li>

                    <li>Communiquer avec vos équipes en quelques clics.</li>

                </ul>

            </div>

        </div>

    </div>





    <!-- Section principale avec deux cartes côte à côte -->

    <div class="container mt-5">

        <div class="row">

            <!-- Carte gauche avec carré rouge en haut à gauche -->

            <div class="col-lg-6 mb-4">

                <div class="card-wrapper">

                    <!-- Carré rouge en haut à gauche -->

                    <div class="red-background"></div>



                    <!-- Carte blanche avec contenu et icône -->

                    <div class="card-custom">

                        <!-- Icône et titre collés ensemble -->

                        <h2 class="card-title-h2">

                            <span class="info-icon-red">

                            <i class="fas fa-newspaper"></i>

                            </span>

                            Actualités du laboratoire GSB

                        </h2>

                        <p class="card-text">

                            Contenu de la carte gauche. Insère ici la description et d'autres détails.

                        </p>

                    </div>

                </div>

            </div>



            <!-- Carte droite avec carré bleu en haut à droite -->

            <div class="col-lg-6 mb-4">

                <div class="card-wrapper">

                    <!-- Carré bleu en haut à droite -->

                    <div class="blue-background-bas"></div>



                    <!-- Carte blanche avec contenu et icône -->

                    <div class="card-custom">

                        <!-- Icône et titre collés ensemble -->

                        <h2 class="card-title-h2">

                            <span class="info-icon">

                                <i class="fas fa-server"></i>

                            </span>

                            Mise à jour de l'application

                        </h2>

                        <p class="card-text">

                            Contenu de la carte droite. Insère ici la description et d'autres détails.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>





    <div class="container mt-4">

        <div class="card-wrapper">

            <!-- Carré bleu en arrière-plan -->

            <div class="blue-background-gauche"></div>



            <!-- Carte blanche avec contenu et icône -->

            <div class="card-custom">

                <!-- Icône et titre collés ensemble -->

                <h2 class="card-title-h2">

                    <span class="info-icon">

                        <i class="fas fa-info-circle"></i>

                    </span>

                    Principales Fonctionnalités

                </h2>

                <p class="card-text">

                    Gérez efficacement vos comptes rendus de visite médicale et suivez vos performances avec l’application GSB-VisitTrack intuitive et sécurisée.

                </p>

                <p class="card-text">

                    Grâce à GSB-VisitTrack, vous pouvez :

                </p>

                <ul>

                    <li>Enregistrer et consulter facilement vos comptes rendus.</li>

                    <li>Suivre vos statistiques de visites et vos échantillons distribués.</li>

                    <li>Communiquer avec vos équipes en quelques clics.</li>

                </ul>

            </div>

        </div>

    </div>





    <div class="container mt-4">

        <div class="card-wrapper">

            <!-- Carré bleu en arrière-plan -->

            <div class="red-background-bas"></div>



            <!-- Carte blanche avec contenu et icône -->

            <div class="card-custom">

                <!-- Icône et titre collés ensemble -->

                <h2 class="card-title-h2">

                    <span class="info-icon-red">

                        <i class="fas fa-question"></i>

                    </span>

                    Support

                </h2>

                <p class="card-text">

            Pour toute question ou demande d'assistance, notre équipe est là pour vous aider. <br>

            N'hésitez pas à nous contacter via “contact@gsb-visittrack.fr” pour obtenir une aide rapide et efficace.                               </p>

            </p>

            </div>

        </div>

    </div>







    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

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

</html>

