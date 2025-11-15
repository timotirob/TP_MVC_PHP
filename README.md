# Projet : Application d'Inscription (PHP & MVC)

Ce projet est un exercice d'introduction à la création d'une application web PHP moderne. En partant d'une structure simple, nous construirons une application d'inscription et de connexion en respectant l'architecture **Modèle-Vue-Contrôleur (MVC)**, en nous connectant à une base de données avec **PDO** et en implémentant les **bonnes pratiques de sécurité**.

## 🎯 Contexte

Vous êtes missionné par l'ENC pour développer le module d'inscription finale des nouveaux étudiants après Parcoursup. Ce module doit permettre aux étudiants de créer un compte en saisissant leurs informations et un mot de passe.

## 📚 Objectifs Pédagogiques

À la fin de ces TPs, vous saurez :
* Structurer une application en **Modèle-Vue-Contrôleur (MVC)**.
* Séparer les responsabilités : logique (Contrôleur), données (Modèle), affichage (Vue).
* Vous connecter à une base de données **MySQL avec PDO** et utiliser des **requêtes préparées**.
* Faire évoluer une application MVC en ajoutant de nouvelles fonctionnalités (champs, pages).
* Comprendre et corriger les **failles de sécurité** liées au stockage des mots de passe.
* Implémenter une authentification sécurisée avec **`password_hash()`** et **`password_verify()`**.

## 🛠️ Prérequis

* Un environnement de développement local (Laragon, WAMP, MAMP...).
* Un SGBD (MySQL/MariaDB) et un outil d'administration (HeidiSQL, phpMyAdmin).
* Connaissances de base en HTML (formulaires) et PHP (variables, `$_POST`).



## 📂 Structure du Projet (Architecture MVC)

Notre application suit une architecture MVC simple pour bien séparer les rôles :

```
/tp_enc_mvc/
|
|-- index.php             # 1. Contrôleur Frontal (Routeur)
|                          #    Toutes les requêtes passent par lui.
|
|-- controller/
|   |-- etudiantController.php # 2. Contrôleur (Le cerveau)
|                          #    Contient la logique, appelle le Modèle et choisit la Vue.
|
|-- model/
|   |-- database.php      # 3. Modèle (Connexion BDD)
|                          #    Fonction de connexion PDO (getBdd()).
|   |-- etudiantModel.php # 4. Modèle (Logique BDD)
|                          #    Fonctions pour lire/écrire dans la table 'etudiant'.
|
|-- view/
|   |-- formulaireInscription.php # 5. Vues (L'affichage)
|   |-- formulaireLogin.php      #    Nos fichiers HTML/PHP pour l'interface.
|   |-- succesInscription.php   #
|   |-- ... (et autres vues)
```

-----
## 🚀 Progression des TPs

Ce projet est divisé en plusieurs TPs qui s'enchaînent logiquement.

### TP 1 : L'ossature MVC et la faille de sécurité (TP6)

1.  **Mise en place :** Création de la structure de dossiers MVC et de la base de données `enc_parcoursup`.
2.  **Développement :** Implémentation d'un formulaire d'inscription fonctionnel. L'utilisateur peut créer un compte.
3.  **Analyse (Bloc 3) :** On constate que les mots de passe sont stockés **en clair** dans la BDD ! C'est une faille de sécurité critique que nous corrigerons plus tard.

### TP 2 : Évolution de l'application (TP7)

1.  **Évolution 1 : Ajout d'un champ "Section"**
    * **Modèle :** On modifie la BDD (`ALTER TABLE`) et la fonction `inscrireEtudiant()` pour inclure la section.
    * **Vue :** On ajoute le menu déroulant `<select>` dans `formulaireInscription.php`.
    * **Contrôleur :** On récupère `$_POST['section']` dans `traiterInscription()` et on le passe au Modèle.

2.  **Évolution 2 : Ajout de la page de Connexion (Login)**
    * **Vue :** On crée les nouvelles vues (`formulaireLogin.php`, `succesConnexion.php`, etc.).
    * **Modèle :** On ajoute la fonction `getEtudiantByEmail()` pour récupérer un utilisateur.
    * **Contrôleur :** On crée les actions `afficherFormulaireLogin()` et `traiterConnexion()`.
    * **Routeur (`index.php`) :** On ajoute les `case 'login'` et `case 'connexion'` au `switch`.
    * **Constat :** La connexion fonctionne, mais elle compare les mots de passe en clair.

### TP 3 : Sécurisation de l'authentification (TP8)

1.  **Correction (Inscription) :** On modifie `inscrireEtudiant()` (Modèle) pour utiliser **`password_hash()`** avant l'insertion en BDD.
2.  **Analyse (Grain de sel) :** On inscrit deux utilisateurs avec le même mot de passe. On observe que les hashs en BDD sont **différents**. C'est le "grain de sel" (salt) qui garantit la sécurité.
3.  **Correction (Connexion) :** On modifie `traiterConnexion()` (Contrôleur) pour remplacer la comparaison `===` par **`password_verify()`**.
4.  **Résultat :** L'authentification est maintenant fonctionnelle ET sécurisée. 🔒

## 🚦 Démarrage

1.  **Base de Données :** Exécutez le script SQL du TP6 dans votre SGBD pour créer la base `enc_parcoursup` et la table `etudiant`.
2.  **Connexion :** Ouvrez `model/database.php` et vérifiez que les identifiants (`$user`, `$pass`) correspondent à votre configuration locale.
3.  **Lancement :** Placez le dossier `tp_enc_mvc` dans votre répertoire `www` (ou équivalent).
4.  **Accès :** Ouvrez votre navigateur et allez à `http://localhost/tp_enc_mvc/`.