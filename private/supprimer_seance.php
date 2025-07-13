<?php
require_once('../connexionpdo.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM seances WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ../espace_admin.php");
    exit;
} else {
    echo "ID de la séance non spécifié.";
}
?>
