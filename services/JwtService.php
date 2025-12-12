<?php

// Fichier : services/JwtService.php

define('JWT_SECRET', 'les_sio1_ne_s@vent_rien_ça_va_changer_avec_Robert');

/**
 * Encode en Base64 compatible URL (sans +, / et =)
 */
function base64UrlEncode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Génère un Token JWT
 */
function createJwt(array $payload): string
{
    // 1. Header (Algorithme)
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $base64Header = base64UrlEncode($header);

    // 2. Payload (Données + Expiration)
    // On ajoute 'iat' (issued at) et 'exp' (expiration)
    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60); // Expire dans 1 heure
    $base64Payload = base64UrlEncode(json_encode($payload));

    // 3. Signature
    $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, JWT_SECRET, true);
    $base64Signature = base64UrlEncode($signature);

    // 4. Assemblage
    return $base64Header . "." . $base64Payload . "." . $base64Signature;
}

/**
 * Vérifie un Token JWT et retourne le payload si valide
 */
function verifyJwt(string $token): ?array
{
    // 1. Découpage
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $signature] = $parts;

    // 2. Vérification de la signature
    $validSignature = hash_hmac('sha256', $header . "." . $payload, JWT_SECRET, true);
    $base64ValidSignature = base64UrlEncode($validSignature);

    if (!hash_equals($base64ValidSignature, $signature)) {
        return null; // Signature invalide (Token falsifié)
    }

    // 3. Vérification de l'expiration
    $decodedPayload = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
    if ($decodedPayload['exp'] < time()) {
        return null; // Token expiré
    }

    return $decodedPayload;
}