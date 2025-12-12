<?php
// Fichier : services/CryptoService.php

/**
 * Fichier de configuration pour le chiffrement.
 * * ATTENTION : Pour ce TP, la clé est "en dur".
 * En production, elle doit être dans un fichier .env (hors Git)
 * et chargée de manière sécurisée.
 */
define('ENCRYPTION_KEY', 'une-super-cle-secrete-de-32-octets'); // 32 octets = 256 bits
define('ENCRYPTION_CIPHER', 'aes-256-cbc'); // Le même algo que votre TP sur OpenSSL

// --- CHEMINS DES CLES ASYMETRIQUES (RSA) ---
// On pointe vers la racine du projet (d'où le /../)
define('PATH_PUBLIC_KEY', __DIR__ . '/../id_rsa_infirmerie_php.pem'); // La version convertie !
define('PATH_PRIVATE_KEY', __DIR__ . '/../id_rsa_infirmerie');

/**
 * Chiffre une donnée avec AES-256-CBC.
 * Stocke l'IV (Vecteur d'Initialisation) avec le chiffré.
 */
function encryptData(string $plaintext): string
{
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_CIPHER);
    $iv = openssl_random_pseudo_bytes($iv_length);

    $ciphertext = openssl_encrypt(
        $plaintext,
        ENCRYPTION_CIPHER,
        ENCRYPTION_KEY,
        OPENSSL_RAW_DATA, // Important pour la concaténation
        $iv
    );

    // On stocke l'IV (non secret) avec le chiffré (secret)
    // On encode le tout en Base64 pour le stocker en BDD
    return base64_encode($iv . $ciphertext);
}

/**
 * Déchiffre une donnée chiffrée par encryptData()
 */
function decryptData(string $encryptedBase64): ?string
{
    // 1. Décoder le Base64
    $data = base64_decode($encryptedBase64);
    if ($data === false) {
        return null;
    }

    // 2. Extraire l'IV
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_CIPHER);
    $iv = substr($data, 0, $iv_length);

    // 3. Extraire le chiffré (le reste)
    $ciphertext = substr($data, $iv_length);

    // 4. Déchiffrer
    $plaintext = openssl_decrypt(
        $ciphertext,
        ENCRYPTION_CIPHER,
        ENCRYPTION_KEY,
        OPENSSL_RAW_DATA,
        $iv
    );

    return ($plaintext === false) ? null : $plaintext;
}

// ==========================================================
// CHIFFREMENT HYBRIDE (FICHIERS)
// ==========================================================

/**
 * Chiffre un fichier pour l'infirmière.
 * 1. Génère une clé AES jetable.
 * 2. Chiffre le fichier en AES.
 * 3. Chiffre la clé AES avec la clé Publique RSA (PEM).
 */
function chiffreFichierPourInfirmiere(string $fileContent): array
{
    // A. Charger la clé publique PEM
    if (!file_exists(PATH_PUBLIC_KEY)) {
        throw new Exception("Erreur config : Clé publique introuvable (" . PATH_PUBLIC_KEY . ")");
    }
    $publicKey = file_get_contents(PATH_PUBLIC_KEY);

    // B. Générer la clé de session (AES)
    $sessionKey = openssl_random_pseudo_bytes(32);
    
    // C. Chiffrement Symétrique du contenu
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_CIPHER);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $contenuChiffre = openssl_encrypt(
        $fileContent, 
        ENCRYPTION_CIPHER, 
        $sessionKey, 
        OPENSSL_RAW_DATA, 
        $iv);

    // D. Chiffrement Asymétrique de la clé de session
    // Note : On gère le cas où la clé publique serait invalide
    if (!openssl_public_encrypt($sessionKey, $encryptedSessionKey, $publicKey)) {
        throw new Exception("Erreur chiffrement RSA : Vérifiez le format de la clé publique (PEM requis).");
    }

    return [
        'content' => $contenuChiffre,              // Blob (AES)
        'key'     => base64_encode($encryptedSessionKey), // Clé (RSA)
        'iv'      => base64_encode($iv)            // IV (Clair)
    ];
}

/**
 * Déchiffre un fichier (Réservé Infirmière).
 */
function dechiffreFichierPourInfirmiere(string $contenuChiffre, string $encryptedSessionKeyBase64, string $ivBase64): ?string
{
    // A. Charger la clé privée
    if (!file_exists(PATH_PRIVATE_KEY)) {
        // En prod, on loggerait l'erreur, ici on retourne null
        return null; 
    }
    $privateKey = file_get_contents(PATH_PRIVATE_KEY);
    
    // B. Déchiffrer la clé de session (RSA)
    $decodedKey = base64_decode($encryptedSessionKeyBase64);
    $success = openssl_private_decrypt($decodedKey, $sessionKey, $privateKey);
    
    if (!$success) return null; // Clé privée incorrecte ou passphrase manquante

    // C. Déchiffrer le contenu (AES)
    $iv = base64_decode($ivBase64);
    return openssl_decrypt($contenuChiffre, ENCRYPTION_CIPHER, $sessionKey, OPENSSL_RAW_DATA, $iv);
}