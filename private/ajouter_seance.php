<?php
session_start();
require_once '../connexionpdo.php';

$message = '';
$error = '';

//moniteurs
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM moniteurs WHERE statut = 'actif'");
    $stmt->execute();
    $moniteurs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des moniteurs.";
}

// véhicules
try {
    $stmt = $pdo->prepare("SELECT id, marque, modele FROM vehicules");
    $stmt->execute();
    $vehicules = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des véhicules.";
}

//clients
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM clients");
    $stmt->execute();
    $clients = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des clients.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $duree = $_POST['duree'] ?? '';
    $lieu = $_POST['lieu'] ?? '';
    $moniteur_id = $_POST['moniteur_id'] ?? null;
    $vehicule_id = $_POST['vehicule_id'] ?? null;
    $client_id = $_POST['client_id'] ?? null;

    if (!$date || !$duree || !$moniteur_id || !$client_id) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        if (strlen($duree) === 5) $duree .= ':00'; // HH:MM -> HH:MM:SS

        try {
            $stmt = $pdo->prepare("SELECT date, duree FROM seances  WHERE moniteur_id = ? AND date = ?");
            $stmt->execute([$moniteur_id, $date]);
            $existingSeances = $stmt->fetchAll();

            $newStart = strtotime($date);
            $newEnd = $newStart + strtotime($duree) - strtotime('TODAY');

            $conflit = false;
            foreach ($existingSeances as $seance) {
                $startExist = strtotime($seance['date']);
                $endExist = $startExist + strtotime($seance['duree']) - strtotime('TODAY');

                if (($newStart < $endExist) && ($newEnd > $startExist)) {
                    $conflit = true;
                    break;
                }
            }

            if ($conflit) {
                $error = "Conflit détecté : le moniteur a déjà une séance à ce moment.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO seances (date, duree, lieu, moniteur_id, vehicule_id, client_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $res = $stmt->execute([
                    $date,
                    $duree,
                    $lieu,
                    $moniteur_id,
                    $vehicule_id ?: null,
                    $client_id
                ]);

                if ($res) {
                    $message = "Séance ajoutée avec succès !";
                    $_POST = [];
                    header("location:../espace_admin.php");
                    exit();
                } else {
                    $error = "Erreur lors de l'ajout de la séance.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur SQL : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une séance</title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body >

    <h3 class="text-center mt-4">Ajouter une nouvelle séance</h3>

    <?php if ($error): ?>
        <div style="color: red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div style="color: green;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="col-6 mx-auto card p-4" >
        <label for="date" class="form-label mt-3">Date et heure :</label>
        <input type="datetime-local" name="date" id="date" class="form-control" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" required>

        <label for="duree" class="form-label mt-3">Durée (HH:MM) :</label>
        <input type="text" name="duree" id="duree" class="form-control" placeholder="ex: 02:00" value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>" required>

        <label for="lieu" class="form-label mt-3">Lieu :</label>
        <input type="text" name="lieu" id="lieu" class="form-control"  value="<?= htmlspecialchars($_POST['lieu'] ?? '') ?>">

        <label for="moniteur_id" class="form-label mt-3">Moniteur :</label>
        <select name="moniteur_id" id="moniteur_id"  class="form-select"required>
            <option value="">-- Choisir un moniteur --</option>
            <?php foreach ($moniteurs as $moniteur): ?>
                <option value="<?= $moniteur['id'] ?>" <?= (isset($_POST['moniteur_id']) && $_POST['moniteur_id'] == $moniteur['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($moniteur['prenom'] . ' ' . $moniteur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="vehicule_id" class="form-label mt-3">Véhicule (optionnel) :</label>
        <select name="vehicule_id" id="vehicule_id" class="form-select">
            <option value="">-- Choisir un véhicule --</option>
            <?php foreach ($vehicules as $vehicule): ?>
                <option value="<?= $vehicule['id'] ?>" <?= (isset($_POST['vehicule_id']) && $_POST['vehicule_id'] == $vehicule['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="client_id" class="form-label mt-3">Client :</label>
        <select name="client_id" id="client_id"  class="form-select" required>
            <option value="">-- Choisir un client --</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>" <?= (isset($_POST['client_id']) && $_POST['client_id'] == $client['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <div class='mt-2'>
        <button type="submit" class="btn btn-success">Ajouter la séance</button>
        <a href="../espace_admin.php" class="btn btn-secondary">Retour</a>
    </div>
    </form>
</body>
</html>
