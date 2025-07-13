<?php

$pdo = new PDO("mysql:host=localhost", "root", "");

try {

    $sql = "CREATE DATABASE IF NOT EXISTS auto_ecole_db2";
    $pdo->exec($sql);
    echo "Base de données créée avec succès<br>";

    $pdo->exec("USE auto_ecole_db2");

   
    $sql = "
    -- Table des clients
    CREATE TABLE IF NOT EXISTS clients (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse TEXT,
        email VARCHAR(100) UNIQUE,
        telephone VARCHAR(20),
        password VARCHAR(255),
        formule ENUM('standard', 'accélérée', 'intensive') NOT NULL,
        montant_paiement DECIMAL(10,2) NOT NULL,  
        date_inscription DATE NOT NULL
    );

    -- Table des moniteurs 
    CREATE TABLE IF NOT EXISTS moniteurs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE,
        telephone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        statut ENUM('actif', 'inactif') DEFAULT 'actif'
    );

    -- Table des véhicules avec etat ENUM
    CREATE TABLE IF NOT EXISTS vehicules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        marque VARCHAR(50) NOT NULL,
        modele VARCHAR(50),
        type VARCHAR(50),
        immatriculation VARCHAR(20) UNIQUE,
        etat ENUM('disponible', 'utilise', 'indisponible') DEFAULT 'disponible',
        kilometrage INT,
        date_achat DATE
    );

    -- Table des séances 
    CREATE TABLE IF NOT EXISTS seances (
        id INT PRIMARY KEY AUTO_INCREMENT,
        date DATETIME NOT NULL,
        duree TIME NOT NULL,
        lieu VARCHAR(100),
        moniteur_id INT,
        vehicule_id INT,
        client_id INT,
        FOREIGN KEY (moniteur_id) REFERENCES moniteurs(id) ON DELETE SET NULL,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
        FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE SET NULL
    );

    -- Table des paiements
    CREATE TABLE IF NOT EXISTS paiements (
        id INT PRIMARY KEY AUTO_INCREMENT,
        client_id INT NOT NULL,
        montant DECIMAL(10,2) NOT NULL,
        date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        moyen_paiement VARCHAR(50) NOT NULL,
        montant_restant DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
    );

    -- Table des examens
    CREATE TABLE IF NOT EXISTS examens (
        id INT PRIMARY KEY AUTO_INCREMENT,
        client_id INT NOT NULL,
        type ENUM('code', 'conduite') NOT NULL,
        date DATE NOT NULL,
        lieu VARCHAR(100),
        resultat ENUM('admis', 'non_admis') NOT NULL,
        tentative INT NOT NULL DEFAULT 1,
        notes TEXT,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
    );

    -- Table des maintenances
    CREATE TABLE IF NOT EXISTS maintenances (
        id INT PRIMARY KEY AUTO_INCREMENT,
        vehicule_id INT NOT NULL,
        date_maintenance DATE NOT NULL,
        type_maintenance VARCHAR(100) NOT NULL,
        description TEXT,
        cout DECIMAL(10,2),
        FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
    );

    -- Table des administrateurs
    CREATE TABLE IF NOT EXISTS administrateurs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'super_admin') DEFAULT 'admin',
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Table consommation de carburant
    CREATE TABLE IF NOT EXISTS consommation_carburant (
        id INT PRIMARY KEY AUTO_INCREMENT,
        seance_id INT NOT NULL,
        vehicule_id INT NOT NULL,
        montant DECIMAL(10,2) NOT NULL,
        date_consommation DATE NOT NULL,
        kilometrage INT,
        commentaire TEXT,
        FOREIGN KEY (seance_id) REFERENCES seances(id) ON DELETE CASCADE,
        FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "Tables créées avec succès<br>";

    // Insertion d'un administrateur par défaut
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO administrateurs (nom, prenom, email, password, role) 
            VALUES ('Bouamar', 'Ouissal', 'admin@auto-ecole.com', :password, 'super_admin')";

            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['password' => $admin_password]);
    echo "Administrateur par défaut créé avec succès<br>";

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}


?>
