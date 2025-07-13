<?php
session_start();
require_once '../connexionpdo.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $type = $_POST['type'];
    $date = $_POST['date'];
    $lieu = $_POST['lieu'];
    $resultat = $_POST['resultat'];
    $tentative = $_POST['tentative'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO examens (client_id, type, date, lieu,resultat,tentative,notes)VALUES(?,?,?,?,?,?,?)");
        $stmt->execute([$client_id, $type, $date, $lieu, $resultat, $tentative, $notes]);

    header("Location: ../espace_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Examen</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>

<body>
<div class="container mt-5">
    <h2>Ajouter un Examen</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="client_id" class="form-label">ID Client</label>
            <input type="number" class="form-control" name="client_id" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type d'examen</label>
            <select class="form-control" name="type" required>
                <option value="code">Code</option>
                <option value="conduite">Conduite</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" required>
        </div>
        <div class="mb-3">
            <label for="lieu" class="form-label">Lieu</label>
            <input type="text" class="form-control" name="lieu">
        </div>
        <div class="mb-3">
            <label for="resultat" class="form-label">Résultat</label>
            <select class="form-control" name="resultat" required>
                <option value="admis">Admis</option>
                <option value="non_admis">Non admis</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tentative" class="form-label">Tentative</label>
            <input type="number" class="form-control" name="tentative" value="1" min="1" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes (facultatif)</label>
            <textarea class="form-control" name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-success w-50">Ajouter</button>
         <a href="../espace_admin.php" class="btn btn-secondary w-50">Retour</a>
    </form>
</div>
</body>
</html>


