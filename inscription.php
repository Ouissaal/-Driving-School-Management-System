<?php
session_start();
require_once 'connexionpdo.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $formule = $_POST['formule'];
        
        // Définir le montant en fonction de la formule
        $montant_paiement = 0;
        switch($formule) {
            case 'standard':
                $montant_paiement = 2999;
                break;
            case 'accélérée':
                $montant_paiement = 4499;
                break;
            case 'intensive':
                $montant_paiement = 6999;
                break;
        }

        
        $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, adresse, email, telephone, password, formule, montant_paiement, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nom, $prenom, $adresse, $email, $telephone, $password, $formule, $montant_paiement]);

        header('Location: connexion.php?success=1');
        exit();
    } catch(PDOException $e) {
        $error = "Erreur lors de l'inscription : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Auto-École</title>
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./src/style.css">
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h1>Inscription</h1>
                <p>Créez votre compte pour commencer</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone">
                </div>

                

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="formule" class="form-label">Formule choisie *</label>
                    <select class="form-select" id="formule" name="formule" onchange="updateMontantTotal()" required>
                        <option value="standard">Standard (2999 DH)</option>
                        <option value="accélérée">Accélérée (4499 DH)</option>
                        <option value="intensive">Intensive (6999 DH)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="montant_paiement" class="form-label">Montant total *</label>
                    <input type="number" class="form-control" id="montant_paiement" name="montant_paiement" readonly required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Le mot de passe doit contenir au moins 8 caractères</small>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmer le mot de passe *</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-primary btn-register">S'inscrire</button>
            </form>

            <div class="text-center mt-3">
                <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
            </div>
        </div>
    </div>

    <script src="./src/bootstrap/js/bootstrap.bundle.js"></script>
    <script>
    function updateMontantTotal() {
        const formule = document.getElementById('formule').value;
        const prices = {
            'standard': 2999,
            'accélérée': 4499,
            'intensive': 6999
        };
        document.getElementById('montant_paiement').value = prices[formule];
    }
    // Set initial value when page loads
    window.onload = updateMontantTotal;

    // Ce script met automatiquement à jour le montant à payer en fonction de la formule sélectionnée, au chargement de la page et (si le HTML est bien configuré) à chaque changement de formule.
    </script>
</body>
</html> 