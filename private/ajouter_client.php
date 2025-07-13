<?php
session_start();
require_once '../connexionpdo.php';

// verification if not admin --> to connexion.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../connexion.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $password = $_POST['password'];
    $formule = $_POST['formule'];
    $montant_paiement = $_POST['montant_paiement'];

    try {
    
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception("Cet email est déjà utilisé");
        }


        $sql = "INSERT INTO clients (nom, prenom, email, telephone, adresse, password, formule, montant_paiement,date_inscription) 
                VALUES (:nom, :prenom, :email, :telephone, :adresse, :password, :formule, :montant_paiement,NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'password' => $password,
            'formule' => $formule,
            'montant_paiement'=>$montant_paiement
        ]);

        $success = "Client ajouté avec succès!";
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
    <title>Ajouter un client </title>
    <link rel="stylesheet" href="../src/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../src/style.css">

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Ajouter un client</h3>
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
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" required>
                            </div>

                    

                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="formule" class="form-label">Formule</label>
                                <select class="form-select" id="formule" name="formule" onchange="updateMontantTotal()" required>
                                    <option value="standard">Standard (2999 DH)</option>
                                    <option value="accélérée">Accélérée (4499 DH)</option>
                                    <option value="intensive">Intensive (6999 DH)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="montant_paiement" class="form-label">Montant paiement</label>
                                <input type="number" class="form-control" id="montant_paiement" name="montant_paiement" readonly required>
                            </div>


                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Ajouter le client</button>
                                <a href="../espace_admin.php" class="btn btn-secondary">Retour</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    </script>
</body>
</html> 