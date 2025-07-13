<?php
session_start();
require_once '../connexionpdo.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../connexion.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../espace_admin.php');
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->execute([$id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    echo "Véhicule non trouvé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $immatriculation = $_POST['immatriculation'];
    $type = $_POST['type'];
    $etat = $_POST['etat'];
   

    $updateStmt = $pdo->prepare("UPDATE vehicules SET marque = ?, modele = ?, immatriculation = ?, type = ?, etat = ? WHERE id = ?");
    $updateStmt->execute([$marque, $modele, $immatriculation, $type, $etat, $id]);

    header("Location: ../espace_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Véhicule</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Modifier Véhicule</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="marque" class="form-label">Marque</label>
            <input type="text" class="form-control" name="marque" value="<?php echo htmlspecialchars($vehicule['marque']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="modele" class="form-label">modele</label>
            <input type="text" class="form-control" name="modele" value="<?php echo htmlspecialchars($vehicule['modele']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="immatriculation" class="form-label">immatriculation</label>
            <input type="text" class="form-control" name="immatriculation" value="<?php echo htmlspecialchars($vehicule['immatriculation']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <input type="text" class="form-control" name="type" value="<?php echo htmlspecialchars($vehicule['type']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="etat" class="form-label">Etat</label>
            <input type="text" class="form-control" name="etat" value="<?php echo htmlspecialchars($vehicule['etat']); ?>">
        </div>
       
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="../espace_admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
