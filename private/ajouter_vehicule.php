<?php
session_start();
require_once '../connexionpdo.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../connexion.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $type = $_POST['type'];
    $immatriculation = $_POST['immatriculation'];
    $etat = $_POST['etat'];
    $kilometrage = $_POST['kilometrage'];
    $date_achat = $_POST['date_achat'];

    try {
        // Vérification si l'immatriculation existe déjà
        $stmt = $pdo->prepare("SELECT id FROM vehicules WHERE immatriculation = :immatriculation");
        $stmt->execute(['immatriculation' => $immatriculation]);
        if ($stmt->fetch()) {
            throw new Exception("Cette immatriculation est déjà utilisée");
        }

        
        $sql = "INSERT INTO vehicules (marque, modele, type, immatriculation, etat, kilometrage, date_achat, statut) 
                VALUES (:marque, :modele, :type, :immatriculation, :etat, :kilometrage, :date_achat, 'disponible')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'marque' => $marque,
            'modele' => $modele,
            'type' => $type,
            'immatriculation' => $immatriculation,
            'etat' => $etat,
            'kilometrage' => $kilometrage,
            'date_achat' => $date_achat
        ]);

        $success = "Véhicule ajouté avec succès!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un véhicule</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
    

</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Ajouter un véhicule</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marque" class="form-label">Marque</label>
                                    <input type="text" class="form-control" id="marque" name="marque" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="modele" class="form-label">Modèle</label>
                                    <input type="text" class="form-control" id="modele" name="modele" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="citadine">Citadine</option>
                                    <option value="berline">Berline</option>
                                    <option value="suv">SUV</option>
                                    <option value="utilitaire">Utilitaire</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="immatriculation" class="form-label">Immatriculation</label>
                                <input type="text" class="form-control" id="immatriculation" name="immatriculation" required>
                            </div>

                            <div class="mb-3">
                                <label for="etat" class="form-label">État</label>
                                <select class="form-select" id="etat" name="etat" required>
                                    <option value="neuf">Neuf</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="à_réparer">À réparer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kilometrage" class="form-label">Kilométrage</label>
                                <input type="number" class="form-control" id="kilometrage" name="kilometrage" required>
                            </div>

                            <div class="mb-3">
                                <label for="date_achat" class="form-label">Date d'achat</label>
                                <input type="date" class="form-control" id="date_achat" name="date_achat" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Ajouter le véhicule</button>
                                <a href="../espace_admin.php" class="btn btn-secondary">Retour</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 