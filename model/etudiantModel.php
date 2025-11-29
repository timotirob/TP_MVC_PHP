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
function inscrireEtudiant(string $nom, string $prenom, string $email, string $section, string $mdp, string $numero_dossier_chiffre): int
{
    // 1. Récupérer la connexion à la BDD
    $bdd = getBdd();

    // 2. Préparer la requête d'insertion

    $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);

    // CORRECTION "A FAIRE" (Etape 4.4) : requête modifiée
    $requete = $bdd->prepare('
        INSERT INTO etudiant (nom, prenom, email, section, mot_de_passe, numero_dossier)
        VALUES (?, ?, ?, ?, ?, ?)
    ');

    // 3. Exécuter la requête en passant les valeurs
    // L'ordre doit correspondre aux '?' ci-dessus
    // (Comme indiqué Etape 4.5 du TP [cite: 120])

    $succes = $requete->execute([$nom, $prenom, $email, $section, $mdp_hache, $numero_dossier_chiffre]);


    if ($succes) {
        // NOUVEAU : On retourne l'ID créé par la base de données
        return (int)$bdd->lastInsertId();
    } else {
        return 0; // 0 indique une erreur
    }

    // return $succes;
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

// Récupération de tous les étudiants de la BDD

function getAllEtudiants(): array {
    $bdd = getBdd();
    $requete = $bdd->query('SELECT email, section, numero_dossier FROM etudiant');
    return $requete->fetchAll() ;
}

// Fichier : model/etudiantModel.php (suite)

/**
 * Enregistre les métadonnées de chiffrement hybride pour un fichier
 */
function ajouterDocumentSante(int $idEtudiant, string $nomFichier, string $contenuChiffre, string $cleChiffree, string $iv): bool
{
    $bdd = getBdd();
    
    // Note : contenu_chiffre doit être un BLOB ou LONGBLOB dans la BDD
    $requete = $bdd->prepare('
        INSERT INTO document_sante 
        (id_etudiant, nom_fichier_origine, contenu_chiffre, cle_session_chiffree, iv_fichier)
        VALUES (?, ?, ?, ?, ?)
    ');

    return $requete->execute([
        $idEtudiant, 
        $nomFichier, 
        $contenuChiffre, 
        $cleChiffree, 
        $iv
    ]);
}

/**
 * Récupère la liste de tous les documents de santé avec le nom de l'étudiant associé
 */
function getAllDocumentsSante(): array
{
    $bdd = getBdd();
    // Jointure pour savoir "Qui" a envoyé le fichier
    $sql = 'SELECT d.id, d.nom_fichier_origine, d.date_ajout, e.nom, e.prenom, e.email 
            FROM document_sante d
            JOIN etudiant e ON d.id_etudiant = e.id
            ORDER BY d.date_ajout DESC';
    return $bdd->query($sql)->fetchAll();
}

/**
 * Récupère les données chiffrées d'un document spécifique
 */
function getDocumentSanteById(int $id): array|false
{
    $bdd = getBdd();
    $stmt = $bdd->prepare('SELECT * FROM document_sante WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}