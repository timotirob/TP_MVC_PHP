<?php
// Fichier : controller/etudiantController.php

// On inclut le modèle
require_once __DIR__ . '/../model/etudiantModel.php';

/**
 * Action : Affiche le formulaire d'inscription
 */
function afficherFormulaire()
{
    // Le contrôleur demande à la vue d'afficher le formulaire
    require_once __DIR__ . '/../view/formulaireInscription.php';
}

/**
 * Action : Traite les données du formulaire d'inscription
 */
/**
 * Action : Traite les données du formulaire d'inscription
 */
function traiterInscription()
{
    require_once __DIR__ .'/../services/CryptoService.php';
    // 1. Vérifier si les données POST existent (ajout de 'section')
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['section']) && isset($_POST['mot_de_passe'])) {
        
        // 2. Récupérer les données
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mdp = $_POST['mot_de_passe'];

        // CORRECTION "A FAIRE" (Etape 3.3) [cite: 94]
        $section = $_POST['section'];

        // Récupération du numéro sensible

        $numero_dossier_clair = $_POST['numero_dossier'] ;

        // chiffrer la données sensible

        $numero_dossier_chiffre = encryptData($numero_dossier_clair) ;

        // 3. Tenter d'inscrire l'étudiant (Appel au Modèle)
        try {
            // Le contrôleur demande au modèle d'inscrire l'étudiant
            
            // Appel modifié : on récupère l'ID (int) au lieu du succès (bool)
$newEtudiantId = inscrireEtudiant($nom, $prenom, $email, $section, $mdp, $numero_dossier_chiffre);

if ($newEtudiantId > 0) { // Si l'ID est supérieur à 0, c'est que ça a marché

    // DEBUG TEMPORAIRE
    echo "<h1>DEBUG</h1>";
    echo "ID Étudiant créé : " . $newEtudiantId . "<br>";
    echo "Fichier reçu ? ";
    var_dump($_FILES['fichier_medical']);
    
    // === GESTION DU FICHIER MÉDICAL (Nouveau) ===
    // On vérifie si un fichier a été envoyé sans erreur
    if (isset($_FILES['fichier_medical']) && $_FILES['fichier_medical']['error'] === 0) {
        
        $tmpName = $_FILES['fichier_medical']['tmp_name'];
        $originalName = $_FILES['fichier_medical']['name'];
        
        // 1. Lire le contenu brut du fichier temporaire
        $fileContent = file_get_contents($tmpName);

        // 2. Chiffrer (Hybride) via le service

        $cryptoData = chiffreFichierPourInfirmiere($fileContent);

        // 3. Enregistrer en BDD lié au nouvel étudiant
        ajouterDocumentSante(
            $newEtudiantId, 
            $originalName, 
            $cryptoData['content'], // Le fichier chiffré (AES)
            $cryptoData['key'],     // La clé AES chiffrée (RSA)
            $cryptoData['iv']       // L'IV
        );
    }
    else {
    echo "<br>Erreur Upload : Code erreur = " . $_FILES['fichier_medical']['error'];
        // Rappel codes erreurs : 1 = Fichier trop gros (php.ini), 4 = Pas de fichier envoyé
    }
    // ============================================

    require_once __DIR__ . '/../view/succesInscription.php';
    } else {
    require_once __DIR__ . '/../view/erreurInscription.php';
    }

        } catch (Exception $e) {
            // Si le modèle lève une exception (ex: email déjà utilisé),
            // on affiche la vue "erreur"
            require_once __DIR__ . '/../view/erreurInscription.php';
        }

    } else {
        // Si les données POST sont manquantes
        echo "Erreur : tous les champs du formulaire sont requis.";
    }
}
// NOUVELLES FONCTIONS AJOUTÉES

/**
 * Action : Affiche le formulaire de connexion
 */
function afficherFormulaireLogin()
{
    require_once __DIR__ . '/../view/formulaireLogin.php';
}

/**
 * Action : Traite les données du formulaire de connexion
 */
function traiterConnexion()
{
    require_once __DIR__ .'/../services/CryptoService.php';

    // 1. Vérifier si les données POST existent
    if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
        $email = $_POST['email'];
        $mdp_saisi = $_POST['mot_de_passe'];

        // 2. Demander au Modèle de trouver l'étudiant par email
        $etudiant = getEtudiantByEmail($email);

        // 3. Vérifier si l'étudiant existe
        if ($etudiant && password_verify($mdp_saisi, $etudiant['mot_de_passe'])) {
            
            // GESTION DES SESSIONS
            $_SESSION['user_id'] = $etudiant['id'] ;
            $_SESSION['user_email'] = $etudiant['email'] ;

            // on triche pour le rôle admin
            // Dans traiterConnexion(), remplacez la gestion des rôles par :
            if ($etudiant['email'] === 'admin@enc.fr') {
                $_SESSION['user_role'] = 'admin';
                header('Location: index.php?action=dashboard');
                exit ;
            } elseif ($etudiant['email'] === 'infirmiere@enc.fr') { // <-- NOUVEAU
                $_SESSION['user_role'] = 'infirmiere';
                header('Location: index.php?action=infirmerie'); // Redirection spécifique
                exit;
            } else {
                $_SESSION['user_role'] = 'etudiant';
                header('Location: index.php?action=dashboard');
                exit;
            }
            
        } else {
            // Aucun étudiant trouvé avec cet email
            require_once __DIR__ . '/../view/erreurConnexion.php';
        }
    } else {
        // Données POST manquantes
        echo "Erreur : email et mot de passe requis.";
    }
}

/**
 * ACTION : Affiche le tableau de bord (listing étudiants)
 * C'est ici qu'on utilise BOUCLES et CONDITIONS
 */
/**
 * ACTION : Affiche le tableau de bord (listing étudiants)
 */
function afficherDashboard()
{
    require_once __DIR__ . '/../services/CryptoService.php';

    // 1. Sécurité : vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_email'])) {
        header('Location: index.php?action=login'); 
        exit;
    }

    // 2. Récupérer les données de session
    $isAdmin = ($_SESSION['user_role'] === 'admin');
    $currentUserEmail = $_SESSION['user_email']; // Pour comparer dans la boucle

    $etudiants = getAllEtudiants(); 
    $etudiantsPourVue = []; 

    // 3. TRAITEMENT 
    foreach ($etudiants as $etudiant) {
        
        $email = $etudiant['email'];
        $dossier_a_afficher = "[ACCÈS REFUSÉ]"; // Valeur par défaut
        
        // <-- CORRECTION ICI : La condition de visibilité -->
        // On vérifie si l'utilisateur est admin OU s'il est le propriétaire de la ligne
        $isOwner = ($email === $currentUserEmail);

        if ($isAdmin || $isOwner) {
            // Droit de lecture accordé : on déchiffre
            $dossier_dechiffre = decryptData($etudiant['numero_dossier']);
            $dossier_a_afficher = $dossier_dechiffre ?? "[Erreur]";
        } 
        
        // On remplit le tableau pour la vue
        $etudiantsPourVue[] = [
            'email' => $email,
            'section' => $etudiant['section'],
            'dossier' => $dossier_a_afficher,
            'is_owner' => $isOwner // (Optionnel) Utile si on veut mettre du gras en CSS
        ];
    }

    require_once __DIR__ . '/../view/adminDashboard.php';
}

/**
 * Affiche le dashboard spécifique infirmerie
 */
function afficherDashboardInfirmiere()
{
    // Sécurité : Seule l'infirmière passe
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'infirmiere') {
        die("Accès INTERDIT : Réservé au personnel médical.");
    }

    $documents = getAllDocumentsSante(); // Appel au modèle
    require_once __DIR__ . '/../view/infirmiereDashboard.php';
}

/**
 * Gère le déchiffrement et le téléchargement
 */
function telechargerDocument()
{
    require_once __DIR__ . '/../services/CryptoService.php';

    // 1. Sécurité
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'infirmiere') {
        die("Accès INTERDIT.");
    }

    if (!isset($_GET['doc_id'])) die("ID manquant.");
    $id = (int)$_GET['doc_id'];

    // 2. Récupération des données chiffrées (Modèle)
    $doc = getDocumentSanteById($id);
    if (!$doc) die("Document introuvable.");

    // 3. Déchiffrement (Service Hybride)
    // On passe le BLOB chiffré, la CLÉ chiffrée (RSA) et l'IV
    $contenuClair = dechiffreFichierPourInfirmiere(
        $doc['contenu_chiffre'], 
        $doc['cle_session_chiffree'], 
        $doc['iv_fichier']
    );

    if ($contenuClair === null) {
        die("Erreur : Impossible de déchiffrer. Vérifiez la clé privée.");
    }

    // 4. Envoi du fichier au navigateur (Headers HTTP)
    // On force le téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="DEC_' . $doc['nom_fichier_origine'] . '"');
    header('Content-Length: ' . strlen($contenuClair));
    
    echo $contenuClair;
    exit;
}