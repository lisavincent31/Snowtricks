# Vincent_Lisa_1_repository_git_012024
 OpenClassrooms Project _ SnowTricks

 SnowTricks est un site communautaire présentant des articles sur des figures de snowboard. Les utilisateurs inscrits sur le site peuvent créer un nouvel article qui comprendra une ou plusieurs images/vidéos. Il lui sera ensuite possible de modifier ou supprimer l'article qu'il a créé ou de laisser des commentaires sur les autres articles.

## Installation
 Ce projet utilise le framework symfony. Assurez-vous d'avoir symfony 7.0 ainsi que composer installé.

 - Ouvrez votre terminal de commande et clonez le repository du projet en tapant : 
 ```bash
 git clone https://github.com/lisavincent31/Snowtricks.git
 ```
 - A la racine du projet, vous trouverez le fichier .env. Modifiez le pour créer la database snowtricks
 ```bash
    ###> .env file ###
    DATABASE_URL="mysql://YOUR_USERNAME:YOUR_PASSWORD@127.0.0.1:3306/DATABASE_NAME"
    ###> symfony/mailer ###
    MAILER_DSN=smtp://localhost:1025
    ###< symfony/mailer ###
    ADDRESS_MAIL=hello@snowtricks.com
 ```

 - Pour créer la base de données j'ai utilisé Doctrine. Une fois le fichier **.env** remplit vous pouvez ouvrir votre terminal de commande et taper : 
 ```bash
 composer require symfony/runtime
 php bin/console doctrine:database:create
 php bin/console doctrine:migrations:migrate
 ```
 - Pour peupler la base de données, vous trouverez un fichier **database.sql** à la racine du dossier. Vous pouvez tout simplement copier/coller le texte dans votre éditeur sql.
 **Attention : Pensez à bien modifier le nom de la base de données dans les deux premières lignes par le nom que vous lui avez donné.**

  - Pour intercepter les emails envoyés depuis le site, j'utilise MailHog. Vous téléchargez l'application [en cliquant ici](https://github.com/mailhog/MailHog/releases/v1.0.0). Il vous suffit de choisir la version qui convient le mieux à votre système d'exploitation, télécharger le fichier puis cliquer dessus. Cela ouvrira un terminal de commande pour lancer l'application. Vous pourrez ensuite aller sur *localhost/8025* pour voir tous les emails envoyés depuis le site SnowTricks.
 
Maintenant, dans votre terminal de commande vous pouvez lancer le serveur symfony :
```bash
symfony server:start
```
Et vous rendre sur votre navigateur web sur : **_localhost:8000/_**.
