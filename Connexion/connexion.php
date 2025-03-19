<?php

header("Content-Type: application/json"); // Toujours renvoyer du JSON pour l'API
require_once '../config.php'; 
session_start();

$pdo_base = getPDO($array_config);

// Vérifie si la requête contient du JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$isAPIRequest = isset($input['pseudo']) && isset($input['password']); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($isAPIRequest) {
        // Requête API (depuis Android)
        $pseudo = trim($input['pseudo']);
        $password = trim($input['password']);
    } else {
        // Requête Web (depuis formulaire HTML)
        $pseudo = trim($_POST['pseudo']);
        $password = trim($_POST['password']);
    }

    if (empty($pseudo) || empty($password)) {
        if ($isAPIRequest) {
            echo json_encode(["status" => 400, "message" => "Veuillez remplir tous les champs."]);
            exit();
        } else {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            header("Location: connexionForm.php");
            exit();
        }
    }

    // Vérification dans la base de données
    $stmt = $pdo_base->prepare("SELECT * FROM utilisateurs WHERE pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        if ($user['status'] == 0) {
            if ($isAPIRequest) {
                echo json_encode(["status" => 403, "message" => "Compte inactif."]);
                exit();
            } else {
                $_SESSION['error'] = 'Votre compte est inactif.';
                header("Location: connexionForm.php");
                exit();
            }
        }

        $token = bin2hex(openssl_random_pseudo_bytes(32));
        $updateToken = $pdo_base->prepare("UPDATE utilisateurs SET token = :token WHERE id = :id");
        $updateToken->execute(['token' => $token, 'id' => $user['id']]);

        if ($isAPIRequest) {
            // Réponse pour l'application Android
            echo json_encode([
                "status" => 200,
                "message" => "Connexion réussie",
                "token" => $token,
                "user" => [
                    "id" => $user['id'],
                    "pseudo" => $user['pseudo'],
                    "email" => $user['email'],
                    "nom" => $user['nom'],
                    "prenom" => $user['prenom'],
                    "role" => $user['role'],
                    "region" => $user['region']
                ]
            ]);
            exit();
        } else {
            // Stocke les données utilisateur dans la session pour le Web
            $_SESSION['id_utilisateur'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['region'] = $user['region'];

            header("Location: ../espacemembre.php");
            exit();
        }
    } else {
        if ($isAPIRequest) {
            echo json_encode(["status" => 401, "message" => "Identifiant ou mot de passe incorrect."]);
            exit();
        } else {
            $_SESSION['error'] = 'Identifiant ou mot de passe incorrect.';
            header("Location: connexionForm.php");
            exit();
        }
    }
}

?>

