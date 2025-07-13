<?php


session_start();
require_once '../connexionpdo.php';

if (!isset($_GET['id']) ) {
    echo "ID invalide.";
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: ../espace_admin.php?message=Client supprimé avec succès");
    exit;
} catch (PDOException $e) {
    echo "Erreur lors de la suppression : " . $e->getMessage();
}
?>
