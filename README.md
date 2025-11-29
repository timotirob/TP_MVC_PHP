# Projet : Application d'Inscription (PHP & MVC)

Ce projet est un exercice d'introduction à la création d'une application web PHP moderne. En partant d'une structure simple, nous construirons une application d'inscription et de connexion en respectant l'architecture **Modèle-Vue-Contrôleur (MVC)**, en nous connectant à une base de données avec **PDO** et en implémentant les **bonnes pratiques de sécurité** (Hachage et Chiffrement).

## 🎯 Contexte

Vous êtes missionné par l'ENC pour développer le module d'inscription finale des nouveaux étudiants après Parcoursup. Ce module doit permettre aux étudiants de créer un compte.

**Nouveauté (TP 4) :** La direction exige désormais que le **Numéro de Dossier Confidentiel** soit stocké de manière sécurisée et accessible uniquement via un espace d'administration. Contrairement au mot de passe qui est haché (irréversible), ce numéro doit être **chiffré** (réversible pour les autorisés).

## 📚 Objectifs 

À la fin de ces TPs, vous saurez :
* Structurer une application en **Modèle-Vue-Contrôleur (MVC)**.
* Séparer les responsabilités : logique (Contrôleur), données (Modèle), affichage (Vue) et **Services**.
* Vous connecter à une base de données **MySQL avec PDO** et utiliser des **requêtes préparées**.
* Comprendre la différence entre **Hachage** (mot de passe) et **Chiffrement** (données sensibles).
* Implémenter une authentification sécurisée avec **`password_hash()`** et **`password_verify()`**.
* Gérer des **sessions utilisateurs** et des **rôles** (Admin vs Étudiant).
* Mettre en œuvre le **Chiffrement Symétrique (AES)** pour les données sensibles.
* Mettre en œuvre le **Chiffrement Hybride (AES/RSA)** pour les documents volumineux.
* Gérer l'accès aux données chiffrées en fonction des **rôles** (Admin, Étudiant, Infirmière).

## 🛠️ Prérequis

* Un environnement de développement local (Laragon, WAMP, MAMP...).
* Un SGBD (MySQL/MariaDB) et un outil d'administration (HeidiSQL, phpMyAdmin).
* Connaissances de base en HTML, PHP (variables, `$_POST`, `$_SESSION`).

## 📂 Structure du Projet (Architecture MVC)

Notre application suit une architecture MVC améliorée avec une couche de **Services** :

```text
/tp_enc_mvc/
|
|-- id_rsa_infirmerie          # 🔑 Clé Privée (Sert à déchiffrer - Simule le poste Infirmière)
|-- id_rsa_infirmerie.pub      # 🔑 Clé Publique (Format SSH - Pour Git/Github)
|-- id_rsa_infirmerie_php.pem  # 🔑 Clé Publique (Format PEM - Convertie pour PHP OpenSSL)
|-- setup_keys.php             # 🛠️ Script utilitaire pour générer/convertir les clés ci-dessus.
|
|-- index.php                  # 1. Contrôleur Frontal (Routeur)
|                              #    Gère désormais les routes 'infirmerie' et 'telecharger_medical'.
|
|-- controller/
|   |-- etudiantController.php # 2. Contrôleur (Le chef d'orchestre)
|                              #    Gère Inscription, Auth, Dashboard Admin et Dashboard Infirmière.
|
|-- model/
|   |-- database.php           # 3. Modèle (Connexion BDD)
|   |-- etudiantModel.php      # 4. Modèle (Accès Données)
|                              #    Gère les tables 'etudiant' ET 'document_sante' (BLOBs).
|
|-- services/                  # <--- (TP 4 & 5)
|   |-- CryptoService.php      # 5. Service Transverse
|                              #    Contient :
|                              #    - encryptData (AES - Symétrique)
|                              #    - chiffreFichierPourInfirmiere (AES+RSA - Hybride)
|
|-- view/
|   |-- formulaireInscription.php # (Mis à jour avec upload de fichier)
|   |-- formulaireLogin.php
|   |-- adminDashboard.php        # Vue Admin (Voir les n° dossiers)
|   |-- infirmiereDashboard.php   # <--- NOUVEAU (Vue Infirmière : liste & téléchargement)
|   |-- succesInscription.php
|   |-- erreurInscription.php
|   |-- erreurConnexion.php

---
```

## 📜 Historique des TPs Réalisés

### TP 1 & 2 : Base MVC & Connexion 

* **Structure MVC :** Le projet est structuré en `controller/`, `model/`, `view/` et `services/`.
* **Routage :** Le contrôleur frontal (`index.php`) gère les routes (`accueil`, `inscrire`, `login`, `connexion`).
* **Modèle :** `etudiantModel.php` gère l'accès aux données.
* **Vues :** `formulaireInscription.php` et `formulaireLogin.php` gèrent l'affichage.

### TP 3 : Sécurisation de l'authentification 

* **Hachage (Inscription) :** Utilisation de `password_hash()` pour le stockage sécurisé du mot de passe.
* **Vérification (Connexion) :** Utilisation de `password_verify()` dans `traiterConnexion()` pour l'authentification sécurisée.

### TP 4 : Information Sensible (Suite) 🔒

Le module a été étendu pour gérer le Numéro de Dossier et le Fichier Médical.

#### Partie A : Numéro de Dossier
* **Chiffrement (Numéro) :** Le champ `numero_dossier` est stocké chiffré symétriquement (AES-256-CBC) en BDD via `CryptoService::encryptData()`.
* **Rôles (Dashboard) :** Le `dashboard` affiche tous les étudiants.
    * L'**Admin** déchiffre le numéro de dossier via `CryptoService::decryptData()` et l'affiche.
    * L'**Étudiant** voit la mention `[ACCÈS REFUSÉ]`.

#### Partie B : Document Médical (Chiffrement Hybride)
* **Architecture Hybride :** Implémentation du chiffrement Hybride pour les fichiers :
    1. Le fichier est chiffré avec une clé de session **AES-256** unique.
    2. Cette clé de session est ensuite chiffrée avec la **clé publique RSA** de l'infirmière.
    3. Les données (contenu chiffré, clé chiffrée RSA, IV) sont stockées dans la table `document_sante`.
* **Espace Infirmier :**
    * Ajout du rôle `infirmiere` et de la route `infirmerie`.
    * La vue `view/infirmiereDashboard.php` liste les documents.
    * L'action `telecharger_medical` déclenche le **déchiffrement Hybride** à l'aide de la **clé privée RSA** de l'infirmerie pour récupérer la clé AES, puis déchiffrer le fichier. Le document clair est ensuite téléchargé par le navigateur.