# Projet : Application d'Inscription (PHP & MVC)

Ce projet est un exercice d'introduction à la création d'une application web PHP moderne. En partant d'une structure simple, nous construirons une application d'inscription et de connexion en respectant l'architecture **Modèle-Vue-Contrôleur (MVC)**, en nous connectant à une base de données avec **PDO** et en implémentant les **bonnes pratiques de sécurité** (Hachage et Chiffrement).

## 🎯 Contexte

Vous êtes missionné par l'ENC pour développer le module d'inscription finale des nouveaux étudiants après Parcoursup. Ce module doit permettre aux étudiants de créer un compte.

**Nouveauté (TP 4) :** La direction exige désormais que le **Numéro de Dossier Confidentiel** soit stocké de manière sécurisée et accessible uniquement via un espace d'administration. Contrairement au mot de passe qui est haché (irréversible), ce numéro doit être **chiffré** (réversible pour les autorisés).

## 📚 Objectifs Pédagogiques

À la fin de ces TPs, vous saurez :
* Structurer une application en **Modèle-Vue-Contrôleur (MVC)**.
* Séparer les responsabilités : logique (Contrôleur), données (Modèle), affichage (Vue) et **Services**.
* Vous connecter à une base de données **MySQL avec PDO** et utiliser des **requêtes préparées**.
* Comprendre la différence entre **Hachage** (mot de passe) et **Chiffrement** (données sensibles).
* Implémenter le chiffrement symétrique avec **OpenSSL** (`aes-256-cbc`).
* Gérer des **sessions utilisateurs** et des **rôles** (Admin vs Étudiant).

## 🛠️ Prérequis

* Un environnement de développement local (Laragon, WAMP, MAMP...).
* Un SGBD (MySQL/MariaDB) et un outil d'administration (HeidiSQL, phpMyAdmin).
* Connaissances de base en HTML, PHP (variables, `$_POST`, `$_SESSION`).

## 📂 Structure du Projet (Architecture MVC)

Notre application suit une architecture MVC améliorée avec une couche de **Services** :

```text
/tp_enc_mvc/
|
|-- index.php             # 1. Contrôleur Frontal (Routeur)
|                          #    Point d'entrée, gestion des routes et session_start().
|
|-- controller/
|   |-- etudiantController.php # 2. Contrôleur (Le chef d'orchestre)
|                          #    Reçoit la demande, appelle les Services/Modèles, choisit la Vue.
|
|-- model/
|   |-- database.php      # 3. Modèle (Connexion BDD)
|   |-- etudiantModel.php # 4. Modèle (Accès Données)
|                          #    CRUD pour la table 'etudiant'.
|
|-- services/             # <--- (TP 4)
|   |-- CryptoService.php # 5. Service Transverse 
|                          #    Contient la logique de chiffrement/déchiffrement.
|
|-- view/
|   |-- formulaireInscription.php
|   |-- formulaireLogin.php
|   |-- adminDashboard.php    # <--- (TP 4) 
|   |-- succesInscription.php