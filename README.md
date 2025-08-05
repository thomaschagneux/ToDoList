# ToDoList

## Description
ToDoList est une application Symfony permettant de gérer des tâches personnelles. Ce projet a été développé dans le cadre du projet #8 d'OpenClassrooms : "Améliorez un projet existant".

L'application permet aux utilisateurs de :
- Créer un compte et se connecter
- Créer, modifier, supprimer et marquer comme terminées des tâches
- Gérer leurs propres tâches

## Prérequis
- PHP 8.3 ou supérieur
- Composer
- Symfony CLI (recommandé pour le serveur de développement)
- Base de données MySQL ou PostgreSQL
- Node.js et npm (pour les assets)

## Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-username/ToDoList.git
cd ToDoList
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configurer l'environnement
Créez un fichier `.env.local` à la racine du projet et configurez votre base de données :
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/todolist?serverVersion=8.0"
```

### 4. Créer la base de données et charger les fixtures
```bash
# Utiliser la commande make pour réinitialiser la base de données
make reset-db
```

Ou manuellement :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 5. Lancer le serveur de développement
```bash
symfony server:start
```
Ou
```bash
php -S localhost:8000 -t public
```

## Utilisation

### Connexion
- Accédez à `http://localhost:8000/login`
- Utilisez les identifiants suivants pour vous connecter en tant qu'administrateur :
  - Email : admin@todolist.com
  - Mot de passe : password

### Gestion des tâches
- Créer une tâche : Cliquez sur "Créer une tâche"
- Modifier une tâche : Cliquez sur "Modifier" à côté de la tâche concernée
- Marquer une tâche comme terminée : Cliquez sur "Marquer comme faite" (ou "Marquer non terminée")
- Supprimer une tâche : Cliquez sur "Supprimer"

### Gestion des utilisateurs (Admin uniquement)
- Accédez à la liste des utilisateurs : Cliquez sur "Utilisateurs" dans le menu
- Créer un utilisateur : Cliquez sur "Créer un utilisateur"
- Modifier un utilisateur : Cliquez sur "Modifier" à côté de l'utilisateur concerné

## Tests

### Préparation de l'environnement de test
```bash
make reset-test-db
```

### Exécution des tests
```bash
make test
```

Ou manuellement :
```bash
php bin/phpunit --testdox --coverage-html var/reports/
```

Les rapports de couverture de code seront générés dans le dossier `var/reports/`.

## Commandes utiles

### Nettoyage et vérification du code
```bash
make clean
```

Cette commande :
- Vide le cache
- Réchauffe le cache
- Exécute PHP-CS-Fixer pour corriger le style du code
- Exécute PHPStan pour l'analyse statique du code

## Structure du projet

Le projet suit la structure standard d'une application Symfony 7.x :

```
config/             # Configuration de l'application
public/             # Point d'entrée web et assets publics
src/
  ├── Controller/   # Contrôleurs de l'application
  ├── Entity/       # Entités Doctrine
  ├── Form/         # Types de formulaires
  ├── Repository/   # Repositories Doctrine
  └── Security/     # Classes liées à la sécurité
templates/          # Templates Twig
tests/              # Tests unitaires et fonctionnels
```

## Migration

Ce projet a été migré de Symfony 3.1 vers Symfony 7.x. Pour plus d'informations sur la migration, consultez le fichier [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md).

## Contribution

1. Forker le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/ma-fonctionnalite`)
3. Commiter vos changements (`git commit -m 'Ajout de ma fonctionnalité'`)
4. Pousser vers la branche (`git push origin feature/ma-fonctionnalite`)
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence propriétaire.
