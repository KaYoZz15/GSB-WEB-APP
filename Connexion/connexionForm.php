<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" />
    <title>Connexion à GSB-VisitTrack</title>
    <!-- Ajouter le CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .login-container img {
            width: 150px;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"], input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box; /* Ajout pour aligner */
        }

        input[type="submit"] {
            background-color: #38B6FF;
            border: none;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #38B6FF;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo -->
        <img src="../assets/images/GSB-logo.png" alt="Logo GSB">
        
        <!-- Formulaire de connexion -->
        <h2>Connexion à GSB-VisitTrack</h2>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger" role="alert">';
            echo htmlspecialchars($_SESSION['error']);
            echo '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="connexion.php" method="POST">
            <label for="pseudo">Identifiant :</label>
            <input type="text" id="pseudo" name="pseudo" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Se connecter">
        </form>
    </div>

    <!-- Ajouter le JS de Bootstrap et jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
