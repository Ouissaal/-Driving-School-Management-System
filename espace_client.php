<?php
session_start();
require_once 'connexionpdo.php';

function handleError($message, $redirect = false) {
    $_SESSION['error'] = $message;
    if ($redirect) {
        header('Location: ' . $redirect);
        exit();
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header('Location: connexion.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$client = $stmt->fetch();

if (!$client) {
    handleError("Client non trouvé.", "connexion.php");
}

// Récupérer les paiements du client
$stmt = $pdo->prepare("SELECT * FROM paiements WHERE client_id = ? ORDER BY date_paiement DESC");
$stmt->execute([$_SESSION['user_id']]);
$paiements = $stmt->fetchAll();

// Calcul du total payé
$total_paye = 0;
foreach ($paiements as $paiement) {
    $total_paye += $paiement['montant'];
}

// Calcul du montant restant
$montant_restant = $client['montant_paiement'] - $total_paye;

// Récupérer les séances du client uniquement
$stmt = $pdo->prepare("
    SELECT s.*, m.nom AS moniteur_nom, m.prenom AS moniteur_prenom, v.marque, v.modele
    FROM seances s
    INNER JOIN moniteurs m ON s.moniteur_id = m.id
    INNER JOIN vehicules v ON s.vehicule_id = v.id
    WHERE s.client_id = ?
    ORDER BY s.date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$seances = $stmt->fetchAll();

// Récupérer les examens du client
$stmt = $pdo->prepare("SELECT * FROM examens WHERE client_id = ? ORDER BY date DESC");
$stmt->execute([$_SESSION['user_id']]);
$examens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Client </title>
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="./src/style.css" />
</head>
<body>
<div class="container-fluid">
    <div class="row">
      
        <div class="col-md-3 col-lg-2 sidebar">
            <h3 class="mb-4">Espace Client</h3>
            <div class="nav flex-column">
                <a class="nav-link active" href="#dashboard"><i class="fas fa-home"></i> Tableau de bord</a>
                <a class="nav-link" href="./private/ajouter_paiement.php"><i class="fas fa-credit-card"></i> Ajouter paiement</a>
                <a class="nav-link" href="#profil"><i class="fas fa-user"></i> Mon profil</a>
                <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>

       
        <div class="col-md-9 col-lg-10 main-content">
            <div class="welcome-header">
                <h2>Bienvenue, <?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h2>
                <p>Formule : <?php echo htmlspecialchars($client['formule']); ?></p>
            </div>

            <div id="dashboard">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Prochaines séances</h5>
                                <?php
                                $prochaines_seances = array_filter($seances, function($seance) {
                                    return strtotime($seance['date']) > time();
                                });
                                if (count($prochaines_seances) > 0) {
                                    foreach (array_slice($prochaines_seances, 0, 3) as $seance) {
                                        echo "<p>" . date('d/m/Y H:i', strtotime($seance['date'])) . " - " . 
                                             htmlspecialchars($seance['type'] ?? 'Séance') . "</p>";
                                    }
                                } else {
                                    echo "<p>Aucune séance prévue</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Derniers paiements</h5>
                                <?php
                                if (count($paiements) > 0) {
                                    foreach (array_slice($paiements, 0, 3) as $paiement) {
                                        echo "<p>" . date('d/m/Y', strtotime($paiement['date_paiement'])) . " - " . 
                                             number_format($paiement['montant'], 2) . " DH</p>";
                                    }
                                } else {
                                    echo "<p>Aucun paiement effectué</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Prochains examens</h5>
                                <?php
                                $prochains_examens = array_filter($examens, function($examen) {
                                    return strtotime($examen['date']) > time();
                                });
                                if (count($prochains_examens) > 0) {
                                    foreach (array_slice($prochains_examens, 0, 3) as $examen) {
                                        echo "<p>" . date('d/m/Y', strtotime($examen['date'])) . " - " . 
                                             htmlspecialchars($examen['type']) . "</p>";
                                    }
                                } else {
                                    echo "<p>Aucun examen prévu</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="seances" class="mt-5">
                <h3 class="mb-4">Mes séances</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Durée</th>
                                <th>Lieu</th>
                                <th>Moniteur</th>
                                <th>Véhicule</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seances as $seance): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($seance['date'])); ?></td>
                                <td><?php echo htmlspecialchars($seance['duree']); ?></td>
                                <td><?php echo htmlspecialchars($seance['lieu']); ?></td>
                                <td><?php echo htmlspecialchars($seance['moniteur_prenom'] . ' ' . $seance['moniteur_nom']); ?></td>
                                <td><?php echo htmlspecialchars($seance['marque'] . ' ' . $seance['modele']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="paiements" class="mt-5">
                <h3 class="mb-4">Mes paiements</h3>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Montant total:</strong> <?php echo number_format($client['montant_paiement'], 2); ?> DH</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Montant payé:</strong> <?php echo number_format($total_paye, 2); ?> DH</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Montant restant:</strong> <?php echo number_format($montant_restant, 2); ?> DH</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Moyen de paiement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiements as $paiement): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($paiement['date_paiement'])); ?></td>
                                <td><?php echo number_format($paiement['montant'], 2); ?> DH</td>
                                <td><?php echo htmlspecialchars($paiement['moyen_paiement']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="examens" class="mt-5">
                <h3 class="mb-4">Mes examens</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Résultat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examens as $examen): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($examen['date'])); ?></td>
                                <td><?php echo htmlspecialchars($examen['type']); ?></td>
                                <td><?php echo htmlspecialchars($examen['resultat']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div> 

<script src="./src/bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html>
