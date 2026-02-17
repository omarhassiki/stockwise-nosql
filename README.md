# StockWise NoSQL (MongoDB + PHP)

## Contexte / utilité
StockWise est une mini-application de gestion de stock :
- Produits (CRUD + recherche)
- Fournisseurs (CRUD + recherche + coordonnées GPS)
- Commandes (CRUD + recherche) avec lignes `items[]`

L’objectif est de manipuler des collections MongoDB liées entre elles depuis une interface web.

## Technologies
- PHP (XAMPP / Apache)
- MongoDB (local)
- Extension PHP MongoDB (`php_mongodb.dll`)
- Composer + librairie `mongodb/mongodb`

## Installation (Windows / XAMPP)
1. Installer XAMPP et démarrer **Apache**
2. Installer MongoDB et démarrer le service MongoDB
3. Copier le projet dans :
   `C:\xampp\htdocs\stockwise-nosql`
4. Activer l’extension MongoDB dans :
   `C:\xampp\php\php.ini`  
   Ajouter / vérifier :
   `extension=php_mongodb.dll`  
   Puis redémarrer Apache
5. Installer les dépendances :
   ```bash
   "C:\ProgramData\ComposerSetup\bin\composer.bat" install
