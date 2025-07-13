<?php
session_start();
require_once '../connexionpdo.php'; 


if (!isset($_GET['id'])) {
    die("ID de séance invalide.");
}

$id = (int)$_GET['id'];
$message = '';
$error = '';

//moniteurs
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM moniteurs WHERE statut = 'actif'");
    $stmt->execute();
    $moniteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des moniteurs.";
}

//véhicules
try {
    $stmt = $pdo->prepare("SELECT id, marque, modele FROM vehicules");
    $stmt->execute();
    $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des véhicules.";
}

//clients
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom FROM clients");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des clients.";
}

//séance
try {
    $stmt = $pdo->prepare("SELECT * FROM seances WHERE id = ?");
    $stmt->execute([$id]);
    $seance = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$seance) {
        die("Séance introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
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
        if (strlen($duree) === 5) $duree .= ':00'; //uniformiser le format de l'heure pour qu'il soit toujours au format heure:minute:seconde (hh:mm:ss), même si l'utilisateur a saisi uniquement heure:minute.

        try {
            $stmt = $pdo->prepare("UPDATE seances SET date = ?, duree = ?, lieu = ?, moniteur_id = ?, vehicule_id = ?, client_id = ? WHERE id = ?");
            $res = $stmt->execute([$date,$duree,$lieu,$moniteur_id, $vehicule_id ?: null, $client_id, $id ]);


            if ($res) {
                $message = "Séance modifiée avec succès !";
                header('location :../espace_admin.php');
                exit();
            } else {
                $error = "Erreur lors de la modification de la séance.";
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
    <title>Modifier la séance</title>
    <link href="../src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../src/style.css">
</head>
<body>

<div class="container col-6 mx-auto card p-2 mt-5">
    <h1>Modifier la séance</h1>

    <?php if ($error): ?>
        <div style="color: red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div style="color: green;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="date" class="form-label mt-3 ">Date et heure :</label>
        <input type="datetime-local" class="form-control" name="date" id="date" value="<?= htmlspecialchars($_POST['date'] ?? $seance['date']) ?>" required>

        <label for="duree" class="form-label mt-3" >Durée (HH:MM) :</label>
        <input type="text" name="duree" class="form-control" id="duree" placeholder="ex: 02:00" value="<?= htmlspecialchars($_POST['duree'] ?? substr($seance['duree'], 0, 5)) ?>" required>

        <label for="lieu" class="form-label mt-3">Lieu :</label>
        <input type="text" name="lieu" class="form-control" id="lieu" value="<?= htmlspecialchars($_POST['lieu'] ?? $seance['lieu']) ?>">

        <label for="moniteur_id" class="form-label mt-3" >Moniteur :</label>
        <select name="moniteur_id" id="moniteur_id" class="form-select" required>
            <option value="">-- Choisir un moniteur --</option>
            <?php foreach ($moniteurs as $moniteur): ?>
                <option value="<?= $moniteur['id'] ?>" <?= ((isset($_POST['moniteur_id']) && $_POST['moniteur_id'] == $moniteur['id']) || (!isset($_POST['moniteur_id']) && $seance['moniteur_id'] == $moniteur['id'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($moniteur['prenom'] . ' ' . $moniteur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="vehicule_id" class="form-label mt-3">Véhicule (optionnel) :</label>
        <select name="vehicule_id" id="vehicule_id" class="form-select">
            <option value="">-- Choisir un véhicule --</option>
            <?php foreach ($vehicules as $vehicule): ?>
                <option value="<?= $vehicule['id'] ?>" <?= ((isset($_POST['vehicule_id']) && $_POST['vehicule_id'] == $vehicule['id']) || (!isset($_POST['vehicule_id']) && $seance['vehicule_id'] == $vehicule['id'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="client_id" class="form-label mt-3">Client :</label>
        <select name="client_id" id="client_id" class="form-select" required>
            <option value="">-- Choisir un client --</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>" <?= ((isset($_POST['client_id']) && $_POST['client_id'] == $client['id']) || (!isset($_POST['client_id']) && $seance['client_id'] == $client['id'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <div class="mt-3">
        <button type="submit" class="btn btn-success ">Modifier la séance</button>
        <a href="../espace_admin.php" class="btn btn-secondary">retour</a>
    </div>
    </form>
    </div>
</body>
</html>
