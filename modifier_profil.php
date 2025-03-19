<?php
session_start();

require_once 'config.php'; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexionForm.php");
    exit();
}

$pdo_base = getPDO($array_config);
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations actuelles de l'utilisateur
$stmt = $pdo_base->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id_utilisateur]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];

    // Vérification que le pseudo est unique
    $stmt = $pdo_base->prepare("SELECT COUNT(*) FROM utilisateurs WHERE pseudo = ? AND id != ?");
    $stmt->execute([$pseudo, $id_utilisateur]);
    $pseudo_exists = $stmt->fetchColumn();

    if ($pseudo_exists) {
        $message_error = "Le pseudo est déjà utilisé par un autre utilisateur.";
    } else {
        if (!empty($new_password)) {
            // Vérification du mot de passe actuel
            if (!password_verify($current_password, $user['mot_de_passe'])) {
                $message_error = "Le mot de passe actuel est incorrect.";
            } else {
                // Hachage du nouveau mot de passe et mise à jour
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo_base->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ?, mot_de_passe = ? WHERE id = ?");
                $stmt->execute([$pseudo, $nom, $prenom, $hashed_password, $id_utilisateur]);
                $message_success = "Profil et mot de passe mis à jour avec succès.";
            }
        } else {
            // Mise à jour sans modifier le mot de passe
            $stmt = $pdo_base->prepare("UPDATE utilisateurs SET pseudo = ?, nom = ?, prenom = ? WHERE id = ?");
            $stmt->execute([$pseudo, $nom, $prenom, $id_utilisateur]);
            $message_success = "Profil mis à jour avec succès.";
        }

        // Rafraîchir les informations de l'utilisateur
        $stmt = $pdo_base->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id_utilisateur]);
        $user = $stmt->fetch();
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
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Lien vers le fichier CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Modifier Profil - GSB</title>
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
                <li class="nav-item"><a class="nav-link mr-4" href="espacemembre.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link mr-4" href="cr.php">CR</a></li>
                <li class="nav-item"><a class="nav-link mr-4" href="stats.php">Stats</a></li>
                <li class="nav-item"><a class="nav-link mr-4" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4 text-primary">Modifier Votre Profil</h1>

        <?php if (isset($message_success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message_success); ?></div>
        <?php endif; ?>

        <?php if (isset($message_error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message_error); ?></div>
        <?php endif; ?>

        <form action="modifier_profil.php" method="POST">
            <div class="form-group">
                <label for="pseudo">Pseudo :</label>
                <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="current_password">Mot de passe actuel :</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
        </form>
    </div>

    <!-- Footer -->
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
