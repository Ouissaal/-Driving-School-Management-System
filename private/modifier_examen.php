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
$stmt = $pdo->prepare("SELECT * FROM examens WHERE id = ?");
$stmt->execute([$id]);
$examen = $stmt->fetch();

if (!$examen) {
    echo "Examen non trouvé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    $date = $_POST['date'];
    $lieu = $_POST['lieu'];
    $resultat = $_POST['resultat'];
    $type = $_POST['type'];

   

    $updateStmt = $pdo->prepare("UPDATE examens SET client_id = ?, type = ?, date = ?, lieu = ?,resultat =? WHERE id = ?");
    $updateStmt->execute([$client_name, $type, $date, $lieu, $resultat, $id]);

    header("Location: ../espace_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Examen</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Modifier Examen</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="client_name" class="form-label">Client</label>
            <input type="text" class="form-control" name="client_name" value="<?php echo htmlspecialchars($examen['client_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($examen['date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="lieu" class="form-label">Lieu</label>
            <input type="text" class="form-control" name="lieu" value="<?php echo htmlspecialchars($examen['lieu']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <input type="text" class="form-control" name="type" value="<?php echo htmlspecialchars($examen['type']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="resultat" class="form-label">Resultat</label>
            <input type="text" class="form-control" name="resultat" value="<?php echo htmlspecialchars($examen['resultat']); ?>">
        </div>
       
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="../espace_admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
