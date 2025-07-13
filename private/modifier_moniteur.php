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
$stmt = $pdo->prepare("SELECT * FROM moniteurs WHERE id = ?");
$stmt->execute([$id]);
$moniteur = $stmt->fetch();

if (!$moniteur) {
    echo "Moniteur non trouvé.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $statut = $_POST['statut'];
   

    $updateStmt = $pdo->prepare("UPDATE moniteurs SET nom = ?, prenom = ?, email = ?, telephone = ?, statut = ? WHERE id = ?");
    $updateStmt->execute([$nom, $prenom, $email, $telephone, $statut, $id]);

    header("Location: ../espace_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Moniteur</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Modifier Moniteur </h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($moniteur['nom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($moniteur['prenom']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($moniteur['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="<?php echo htmlspecialchars($moniteur['telephone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="statut" class="form-label">statut</label>
            <input type="text" class="form-control" name="statut" value="<?php echo htmlspecialchars($moniteur['statut']); ?>">
        </div>
       
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="../espace_admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
