<?php

header('Content-Type: application/json');

// Récupérer le token envoyé dans les en-têtes HTTP
$token = null;
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = trim(str_replace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
}

// Vérifier si un token est présent
if ($token === null) {
    echo json_encode(['error' => 'Token manquant']);
    exit();
}

require_once 'config.php'; // Connexion à la base de données
$pdo_base = getPDO($array_config);

// Vérifier la validité du token (par exemple en le comparant à la base de données ou en vérifiant sa signature)
$stmt = $pdo_base->prepare("SELECT id_utilisateur FROM utilisateurs WHERE token = ?");
$stmt->execute([$token]);

$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['error' => 'Token invalide ou expiré']);
    exit();
}

// Récupérer l'id de l'utilisateur à partir du token
$id_utilisateur = $user['id_utilisateur'];

// Requête pour récupérer les comptes rendus de l'utilisateur connecté
$stmt = $pdo_base->prepare("SELECT compte_rendu.*, praticiens.nom AS praticien_nom
                            FROM compte_rendu 
                            JOIN praticiens ON compte_rendu.praticien_id = praticiens.id
                            WHERE compte_rendu.id_utilisateur = ?");
$stmt->execute([$id_utilisateur]);

// Récupérer les résultats
$comptes_rendus = [];
while ($row = $stmt->fetch()) {
    $comptes_rendus[] = [
        'id' => $row['id'],
        'date_visite' => $row['date_visite'],
        'praticien_nom' => $row['praticien_nom']
    ];
}

// Retourner les comptes rendus en format JSON
echo json_encode($comptes_rendus);

?>
