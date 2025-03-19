<?php

session_start();



// Vérifier si l'utilisateur est connecté et a le bon rôle

if (!isset($_SESSION['id_utilisateur']) || ($_SESSION['role'] !== 'delegue' && $_SESSION['role'] !== 'responsable' && $_SESSION['role'] !== 'admin')) {

    header("Location: Connexion/connexionForm.php");

    exit();

}



require_once 'config.php'; // Inclure la connexion à la base de données

$pdo_base = getPDO($array_config);





// Gestion des actions (ajout, modification, suppression, activation/désactivation)

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['ajouter'])) {

        // Ajouter un échantillon

        $nom = trim($_POST['nom']);

        $description = trim($_POST['description']);

        $stmt = $pdo_base->prepare("INSERT INTO echantillons (nom, description, actif) VALUES (?, ?, 1)");

        $stmt->execute([$nom, $description]);

        header("Location: echantillons.php");

        exit();

    } elseif (isset($_POST['modifier'])) {

        // Modifier un échantillon

        $id = $_POST['id'];

        $nom = trim($_POST['nom']);

        $description = trim($_POST['description']);

        $stmt = $pdo_base->prepare("UPDATE echantillons SET nom = ?, description = ? WHERE id = ?");

        $stmt->execute([$nom, $description, $id]);

        header("Location: echantillons.php");

        exit();

    } elseif (isset($_POST['supprimer'])) {

        // Supprimer un échantillon

        $id = $_POST['id'];

        $stmt = $pdo_base->prepare("DELETE FROM echantillons WHERE id = ?");

        $stmt->execute([$id]);

        header("Location: echantillons.php");

        exit();

    } elseif (isset($_POST['toggle'])) {

        // Activer ou désactiver un échantillon

        $id = $_POST['id'];

        $actif = $_POST['actif'] == 1 ? 0 : 1; // Inverse l'état actuel

        $stmt = $pdo_base->prepare("UPDATE echantillons SET actif = ? WHERE id = ?");

        $stmt->execute([$actif, $id]);

        header("Location: echantillons.php");

        exit();

    }

}



// Récupérer la liste des échantillons

$stmt = $pdo_base->query("SELECT * FROM echantillons");

$echantillons = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Lien vers le fichier CSS -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>Gestion des Échantillons - GSB</title>

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

        <h1 class="mb-4">Gestion des Échantillons</h1>



        <!-- Formulaire d'ajout d'échantillons -->

        <form action="echantillons.php" method="POST" class="mb-4">

            <div class="form-row">

                <div class="col-md-4">

                    <input type="text" name="nom" class="form-control" placeholder="Nom de l'échantillon" required>

                </div>

                <div class="col-md-6">

                    <input type="text" name="description" class="form-control" placeholder="Description de l'échantillon" required>

                </div>

                <div class="col-md-2">

                    <button type="submit" name="ajouter" class="btn btn-primary btn-block">Ajouter</button>

                </div>

            </div>

        </form>



        <!-- Table des échantillons -->

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nom</th>

                    <th>Description</th>

                    <th>Statut</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($echantillons as $echantillon): ?>

                    <tr>

                        <td><?php echo $echantillon['id']; ?></td>

                        <td><?php echo htmlspecialchars($echantillon['nom']); ?></td>

                        <td><?php echo htmlspecialchars($echantillon['description']); ?></td>

                        <td><?php echo $echantillon['actif'] ? 'Actif' : 'Désactivé'; ?></td>

                        <td>

                            <!-- Formulaire de modification -->

                            <form action="echantillons.php" method="POST" class="d-inline">

                                <input type="hidden" name="id" value="<?php echo $echantillon['id']; ?>">

                                <input type="text" name="nom" value="<?php echo htmlspecialchars($echantillon['nom']); ?>" class="form-control d-inline w-25" required>

                                <input type="text" name="description" value="<?php echo htmlspecialchars($echantillon['description']); ?>" class="form-control d-inline w-50" required>

                                <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>

                            </form>



                            <!-- Formulaire d'activation/désactivation -->

                            <form action="echantillons.php" method="POST" class="d-inline">

                                <input type="hidden" name="id" value="<?php echo $echantillon['id']; ?>">

                                <input type="hidden" name="actif" value="<?php echo $echantillon['actif']; ?>">

                                <button type="submit" name="toggle" class="btn <?php echo $echantillon['actif'] ? 'btn-secondary' : 'btn-success'; ?>">

                                    <?php echo $echantillon['actif'] ? 'Désactiver' : 'Activer'; ?>

                                </button>

                            </form>



                            <!-- Formulaire de suppression -->

                            <form action="echantillons.php" method="POST" class="d-inline">

                                <input type="hidden" name="id" value="<?php echo $echantillon['id']; ?>">

                                <!-- <button type="submit" name="supprimer" class="btn btn-danger">Supprimer</button> -->

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

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

