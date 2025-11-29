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