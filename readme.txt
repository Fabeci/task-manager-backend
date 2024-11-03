README - Gestion de Tâches
Prérequis
Avant de commencer, assurez-vous d'avoir installé les éléments suivants :

PHP (version 8.0 ou supérieure)
Composer
Node.js (version 14 ou supérieure)
NPM ou Yarn
MySQL (ou tout autre système de gestion de base de données que vous préférez)
Configuration du Backend (Laravel)

Clonez le dépôt :
git clone <URL_DU_DÉPÔT>
cd task-manager-backend
Installez les dépendances :

composer install
Configurez votre fichier .env :

Copiez le fichier .env.example en .env et modifiez les valeurs pour correspondre à votre environnement local.

cp .env.example .env
Modifiez les paramètres de la base de données :

plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_votre_base_de_données
DB_USERNAME=votre_nom_utilisateur
DB_PASSWORD=votre_mot_de_passe
Générez la clé d'application :

php artisan key:generate
Migrez la base de données :

php artisan migrate
Démarrez le serveur :

php artisan serve
Votre API backend sera disponible à http://localhost:8000