# Projet Open Classrooms de site communautaire SnowTricks

Information du projet
Projet de la formation Développeur d'application - PHP / Symfony.

---

## Développez de A à Z le site communautaire SnowTricks

[![Maintainability](https://api.codeclimate.com/v1/badges/ca5bb6834e071a4d097b/maintainability)](https://codeclimate.com/github/D-Jerome/P6-SnowTricks/maintainability)

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/eeb0589126a541ac8b4c10fba0b0b4cc)](https://app.codacy.com/gh/D-Jerome/P6-SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

---

## Configuration / Technologies

PHP 8.2
Composer 2.6.3
Symfony 7.0.2
Bootstrap 5.3.2

Voici les principales fonctionnalités disponibles :

-   Le visiteur:

    -   Visiter la page d'accueil.
    -   S'inscrire sur le site.
    -   Parcourir les Tricks crées par les utilisateurs et parcourir les commentaires.

-   L'utilisateur (inscrit et activé):

    -   Prérequis: s'être enregistré via le formulaire d'inscription.
    -   Accès aux mêmes fonctionnaités que le visiteur.
    -   Ajout de commentaires et ajout/modification/suppression des ses propres Tricks.

---

### Informations

Un thème de base a été choisi pour réaliser ce projet, il s'agit du thème bootswatch.com/quartz/.

La version en ligne n'est pas encore disponible.

---

### Prérequis

Php ainsi que Composer doivent être installés sur votre serveur afin de pouvoir correctement lancé le blog.
Une base de données Mysql pour le stockage des données.

---

### Installation

-   **Etape 1** : Cloner le Repositary sur votre serveur.

```
                git clone https://github.com/D-Jerome/P6-SnowTricks.git
```

-   **Etape 2** : Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier `.env.local` qui devra être crée à la racine du projet en réalisant une copie du fichier `.env`.

-   **Etape 3** : Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :

```
    php bin/console doctrine:database:create
```

-   **Etape 4** : Créez les différentes tables de la base de données en appliquant les migrations :

```
    php bin/console doctrine:migrations:migrate
```

-   **Etape 5** :(Optionnel) Installer les fixtures pour avoir une démo de données fictives :

```
    php bin/console doctrine:fixtures:load
```

-   **Etape 6** : Félications le projet est installé correctement, vous pouvez désormais commencer à l'utiliser à votre guise !

---

### Mise en place du compte utilisateur

-   **Etape 1** : Inscrivez-vous

-   **Etape 2** : Activez votre compte pa rle lien de verification de l'email

-   **Etape 3** : Votre compte est désormais opérationnel.

---

## Librairies utilisées

    - Webmozart
    - Phpstan
    - Mailer

---

## Auteur

Dubus Jérôme - Étudiant à Openclassrooms - Développeur d'application PHP/Symfony
