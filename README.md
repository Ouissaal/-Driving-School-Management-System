# Application Web de Gestion d'Auto-école

Une application web développée en **PHP**, **MySQL**, **Bootstrap** et **HTML**, conçue pour digitaliser et améliorer la gestion des séances de conduite, des véhicules, des étudiants et des moniteurs dans une auto-école.


## 🎯 Objectif

L’application vise à :

- Résoudre le manque d'organisation dans la gestion manuelle
- Automatiser la planification des séances
- Fournir un espace personnalisé à chaque acteur de l’auto-école
- Optimiser l’utilisation des véhicules et du temps des moniteurs


##  Espaces Utilisateurs

### 1. **Client**

- Créer un compte (inscription en ligne)
- Se connecter à son espace personnel
- Voir ses séances planifiées
- Connaître le moniteur, le véhicule et l’horaire
- Être informé des changements

### 2. **Moniteur**

- Voir son planning personnel
- Consulter la liste des étudiants affectés à ses séances
- Gérer sa disponibilité
- **Ajouter de nouvelles séances**
- **Spécifier le montant de carburant nécessaire pour chaque séance**

### 3. **Administrateur**

- Gérer les comptes utilisateurs (clients & moniteurs)
- Créer, modifier et supprimer les séances
- Gérer les véhicules disponibles
- Attribuer automatiquement ou manuellement les séances
- Superviser l’ensemble des plannings

---

##  Technologies Utilisées

- **Frontend** : HTML, CSS, Bootstrap
- **Backend** : PHP avec PDO
- **Base de données** : MySQL

---

## 📽️ Démo Vidéo

👉 (https://github.com/Ouissaal/-Driving-School-Management-System/issues/1#issuecomment-3141134099)

---

##  Sécurité Intégrée

- **Protection CSRF** : Jetons CSRF dans les formulaires critiques
- **Échappement des données** : `htmlspecialchars()` pour éviter les attaques XSS
- **Validation des entrées** côté serveur
- **Requêtes préparées (PDO ou MySQLi)** contre les injections SQL
- **Gestion sécurisée des sessions**
- **Contrôle d’accès par rôle (admin, moniteur, client)**

---

##  Fonctionnalités Clés

- Authentification multi-rôle (Admin, Moniteur, Client)
- **Inscription (Sign Up)** pour les nouveaux clients
- Tableau de bord personnalisé selon le rôle
- Planification des séances avec affectation de véhicule et moniteur
- Gestion du carburant par séance 
- Interface responsive adaptée à tous les appareils

<div align="center">
  <p>© 2025 OB_gestion. Tous droits réservés.</p>
</div>
