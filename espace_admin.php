<?php
session_start();

require_once 'connexionpdo.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: connexion.php');
    exit();
}

// Fonction pour vérifier la disponibilité d’un véhicule
function estVehiculeDisponible($pdo, $vehicule_id) {
    // Vérifie si le véhicule est utilisé dans une séance active (date >= aujourd'hui)
                   //(COUNT(*)) --> compte le nombre de séances      (date >= CURDATE())->  la date de la séance est aujourd’hui ou dans le futur.
    $sqlSeance = "SELECT COUNT(*) FROM seances WHERE vehicule_id = :vehicule_id AND date >= CURDATE()";
    $stmtSeance = $pdo->prepare($sqlSeance);
    $stmtSeance->execute(['vehicule_id' => $vehicule_id]);
    $estUtilise = $stmtSeance->fetchColumn() > 0;  // Si oui, $estUtilise sera true


    // Vérifie si le véhicule est en maintenance (date_maintenance >= aujourd'hui)
    $sqlMaintenance = "SELECT COUNT(*) FROM maintenances WHERE vehicule_id = :vehicule_id AND date_maintenance >= CURDATE()";
    $stmtMaint = $pdo->prepare($sqlMaintenance);
    $stmtMaint->execute(['vehicule_id' => $vehicule_id]);
    $enMaintenance = $stmtMaint->fetchColumn() > 0;

    if ($estUtilise) {
        return "Utilisé";
    } elseif ($enMaintenance) {
        return "En maintenance";
    } else {
        return "Disponible";
    }
}

// clients
$stmt = $pdo->query("SELECT * FROM clients ORDER BY nom, prenom");
$clients = $stmt->fetchAll();

// moniteurs
$stmt = $pdo->query("SELECT * FROM moniteurs ORDER BY nom, prenom");
$moniteurs = $stmt->fetchAll();

// véhicules
$stmt = $pdo->query("SELECT * FROM vehicules ORDER BY marque, modele");
$vehicules = $stmt->fetchAll();

// séances avec jointures
$stmt = $pdo->query("
    SELECT s.*, 
           c.nom AS client_nom, c.prenom AS client_prenom,
           m.nom AS moniteur_nom, m.prenom AS moniteur_prenom,
           v.marque, v.modele
    FROM seances s
    LEFT JOIN clients c ON s.client_id = c.id
    LEFT JOIN moniteurs m ON s.moniteur_id = m.id
    LEFT JOIN vehicules v ON s.vehicule_id = v.id
    ORDER BY s.date DESC
");
$seances = $stmt->fetchAll();

// paiements
$stmt = $pdo->query("
    SELECT p.*, c.nom, c.prenom 
    FROM paiements p 
    JOIN clients c ON p.client_id = c.id 
    ORDER BY p.date_paiement DESC
");
$paiements = $stmt->fetchAll();

// Examens avec clients
$stmt = $pdo->query("
    SELECT e.*, c.nom AS client_nom, c.prenom AS client_prenom 
    FROM examens e 
    JOIN clients c ON e.client_id = c.id 
    ORDER BY e.date DESC
");
$examens = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur</title>
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./src/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
       
        <div class="col-md-3 col-lg-2 sidebar">
            <h3 class="text-center mb-4">Admin</h3>
            <nav>
                <a href="espace_admin.php" class="active"><i class="fas fa-home me-2"></i>Tableau de bord</a>
                <a href="deconnexion.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a>
            </nav>
        </div>

        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="text-center mb-4">Espace Administrateur</h2>

            
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active bg-transparent" id="seances-tab" data-bs-toggle="tab" data-bs-target="#seances" type="button" role="tab">
                        <i class="fas fa-calendar-alt me-2"></i>Séances
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-transparent" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>Clients
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-transparent" id="moniteurs-tab" data-bs-toggle="tab" data-bs-target="#moniteurs" type="button" role="tab">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Moniteurs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bg-transparent" id="vehicules-tab" data-bs-toggle="tab" data-bs-target="#vehicules" type="button" role="tab">
                        <i class="fas fa-car me-2"></i>Véhicules
                    </button>
                </li>

                
                <li class="nav-item" role="presentation">
                    <button class="nav-link  bg-transparent" id="seances-tab" data-bs-toggle="tab" data-bs-target="#examen" type="button" role="tab">
                        <i class="fas fa-calendar-alt me-2"></i> examens
                    </button>
                </li>
            </ul>

<!-- Séances  -->
           
            <div class="tab-content" id="adminTabsContent">
            
                <div class="tab-pane fade show active" id="seances" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    
                                    <th>Date</th>
                                    <th>Duree</th>
                                    <th>Lieu</th>
                                    <th>Véhicule</th>
                                    <th>Moniteur</th>
                                    <th>Client</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($seances as $seance): ?>
                                <tr>
                                    
                                    <td><?php echo date('d/m/Y H:i', strtotime($seance['date'])); ?></td>
                                    <td><?php echo $seance['duree']; ?></td>
                                    <td><?php echo $seance['lieu']; ?></td>
                                    <td><?php echo $seance['marque'] . ' ' . $seance['modele']; ?></td>
                                    <td><?php echo $seance['moniteur_prenom'] . ' ' . $seance['moniteur_nom']; ?></td>
                                    <td><?php echo htmlspecialchars($seance['client_prenom'] . ' ' . $seance['client_nom']); ?></td>

                                    <td>
                                        <a href="private/modifier_seance.php?id=<?php echo $seance['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="private/supprimer_seance.php?id=<?php echo $seance['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="private/ajouter_seance.php" class="btn btn-success">Ajouter</a>
                    </div>
                </div>

<!-- Clients  -->
                <div class="tab-pane fade" id="clients" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Formule</th>
                                    <th>Montant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><?php echo $client['nom']; ?></td>
                                    <td><?php echo $client['prenom']; ?></td>
                                    <td><?php echo $client['email']; ?></td>
                                    <td><?php echo $client['telephone']; ?></td>
                                    <td><?php echo $client['formule']; ?></td>
                                    <td><?php echo $client['montant_paiement'];?></td>

                                    <td>
                                        <a href="private/modifier_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="private/supprimer_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="private/ajouter_client.php" class="btn btn-success">Ajouter</a>
                    </div>
                </div>

<!-- Moniteurs  -->
                <div class="tab-pane fade" id="moniteurs" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($moniteurs as $moniteur): ?>
                                <tr>
                                    <td><?php echo $moniteur['nom']; ?></td>
                                    <td><?php echo $moniteur['prenom']; ?></td>
                                    <td><?php echo $moniteur['email']; ?></td>
                                    <td><?php echo $moniteur['telephone']; ?></td>
                                    <td><?php echo $moniteur['statut']; ?></td>
                                    <td>
                                        <a href="private/modifier_moniteur.php?id=<?php echo $moniteur['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="private/supprimer_moniteur.php?id=<?php echo $moniteur['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce moniteur ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="private/ajouter_moniteur.php" class="btn btn-success">Ajouter</a>
                    </div>
                </div>

<!-- Véhicules  -->
                <div class="tab-pane fade" id="vehicules" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Marque</th>
                                    <th>Modèle</th>
                                    <th>Immatriculation</th>
                                    <th>Type</th>
                                    <th>État</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($vehicules as $vehicule): ?>
                                <tr>
                                    <td><?php echo $vehicule['marque']; ?></td>
                                    <td><?php echo $vehicule['modele']; ?></td>
                                    <td><?php echo $vehicule['immatriculation']; ?></td>
                                    <td><?php echo $vehicule['type']; ?></td>
                                    <td><?= estVehiculeDisponible($pdo, $vehicule['id']) ?></td>
                                    <td>
                                        <a href="private/modifier_vehicule.php?id=<?php echo $vehicule['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="private/supprimer_vehicule.php?id=<?php echo $vehicule['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="private/ajouter_vehicule.php" class="btn btn-success">Ajouter</a>
                    </div>
                </div>

<!-- Examens  -->
                <div class="tab-pane fade" id="examen" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Lieu</th>
                                    <th>Résultat</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($examens as $e): ?>
                                <tr>
                                     
                                    <td><?= $e['client_nom'] ?> <?= $e['client_prenom'] ?></td>
                                    <td><?= $e['date'] ?></td>
                                    <td><?= $e['lieu'] ?></td>
                                    <td><?= $e['resultat'] ?></td>
                                    <td><?= $e['type'] ?></td>
                                    <td>
                                        <a href="private/modifier_examen.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="private/supprimer_examen.php?id=<?php echo $e['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="private/ajouter_examen.php" class="btn btn-success">Ajouter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="./src/bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html> 