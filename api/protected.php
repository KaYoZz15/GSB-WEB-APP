<?php

require_once '../config.php';
header("Content-Type: application/json");

$pdo_base = getPDO($array_config);

function extractToken() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return preg_replace('/^Bearer\s/', '', $_SERVER['HTTP_AUTHORIZATION']);
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return preg_replace('/^Bearer\s/', '', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    }
    return null;
}

$token = extractToken();
if (!$token) {
    echo json_encode(["status" => 400, "message" => "Aucun token fourni"]);
    exit();
}

// Vérification du token et récupération de l'utilisateur
$stmt = $pdo_base->prepare("SELECT id, pseudo FROM utilisateurs WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(["status" => 401, "message" => "Token invalide"]);
    exit();
}

$id_utilisateur = $user['id'];

// Vérifier si l'utilisateur demande uniquement l'authentification
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET['comptes_rendus'])) {
    echo json_encode(["status" => 200, "message" => "Accès autorisé", "user" => $user]);
    exit();
}

// Si l'utilisateur demande tous les comptes rendus
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['comptes_rendus']) && $_GET['comptes_rendus'] == 'true') {
    $stmt = $pdo_base->prepare("
        SELECT compte_rendu.id, compte_rendu.date_visite, compte_rendu.medecin, compte_rendu.commentaires, 
               compte_rendu.piece_jointe, compte_rendu.echantillons_distribues, praticiens.nom AS praticien_nom
        FROM compte_rendu
        JOIN praticiens ON compte_rendu.praticien_id = praticiens.id
        WHERE compte_rendu.id_utilisateur = ?
    ");
    $stmt->execute([$id_utilisateur]);

    $comptes_rendus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($comptes_rendus) {
        echo json_encode(["status" => 200, "comptes_rendus" => $comptes_rendus]);
    } else {
        echo json_encode(["status" => 404, "message" => "Aucun compte rendu trouvé"]);
    }
    exit();
}

// Si l'utilisateur demande un seul compte rendu (avec ID)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['comptes_rendus']) && isset($_GET['id_cr'])) {
    $id_cr = $_GET['id_cr'];  // ID du compte rendu demandé

    $stmt = $pdo_base->prepare("
        SELECT compte_rendu.id, compte_rendu.date_visite, compte_rendu.medecin, compte_rendu.commentaires, 
               compte_rendu.piece_jointe, compte_rendu.echantillons_distribues, praticiens.nom AS praticien_nom
        FROM compte_rendu
        JOIN praticiens ON compte_rendu.praticien_id = praticiens.id
        WHERE compte_rendu.id = ? AND compte_rendu.id_utilisateur = ?
    ");
    $stmt->execute([$id_cr, $id_utilisateur]);  // Vérifier que l'utilisateur a accès à ce CR

    $compte_rendu = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($compte_rendu) {
        echo json_encode(["status" => 200, "compte_rendu" => $compte_rendu]);
    } else {
        echo json_encode(["status" => 404, "message" => "Compte rendu non trouvé ou accès non autorisé"]);
    }
    exit();
}

echo json_encode(["status" => 405, "message" => "Méthode non autorisée"]);
exit();
