<?php
session_start();
require_once 'connexionpdo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $type = $_POST['type'];

    try {
        switch ($type) {
            case 'client':
                $sql = "SELECT * FROM clients WHERE email = :email";
                $redirect = 'espace_client.php';
                break;
            case 'moniteur':
                $sql = "SELECT * FROM moniteurs WHERE email = :email";
                $redirect = 'espace_moniteur.php';
                break;
            case 'admin':
                $sql = "SELECT * FROM administrateurs WHERE email = :email";
                $redirect = 'espace_admin.php';
                break;
            default:
                throw new Exception("Type d'utilisateur invalide");
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && $password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $type;
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            
            header("Location: $redirect");
            exit();
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    } catch (Exception $e) {
        $error = "Erreur de connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./src/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Connexion</h1>
                <p>Connectez-vous à votre espace</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="type" class="form-label">Type de compte</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="client">Client</option>
                        <option value="moniteur">Moniteur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-login">Se connecter</button>
            </form>

            <div class="text-center mt-3">
                <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
                <p class="text-muted">Mot de passe oublié ?</p>
            </div>
        </div>
    </div>

    <script src="./src/bootstrap/js/bootstrap.min.js"></script>
</body>
</html> 