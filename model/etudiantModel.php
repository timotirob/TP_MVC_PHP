<?php
// Fichier : model/etudiantModel.php

// On inclut le fichier de connexion à la BDD
require_once 'database.php';

/**
 * Enregistre un nouvel étudiant dans la base de données
 *
 * @param string $nom Le nom de l'étudiant
 * @param string $prenom Le prénom de l'étudiant
 * @param string $email L'email de l'étudiant
 * @param string $section La section de l'étudiant (ajouté)
 * @param string $mdp Le mot de passe (en clair pour l'instant)
 *
 * @return bool Vrai si l'inscription a réussi, faux sinon
 */

// CORRECTION "A FAIRE" (Etape 4.3) : signature modifiée 
function inscrireEtudiant(string $nom, string $prenom, string $email, string $section, string $mdp): bool
{
    // 1. Récupérer la connexion à la BDD
    $bdd = getBdd();

    // 2. Préparer la requête d'insertion

    $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);

    // CORRECTION "A FAIRE" (Etape 4.4) : requête modifiée
    $requete = $bdd->prepare('
        INSERT INTO etudiant (nom, prenom, email, section, mot_de_passe)
        VALUES (?, ?, ?, ?, ?)
    ');

    // 3. Exécuter la requête en passant les valeurs
    // L'ordre doit correspondre aux '?' ci-dessus
    // (Comme indiqué Etape 4.5 du TP [cite: 120])

    $succes = $requete->execute([$nom, $prenom, $email, $section, $mdp_hache]);

    return $succes;
}

// NOUVELLE FONCTION AJOUTÉE
/**
 * Récupère un étudiant en fonction de son email.
 *
 * @param string $email L'email de l'étudiant à trouver
 * @return array|false Un tableau contenant les infos de l'étudiant, ou 'false' s'il n'est pas trouvé.
 */
function getEtudiantByEmail(string $email): array|false
{
    $bdd = getBdd();

    // On prépare une requête SELECT pour chercher l'étudiant
    $requete = $bdd->prepare('SELECT * FROM etudiant WHERE email = ?');
    $requete->execute([$email]);

    // fetch() récupère le premier (et unique) résultat
    $etudiant = $requete->fetch(); 
    
    return $etudiant;
}