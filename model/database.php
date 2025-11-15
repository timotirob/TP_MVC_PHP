<?php
// Fichier : model/database.php

/**
 * Retourne une connexion à la base de données
 * @return PDO
 */
function getBdd(): PDO
{
    // Informations de connexion
    $host = 'localhost';
    $dbname = 'enc_parcoursup'; // Le nom de la BDD créée à l'étape 1
    $user = 'root';
    $pass = ''; // Mettez votre mot de passe root (souvent vide sur Laragon/WAMP)

    try {
        $bdd = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les erreurs SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Retourne les résultats en tableau associatif
            ]
        );
        return $bdd;

    } catch (Exception $e) {
        // En cas d'erreur de connexion, on arrête tout et on affiche l'erreur
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}