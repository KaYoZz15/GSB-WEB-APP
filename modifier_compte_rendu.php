<?php
session_start();

// Verify if the user is logged in
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexionForm.php");
    exit();
}

require_once 'config.php'; // Database connection
$pdo_base = getPDO($array_config);

// Verify that the report ID is provided
if (!isset($_GET['id'])) {
    echo "Aucun compte rendu spécifié.";
    exit();
}

$id_compte_rendu = $_GET['id'];

// Fetch report details
$stmt = $pdo_base->prepare("SELECT * FROM compte_rendu WHERE id = ?");
$stmt->execute([$id_compte_rendu]);
$compte_rendu = $stmt->fetch();

if (!$compte_rendu) {
    echo "Compte rendu non trouvé.";
    exit();
}

// Fetch all samples for display
$echantillons_stmt = $pdo_base->query("SELECT * FROM echantillons");
$echantillons = $echantillons_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch practitioners for dropdown selection
$praticiens_stmt = $pdo_base->query("SELECT id, nom, specialite FROM praticiens WHERE actif = 1");
$praticiens = $praticiens_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process report update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cr'])) {
    $date_visite = $_POST['date_visite'];
    $praticien_id = $_POST['praticien_id'];
    $commentaires = $_POST['commentaires'];
    $echantillons_distribues = !empty($_POST['echantillons']) ? implode(',', $_POST['echantillons']) : null;

    // Handle file upload
    $piece_jointe = $compte_rendu['piece_jointe'];
    if (!empty($_FILES['piece_jointe']['name'])) {
        $piece_jointe = 'uploads/' . basename($_FILES['piece_jointe']['name']);
        move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $piece_jointe);
    }

    // Update the report
    $stmt = $pdo_base->prepare("UPDATE compte_rendu SET date_visite = ?, praticien_id = ?, commentaires = ?, piece_jointe = ?, echantillons_distribues = ? WHERE id = ?");
    $stmt->execute([$date_visite, $praticien_id, $commentaires, $piece_jointe, $echantillons_distribues, $id_compte_rendu]);

    echo '<div class="alert alert-success">Compte rendu mis à jour avec succès !</div>';
    echo '<script>window.location.href="cr.php";</script>';
    exit();
}

// Extract currently distributed samples
$current_echantillons = explode(',', $compte_rendu['echantillons_distribues']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Compte Rendu - GSB</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Modifier Compte Rendu</h2>
    <form action="modifier_compte_rendu.php?id=<?php echo htmlspecialchars($id_compte_rendu); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="date_visite">Date de visite :</label>
            <input type="date" class="form-control" id="date_visite" name="date_visite" value="<?php echo htmlspecialchars($compte_rendu['date_visite']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="praticien_id">Praticien :</label>
            <select class="form-control" id="praticien_id" name="praticien_id" required>
                <option value="">Sélectionnez un praticien</option>
                <?php foreach ($praticiens as $praticien): ?>
                    <option value="<?php echo htmlspecialchars($praticien['id']); ?>" <?php echo ($praticien['id'] == $compte_rendu['praticien_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($praticien['nom'] . " - " . $praticien['specialite']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="echantillons">Échantillons distribués :</label>
            <div>
                <?php foreach ($echantillons as $echantillon): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="echantillons[]" value="<?php echo htmlspecialchars($echantillon['id']); ?>" id="echantillon_<?php echo htmlspecialchars($echantillon['id']); ?>" <?php echo in_array($echantillon['id'], $current_echantillons) ? 'checked' : ''; ?> <?php echo $echantillon['actif'] == 1 ? '' : 'disabled'; ?>>
                        <label class="form-check-label <?php echo $echantillon['actif'] == 1 ? '' : 'text-muted'; ?>" for="echantillon_<?php echo htmlspecialchars($echantillon['id']); ?>">
                            <?php echo htmlspecialchars($echantillon['nom']); ?> <?php echo $echantillon['actif'] == 1 ? '' : '(Indisponible)'; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="piece_jointe">Pièce jointe :</label>
            <input type="file" class="form-control" id="piece_jointe" name="piece_jointe">
            <?php if ($compte_rendu['piece_jointe']): ?>
                <small>Fichier actuel : <a href="<?php echo htmlspecialchars($compte_rendu['piece_jointe']); ?>" target="_blank">Voir la pièce jointe</a></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="commentaires">Commentaires :</label>
            <textarea class="form-control" id="commentaires" name="commentaires" rows="3"><?php echo htmlspecialchars($compte_rendu['commentaires']); ?></textarea>
        </div>
        
        <button type="submit" name="update_cr" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
