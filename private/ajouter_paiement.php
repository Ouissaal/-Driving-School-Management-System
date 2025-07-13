<?php
session_start();
require_once '../connexionpdo.php';

// Redirection si non connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header('Location: connexion.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = $_POST['montant'];
    $moyen = $_POST['moyen_paiement'];

    $stmt = $pdo->prepare("INSERT INTO paiements (client_id, montant, date_paiement, moyen_paiement) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$_SESSION['user_id'], $montant, $moyen]);

    $_SESSION['success'] = "Paiement ajouté avec succès.";
    header("Location: ../espace_client.php"); 
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un paiement</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>

<body class="container mt-5">
    <h2>Ajouter un paiement</h2>
    <form method="post">
        <div class="mb-3">
            <label for="montant" class="form-label">Montant</label>
            <input type="number" name="montant" id="montant" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="moyen_paiement" class="form-label">Moyen de paiement</label>
            <select name="moyen_paiement" id="moyen_paiement" class="form-control" required>
                <option value="Espèces">Espèces</option>
                <option value="Carte bancaire">Carte bancaire</option>
                <option value="Chèque">Chèque</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Valider</button>
         <a href="../espace_admin.php" class="btn btn-secondary">Retour</a>
    </form>
</body>
</html>
