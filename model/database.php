<?php
// Fichier : model/database.php

/**
 * Retourne une connexion à la base de données
 * @return PDO
 */

function getBdd() {
    // 1. On tente de lire les variables d'environnement (Config Serveur)
    // Si elles n'existent pas (?:), on prend les valeurs locales (Laragon)

    // Configuration Base de Données
    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'enc_parcoursup';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    // Configuration JWT (Secret)
    // On définit la constante seulement si elle n'existe pas déjà
    if (!defined('JWT_SECRET')) {
        $secret = getenv('JWT_SECRET') ?: 'ma_cle_secrete_locale_pour_le_dev_a_changer';
        define('JWT_SECRET', $secret);
    }

    try {
        $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdd;
    } catch (Exception $e) {
        // En production, on évite d'afficher $e->getMessage() directement aux utilisateurs
        // Mais pour ce TP, on le garde pour le débogage.
        die('Erreur BDD : ' . $e->getMessage());
    }
}