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
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    echo "Client non trouvé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $formule = $_POST['formule'];
    $montant = $_POST['montant'];

    $updateStmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, telephone = ?, formule = ?, montant_paiement = ? WHERE id = ?");
    $updateStmt->execute([$nom, $prenom, $email, $telephone, $formule, $montant, $id]);

    header("Location: ../espace_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Client</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
</head>
<body style="background-color: #a9a093;">
<div class="container mt-5">
    <h2>Modifier Client</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($client['nom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($client['prenom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="<?php echo htmlspecialchars($client['telephone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="formule" class="form-label">Formule</label>
            <input type="text" class="form-control" name="formule" value="<?php echo htmlspecialchars($client['formule']); ?>">
        </div>
        <div class="mb-3">
            <label for="montant" class="form-label">Montant</label>
            <input type="number" class="form-control" name="montant" value="<?php echo htmlspecialchars($client['montant_paiement']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="../espace_admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
