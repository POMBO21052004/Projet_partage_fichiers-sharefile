# Projet Share File

**Share File** est une application web sécurisée de gestion et de partage de fichiers, développée avec Laravel et Tailwind CSS. Elle permet à des utilisateurs de gérer leurs espaces de stockage, et aux administrateurs de superviser l'ensemble des données et des accès de la plateforme.

## 🚀 Fonctionnalités Principales

### 👤 Espace Utilisateur
- **Explorateur de Fichiers** : Interface moderne pour naviguer dans ses dossiers et fichiers.
- **Gestion des Dossiers** : Création, renommage, déplacement et suppression des dossiers personnels. La suppression d'un dossier efface automatiquement tout son contenu.
- **Gestion des Fichiers** : Téléchargement, déplacement, renommage et suppression.
- **Upload Performant (AJAX)** : 
  - Sélection multiple de fichiers.
  - Barre de progression en temps réel pour le suivi de l'upload.
  - Support de gros fichiers (jusqu'à 3 Go par fichier).
- **Modales Sécurisées** : Les actions utilisent des modales interactives.

### 🛡️ Espace Administrateur
- **Contrôle Total** : Les administrateurs peuvent voir, modifier, déplacer et supprimer *tous* les fichiers et dossiers du système.
- **Gestion des Rôles et Utilisateurs** : Création et édition de comptes.
- **Audit Trail (Traçabilité)** : Registre d'audit complet permettant de suivre les actions.
- **Gestion des Permissions** : Interface pour modifier les accès de n'importe quel fichier ou dossier du système.

## 🛠️ Stack Technique

- **Backend** : Laravel (PHP 8+)
- **Base de données** : MySQL / SQLite (via Eloquent ORM)
- **Frontend** : Blade Templates, Tailwind CSS (via CDN), JavaScript Vanilla (AJAX)
- **Icônes** : Google Material Symbols

## 📦 Installation & Configuration

1. **Cloner le dépôt**
   ``bash
   git clone <url-du-depot>
   cd "Projet Share File"
   ``

2. **Installer les dépendances PHP**
   ``bash
   composer install
   ``

3. **Configuration de l'environnement**
   Dupliquez le fichier .env.example en .env :
   ``bash
   cp .env.example .env
   ``
   Générez la clé d'application :
   ``bash
   php artisan key:generate
   ``
   Configurez votre base de données dans le fichier .env.

4. **Migrations et Seeders**
   ``bash
   php artisan migrate --seed
   ``

5. **Liaison du stockage (Storage Link)**
   *Étape cruciale* pour le système de fichiers Laravel :
   ``bash
   php artisan storage:link
   ``

## ⚙️ Configuration Serveur (Important)

L'application autorise l'upload de très gros fichiers (jusqu'à **3 Go**). Vous **devez** mettre à jour votre fichier php.ini avec les valeurs suivantes :

`ini
upload_max_filesize = 3G
post_max_size = 3G
memory_limit = 3G
max_execution_time = 3600
max_input_time = 3600
`

## 🚀 Utilisation

Lancez le serveur :
``bash
php artisan serve
``

Accédez à l'application via http://localhost:8000.
- **Panel Admin** : /admin
- **Panel Utilisateur** : /user (ou /dashboard)
