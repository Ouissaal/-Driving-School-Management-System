<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OB Auto-École</title>
    <link href="./src/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./src/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="./média/our_school_logo.png" alt="logo" style="width: 40px; height: auto; margin-right: 8px;">
            Auto-École
        </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#accueil">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#a-propos">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cours">Nos Offres</a></li>
                    <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                    <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="accueil">
        <video class="intro-video" autoplay loop muted playsinline>
            <source src="média/vidéo.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo HTML5.
        </video>
        <div class="hero-overlay">
            <div class="hero-content">
                <h1>Bienvenue à l'Auto-École</h1>
                <p class="lead">apprenez à conduire en toute confiance.</p>
                <a href="inscription.php" class="btn btn-lg">Commencer maintenant</a>
            </div>
        </div>
    </section>



    <section class="py-5" id="a-propos" style="background-color:#a9a093;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Notre Mission</h1>
                    <p>Chez OB Auto-École , nous nous engageons à former des conducteurs responsables et confiants. Notre équipe de moniteurs expérimentés vous accompagne tout au long de votre parcours d'apprentissage.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Moniteurs qualifiés et expérimentés</li>
                        <li><i class="fas fa-check text-success"></i> Véhicules modernes et bien entretenus</li>
                        <li><i class="fas fa-check text-success"></i> Taux de réussite élevé aux examens</li>
                        <li><i class="fas fa-check text-success"></i> Horaires flexibles adaptés à vos besoins</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="média/picture_intro.png" class="img-fluid rounded shadow" alt="Auto-école" style="width: 600px; height: 600px;">
                </div>
            </div>
        </div>
    </section>



    <section class="py-5 " id="cours"  >
        <div class="container">
            <h1 class="fw-bold mb-3 text-center p-4"> Profitez de nos Offres Spéciales !</h1>
            <div class="row text-center">
                <!-- Formule Standard -->
                <div class="col-md-4 ">
                    <div class="course-card shadow">
                        <h3>Formule Standard</h3>
                        <p>Formation complète sur plusieurs mois</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-clock"></i> 20 heures de conduite</li>
                            <li><i class="fas fa-book"></i> Cours de code illimités</li>
                            <li><i class="fas fa-car"></i> 1 passage à l'examen</li>
                        </ul>
                        <p class="h5 text-muted text-decoration-line-through">4999DH</p>
                        <p class="h3 text-danger">2999DH</p>
                        <a href="inscription.php" class="btn btn_cards btn-lg text-white">S'inscrire</a>
                    </div>
                </div>
                <!-- Formule Accélérée -->
                <div class="col-md-4">
                    <div class="course-card shadow">
                        <h3>Formule Accélérée</h3>
                        <p>Formation intensive sur 1 mois</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-clock"></i> 25 heures de conduite</li>
                            <li><i class="fas fa-book"></i> Cours de code illimités</li>
                            <li><i class="fas fa-car"></i> 2 passages à l'examen</li>
                        </ul>
                       <p class="h5 text-muted text-decoration-line-through">5999DH</p>
                        <p class="h3 text-danger">4499DH</p>
                        <a href="inscription.php" class="btn btn_cards btn-lg text-white">S'inscrire</a>
                    </div>
                </div>
                <!-- Formule Intensive -->
                <div class="col-md-4">
                    <div class="course-card shadow">
                        <h3>Formule Intensive</h3>
                        <p>Formation ultra-rapide sur 2 semaines</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-clock"></i> 30 heures de conduite</li>
                            <li><i class="fas fa-book"></i> Cours de code illimités</li>
                            <li><i class="fas fa-car"></i> 3 passages à l'examen</li>
                        </ul>
                         <p class="h5 text-muted text-decoration-line-through">7999DH</p>
                        <p class="h3 text-danger">6999DH</p>
                        <a href="inscription.php" class="btn btn_cards btn-lg text-white">S'inscrire</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

   
    <footer>
        <p class=" text-center text-light ">Bouamar Ouissal@2025</p>
    </footer>

    <script src="./src/bootstrap/js/bootstrap.bundle.js"></script>
</body>
</html> 