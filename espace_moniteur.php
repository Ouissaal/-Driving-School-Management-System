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

// validation des champs
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'moniteur') {
    header('Location: connexion.php');
    exit();
}

// moniteur infos
$stmt = $pdo->prepare("SELECT * FROM moniteurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$moniteur = $stmt->fetch();

if (!$moniteur) {
    handleError('Moniteur non trouvé', 'connexion.php');
}

// moniteur session
$stmt = $pdo->prepare("SELECT * FROM seances WHERE moniteur_id = ? ORDER BY date DESC");
$stmt->execute([$_SESSION['user_id']]);
$seances = $stmt->fetchAll();

// sessions details
$seances_details = [];
foreach ($seances as $seance) {
    // client info
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$seance['client_id']]);
    $client = $stmt->fetch();
    
    //vehicle info
    $stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ?");
    $stmt->execute([$seance['vehicule_id']]);
    $vehicule = $stmt->fetch();
    
    $seances_details[$seance['id']] = [
        'client' => $client,
        'vehicule' => $vehicule
    ];
}

// récupperation de consommation de carburant
$stmt = $pdo->prepare("
    SELECT c.*, s.date, v.marque, v.modele, v.immatriculation
    FROM consommation_carburant c
    JOIN seances s ON c.seance_id = s.id
    JOIN vehicules v ON c.vehicule_id = v.id
    WHERE s.moniteur_id = :moniteur_id
    ORDER BY c.date_consommation DESC
");
$stmt->execute(['moniteur_id' => $_SESSION['user_id']]);
$carburant = $stmt->fetchAll();

//Gérer la soumission du formulaire de consommation de carburant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_carburant'])) {
    $seance_id = validateInput($_POST['seance_id']);
    $montant = validateInput($_POST['montant']);
    $kilometrage = validateInput($_POST['kilometrage']);
    $commentaire = validateInput($_POST['commentaire']);

    // Validation des données
    if (!is_numeric($montant) || $montant <= 0) {
        handleError('Le montant doit être un nombre positif');
    }

    try {
        //vehicle_id from seance
        $stmt = $pdo->prepare("SELECT vehicule_id FROM seances WHERE id = ?");
        $stmt->execute([$seance_id]);
        $seance = $stmt->fetch();
        
        if (!$seance || !$seance['vehicule_id']) {
            handleError('Véhicule non trouvé pour cette séance');
            exit();
        }

        $stmt = $pdo->prepare("
            INSERT INTO consommation_carburant (seance_id, vehicule_id, montant, date_consommation, kilometrage, commentaire)
            VALUES (:seance_id, :vehicule_id, :montant, CURDATE(), :kilometrage, :commentaire)
        ");
        $stmt->execute([
            'seance_id' => $seance_id,
            'vehicule_id' => $seance['vehicule_id'],
            'montant' => $montant,
            'kilometrage' => $kilometrage ?: null,
            'commentaire' => $commentaire
        ]);
        $_SESSION['success'] = "Consommation de carburant enregistrée avec succès.";
    } catch (PDOException $e) {
        handleError("Erreur lors de l'enregistrement de la consommation de carburant.");
    }
}

//Gérer la soumission du formulaire de nouvelle session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_seance'])) {
    $date = validateInput($_POST['date']);
    $heure = validateInput($_POST['heure']);
    $duree = validateInput($_POST['duree']);
    $lieu = validateInput($_POST['lieu']);
    $client_id = validateInput($_POST['client_id']);
    $vehicule_id = validateInput($_POST['vehicule_id']);

    try {
        $date_heure = $date . ' ' . $heure;
        $stmt = $pdo->prepare("
            INSERT INTO seances (date, duree, lieu, moniteur_id, client_id, vehicule_id)
            VALUES (:date, :duree, :lieu, :moniteur_id, :client_id, :vehicule_id)
        ");
        $stmt->execute([
            'date' => $date_heure,
            'duree' => $duree,
            'lieu' => $lieu,
            'moniteur_id' => $_SESSION['user_id'],
            'client_id' => $client_id,
            'vehicule_id' => $vehicule_id
        ]);
        $_SESSION['success'] = "Séance ajoutée avec succès.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        handleError("Erreur lors de l'ajout de la séance.");
    }
}

// Récupérer les clients disponibles.
$stmt = $pdo->prepare("SELECT * FROM clients ORDER BY nom, prenom");
$stmt->execute();
$clients = $stmt->fetchAll();

// Récupérer les véhicules disponibles.
$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE etat = 'disponible' ORDER BY marque, modele");
$stmt->execute();
$vehicules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Espace Moniteur </title>
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="./src/style.css" />
</head>
<body>
    <div class="container-fluid">
        <div class="row">
           
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="mb-4">Espace Moniteur</h3>
                <div class="nav flex-column">
                    <a class="nav-link active" href="#dashboard"><i class="fas fa-home"></i> Tableau de bord</a>
                    <a class="nav-link" href="#planning"><i class="fas fa-calendar"></i> Planning</a>
                    <a class="nav-link" href="#carburant"><i class="fas fa-gas-pump"></i> Carburant</a>
                    <a class="nav-link" href="#profil"><i class="fas fa-user"></i> Mon profil</a>
                    <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>

         
            <div class="col-md-9 col-lg-10 main-content">
              
                <div class="welcome-header">
                    <h2>Bienvenue, <?= htmlspecialchars($moniteur['prenom'] . ' ' . $moniteur['nom']); ?></h2>
                    <p >Statut : <span class="badge bg-success"><?= htmlspecialchars($moniteur['statut']); ?></span></p>
                </div>

             
                <div id="dashboard">
                    <h3 class="mb-4">Tableau de bord</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Prochaines séances</h5>
                                    <?php
                                    $prochaines_seances = array_filter($seances, function($seance) {
                                        return strtotime($seance['date']) > time();
                                    });
                                    if (count($prochaines_seances) > 0) {
                                        foreach (array_slice($prochaines_seances, 0, 3) as $seance) {
                                            $client = $seances_details[$seance['id']]['client'];
                                            $client_name = $client ? htmlspecialchars($client['prenom'] . ' ' . $client['nom']) : 'Client non assigné';
                                            echo "<p>" . date('d/m/Y H:i', strtotime($seance['date'])) . " - " . $client_name . "</p>";
                                        }
                                    } else {
                                        echo "<p>Aucune séance prévue</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Consommation carburant</h5>
                                    <?php
                                    if (count($carburant) > 0) {
                                        echo "<ul class='list-group'>";
                                        foreach ($carburant as $consom) {
                                            echo "<li class='list-group-item'>";
                                            echo date('d/m/Y', strtotime($consom['date_consommation'])) . " - Véhicule: " . htmlspecialchars($consom['marque'] . " " . $consom['modele'] . " (" . $consom['immatriculation'] . ")") . " - Montant: " . number_format($consom['montant'], 2) . " DH";
                                            if (!empty($consom['commentaire'])) {
                                                echo "<br><small>Commentaire: " . htmlspecialchars($consom['commentaire']) . "</small>";
                                            }
                                            echo "</li>";
                                        }
                                        echo "</ul>";
                                    } else {
                                        echo "<p>Aucune consommation enregistrée.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

             
                <section id="planning" class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Planning des séances</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterSeanceModal">
                            <i class="fas fa-plus"></i> Ajouter une séance
                        </button>
                    </div>
                    <?php if (count($seances) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Véhicule</th>
                                        <th>Heure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seances as $seance): ?>
                                        <?php
                                        $client = $seances_details[$seance['id']]['client'];
                                        $vehicule = $seances_details[$seance['id']]['vehicule'];
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($seance['date'])) ?></td>
                                            <td><?= $client ? htmlspecialchars($client['prenom'] . ' ' . $client['nom']) : 'Client inconnu' ?></td>
                                            <td><?= $vehicule ? htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) : 'Véhicule inconnu' ?></td>
                                            <td><?= date('H:i', strtotime($seance['date'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Aucune séance planifiée.</p>
                    <?php endif; ?>
                </section>

                <!-- Carburant -->
                <section id="carburant" class="mb-5">
                    <h3 class="mb-4">Suivi du carburant</h3>
                    
                    <!-- Formulaire d'ajout de consommation -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Ajouter une consommation</h5>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>
                            <form method="POST" action="" class="row g-3">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Séance</label>
                                        <select name="seance_id" class="form-select" required>
                                            <option value="">Sélectionner une séance</option>
                                            <?php foreach ($seances as $seance): 
                                                $client = $seances_details[$seance['id']]['client'];
                                                $client_name = $client ? htmlspecialchars($client['prenom'] . ' ' . $client['nom']) : 'Client non assigné';
                                            ?>
                                                <option value="<?php echo $seance['id']; ?>">
                                                    <?php echo date('d/m/Y H:i', strtotime($seance['date'])) . ' - ' . $client_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Montant (DH)</label>
                                        <input type="number" step="0.01" name="montant" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Kilométrage</label>
                                        <input type="number" name="kilometrage" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Commentaire</label>
                                        <input type="text" name="commentaire" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" name="ajouter_carburant" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des consommations -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Historique des consommations</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Véhicule</th>
                                            <th>Montant</th>
                                            <th>Kilométrage</th>
                                            <th>Commentaire</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($carburant as $consommation): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($consommation['date_consommation'])); ?></td>
                                            <td><?php echo htmlspecialchars($consommation['marque'] . ' ' . $consommation['modele']); ?></td>
                                            <td><?php echo isset($consommation['montant']) ? number_format($consommation['montant'], 2) : '0.00'; ?> DH</td>
                                            <td><?php echo isset($consommation['kilometrage']) ? htmlspecialchars($consommation['kilometrage']) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($consommation['commentaire']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            
                <div id="profil" class="mt-5">
                    <h3 class="mb-4">Mon profil</h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="profile-info">
                                <p><strong>Nom complet:</strong> <?= htmlspecialchars($moniteur['prenom'] . ' ' . $moniteur['nom']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($moniteur['email']) ?></p>
                                <p><strong>Statut:</strong> <?= htmlspecialchars($moniteur['statut']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Séance -->
    <div class="modal fade" id="ajouterSeanceModal" tabindex="-1" aria-labelledby="ajouterSeanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajouterSeanceModalLabel">Ajouter une nouvelle séance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="heure" class="form-label">Heure</label>
                                <input type="time" class="form-control" id="heure" name="heure" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duree" class="form-label">Durée</label>
                                <select class="form-select" id="duree" name="duree" required>
                                    <option value="01:00:00">1 heure</option>
                                    <option value="01:30:00">1 heure 30</option>
                                    <option value="02:00:00">2 heures</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lieu" class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="lieu" name="lieu" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Client</label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Sélectionner un client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>">
                                            <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehicule_id" class="form-label">Véhicule</label>
                                <select class="form-select" id="vehicule_id" name="vehicule_id" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    <?php foreach ($vehicules as $vehicule): ?>
                                        <option value="<?= $vehicule['id'] ?>">
                                            <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele'] . ' (' . $vehicule['immatriculation'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="ajouter_seance" class="btn btn-primary">Ajouter la séance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="./src/bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html> 