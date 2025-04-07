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
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => 401, "message" => "Token invalide"]);
    exit();
}

$id_utilisateur = $user['id'];

// Récupération des praticiens actifs
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['data']) && $_GET['data'] === 'praticiens') {
    try {
        // Correction : Utilisation de `actif` au lieu de `status`
        $stmt = $pdo_base->prepare("SELECT id, nom FROM praticiens WHERE actif = 1");
        $stmt->execute();
        $praticiens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Modifier le tableau pour inclure l'ID et le nom
        $praticiens_modifies = array_map(function($praticien) {
            return array(
                'id' => $praticien['id'],
                'nom' => $praticien['nom']
            );
        }, $praticiens);

        echo json_encode(["status" => 200, "data" => $praticiens_modifies]);
    } catch (Exception $e) {
        echo json_encode(["status" => 500, "message" => "Erreur serveur"]);
    }
    exit();
}

// Récupération des échantillons actifs
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['data']) && $_GET['data'] === 'echantillons') {
    try {
        $stmt = $pdo_base->prepare("SELECT id, nom FROM echantillons WHERE actif = 1");
        $stmt->execute();
        $echantillons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Modifier le tableau pour inclure l'ID et le nom
        $echantillons_modifies = array_map(function($echantillon) {
            return array(
                'id' => $echantillon['id'],
                'nom' => $echantillon['nom']
            );
        }, $echantillons);

        echo json_encode(["status" => 200, "data" => $echantillons_modifies]);
    } catch (Exception $e) {
        echo json_encode(["status" => 500, "message" => "Erreur serveur"]);
    }
    exit();
}

// Vérification de l'authentification
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET['comptes_rendus'])) {
    echo json_encode(["status" => 200, "message" => "Accès autorisé", "user" => $user]);
    exit();
}

// Récupération de tous les comptes rendus
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['comptes_rendus']) && $_GET['comptes_rendus'] === 'true') {
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
        foreach ($comptes_rendus as &$cr) {
            $ids = array_map('intval', explode(',', $cr['echantillons_distribues']));

            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt2 = $pdo_base->prepare("SELECT nom FROM echantillons WHERE id IN ($placeholders)");
                $stmt2->execute($ids);
                $noms = $stmt2->fetchAll(PDO::FETCH_COLUMN);
                $cr['echantillons_distribues'] = $noms;
            } else {
                $cr['echantillons_distribues'] = [];
            }
        }

        echo json_encode(["status" => 200, "comptes_rendus" => $comptes_rendus]);
    } else {
        echo json_encode(["status" => 404, "message" => "Aucun compte rendu trouvé"]);
    }
    exit();
}


// Récupération d'un compte rendu spécifique
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['comptes_rendus']) && isset($_GET['id_cr'])) {
    $id_cr = $_GET['id_cr'];

    if (!is_numeric($id_cr)) {
        echo json_encode(["status" => 400, "message" => "ID de compte rendu invalide"]);
        exit();
    }

    $stmt = $pdo_base->prepare("
        SELECT compte_rendu.id, compte_rendu.date_visite, compte_rendu.medecin, compte_rendu.commentaires, 
            compte_rendu.piece_jointe, compte_rendu.echantillons_distribues, praticiens.nom AS praticien_nom
        FROM compte_rendu
        JOIN praticiens ON compte_rendu.praticien_id = praticiens.id
        WHERE compte_rendu.id = ? AND compte_rendu.id_utilisateur = ?
    ");
    $stmt->execute([$id_cr, $id_utilisateur]);

    $compte_rendu = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($compte_rendu) {
        echo json_encode(["status" => 200, "compte_rendu" => $compte_rendu]);
    } else {
        echo json_encode(["status" => 404, "message" => "Compte rendu non trouvé ou accès non autorisé"]);
    }
    exit();
}

// Suppression d'un compte rendu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET['comptes_rendus']) && isset($_GET['delete']) && isset($_GET['id_cr'])) {
    $id_cr = $_GET['id_cr'];

    if (!is_numeric($id_cr)) {
        echo json_encode(["status" => 400, "message" => "ID de compte rendu invalide"]);
        exit();
    }

    $stmt = $pdo_base->prepare("SELECT id FROM compte_rendu WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$id_cr, $id_utilisateur]);
    $compte_rendu = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($compte_rendu) {
        $stmt = $pdo_base->prepare("DELETE FROM compte_rendu WHERE id = ?");
        $stmt->execute([$id_cr]);

        echo json_encode(["status" => 200, "message" => "Compte rendu supprimé"]);
    } else {
        echo json_encode(["status" => 404, "message" => "Compte rendu non trouvé ou accès non autorisé"]);
    }
    exit();
}

// Création d'un compte rendu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET['comptes_rendus']) && $_GET['comptes_rendus'] === 'true') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["status" => 400, "message" => "Données JSON invalides"]);
        exit();
    }

    $date_visite = $data['date_visite'];
    $praticien_id = $data['praticien_id'];
    $echantillons = $data['echantillons'];
    $commentaires = isset($data['commentaires']) ? $data['commentaires'] : null;

    // Validation des données (à compléter selon vos besoins)
    if (empty($date_visite) || empty($praticien_id) || empty($echantillons)) {
        echo json_encode(["status" => 400, "message" => "Veuillez remplir tous les champs obligatoires"]);
        exit();
    }

    // Correction : Utilisation de `praticien_id` (correspond à la BDD)
    $stmt = $pdo_base->prepare("INSERT INTO compte_rendu (date_visite, praticien_id, echantillons_distribues, commentaires, id_utilisateur) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$date_visite, $praticien_id, $echantillons, $commentaires, $id_utilisateur]);

    echo json_encode(["status" => 200, "message" => "Compte rendu créé avec succès"]);
    exit();
}

echo json_encode(["status" => 405, "message" => "Méthode non autorisée"]);
exit();
?>
