<?php
session_start();

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: Connexion/connexionForm.php");
    exit();
}

require_once 'config.php'; // Inclure la connexion à la base de données
$pdo_base = getPDO($array_config);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Gestion des Utilisateurs - GSB</title>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <a class="navbar-brand" href="espacemembre.php">
            <img src="https://i.imgur.com/kZxkmAy.png" alt="Logo GSB" style="height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto ml-auto">
                <li class="nav-item">
                    <a class="nav-link mr-4" href="espacemembre.php">Accueil</a>
                </li>
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
        <h1 class="mb-4">Gestion des Utilisateurs</h1>

        <!-- Formulaire de création d'un nouvel utilisateur -->
        <div class="card mb-4">
            <div class="card-header">Ajouter un Nouvel Utilisateur</div>
            <div class="card-body">
                <form action="admin.php" method="POST">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="pseudo" class="form-label">Pseudo :</label>
                            <input type="text" class="form-control" id="pseudo" name="pseudo" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="nom" class="form-label">Nom :</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="prenom" class="form-label">Prénom :</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Rôle :</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="visiteur">Visiteur</option>
                                <option value="delegue">Délégué</option>
                                <option value="responsable">Responsable</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
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
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Statut :</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Ajouter l'Utilisateur</button>
                </form>
            </div>
        </div>

        <!-- Formulaire de modification d'un utilisateur -->
        <?php
        if (isset($_GET['edit'])) {
            $user_id = $_GET['edit'];
            $stmt = $pdo_base->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        ?>

        <div class="card mb-4">
            <div class="card-header">Modifier Utilisateur</div>
            <div class="card-body">
                <form action="admin.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="pseudo" class="form-label">Pseudo :</label>
                            <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="nom" class="form-label">Nom :</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="prenom" class="form-label">Prénom :</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Rôle :</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="visiteur" <?php if ($user['role'] == 'visiteur') echo 'selected'; ?>>Visiteur</option>
                                <option value="delegue" <?php if ($user['role'] == 'delegue') echo 'selected'; ?>>Délégué</option>
                                <option value="responsable" <?php if ($user['role'] == 'responsable') echo 'selected'; ?>>Responsable</option>
                                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region_id" class="form-label">Région :</label>
                            <select class="form-control" id="region_id" name="region_id" required>
                                <?php
                                $stmt = $pdo_base->query("SELECT id, name FROM regions");
                                while ($region = $stmt->fetch()) {
                                    $selected = $region['id'] == $user['region_id'] ? 'selected' : '';
                                    echo "<option value='" . $region['id'] . "' $selected>" . htmlspecialchars($region['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_user" class="btn btn-primary">Enregistrer les Modifications</button>
                </form>
            </div>
        </div>
        <?php
        }

        // Traitement de l'ajout d'un utilisateur
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
            $pseudo = $_POST['pseudo'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $region_id = $_POST['region_id'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $status = $_POST['status'];

            $stmt = $pdo_base->prepare("INSERT INTO utilisateurs (pseudo, nom, prenom, email, role, region_id, mot_de_passe, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$pseudo, $nom, $prenom, $email, $role, $region_id, $password, $status]);

            echo '<div class="alert alert-success">Utilisateur ajouté avec succès !</div>';
        }

        // Traitement de la modification d'un utilisateur
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
            $user_id = $_POST['user_id'];
            $pseudo = $_POST['pseudo'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $region_id = $_POST['region_id'];

            $stmt = $pdo_base->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ?, email = ?, role = ?, region_id = ? WHERE id = ?");
            $stmt->execute([$pseudo, $nom, $prenom, $email, $role, $region_id, $user_id]);

            echo '<div class="alert alert-info">Utilisateur modifié avec succès !</div>';
            echo '<script>window.location.href="admin.php";</script>';
            exit();
        }

        // Affichage de la liste des utilisateurs
        echo "<h2>Liste des Utilisateurs</h2>";
        echo '<table class="table table-bordered">';
        echo '<thead class="thead-light"><tr><th>Pseudo</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Région</th><th>Statut</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        $stmt = $pdo_base->query("SELECT utilisateurs.*, regions.name AS region_name FROM utilisateurs LEFT JOIN regions ON utilisateurs.region_id = regions.id");
        
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['pseudo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
            echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
            echo "<td>" . ($row['status'] ? 'Actif' : 'Inactif') . "</td>";
            echo "<td>";

            // Formulaire pour activer/désactiver
            echo "<form action='admin.php' method='POST' class='d-inline'>";
            echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
            echo "<input type='hidden' name='new_status' value='" . ($row['status'] ? 0 : 1) . "'>";
            echo "<button type='submit' name='toggle_status' class='btn btn-sm " . ($row['status'] ? 'btn-danger' : 'btn-success') . "'>";
            echo $row['status'] ? 'Désactiver' : 'Activer';
            echo "</button>";
            echo "</form>";

            // Bouton pour modifier
            echo " <a href='admin.php?edit=" . $row['id'] . "' class='btn btn-sm btn-warning'>Modifier</a>";

            echo "</td>";
            echo "</tr>";
        }

        echo '</tbody>';
        echo '</table>';

        // Traitement de l'activation/désactivation d'un utilisateur
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
            $user_id = $_POST['user_id'];
            $new_status = $_POST['new_status'];
            $stmt = $pdo_base->prepare("UPDATE utilisateurs SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);

            echo '<div class="alert alert-info">Statut de l\'utilisateur mis à jour avec succès !</div>';
            echo '<script>window.location.href="admin.php";</script>';
            exit();
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-12 col-md-12 mb-4 mb-md-0">
                    <img src="https://i.imgur.com/kZxkmAy.png" alt="Logo GSB" style="height: 50px;">
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
