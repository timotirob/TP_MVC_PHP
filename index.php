<?php
// Fichier : index.php (Le Contrôleur Frontal)
// Gestion des sessions pour naviguer de page en page
// session_start() ;

require_once 'services/JwtService.php';
require_once 'controller/etudiantController.php';
// --- MIDDLEWARE D'AUTHENTIFICATION JWT ---
// On initialise $_SESSION vide (pour simuler l'ancien comportement)
$_SESSION = [];

if (isset($_COOKIE['auth_token'])) {
    $payload = verifyJwt($_COOKIE['auth_token']);
    if ($payload) {
        // Le token est valide, on "hydrate" la session virtuelle
        $_SESSION['user_id'] = $payload['user_id'];
        $_SESSION['user_email'] = $payload['user_email'];
        $_SESSION['user_role'] = $payload['role'];
    }
}

// On inclut le contrôleur des étudiants


// --- ROUTAGE ---
// On analyse l'action demandée par l'utilisateur (via l'URL)

// 1. Récupérer l'action de l'URL (paramètre 'action')
// S'il n'y a pas d'action, on met 'accueil' par défaut
$action = $_GET['action'] ?? 'accueil';

// 2. Utiliser un 'switch' pour appeler la bonne fonction (l'action)
switch ($action) {

    case 'inscrire':
        // Si l'action est 'inscrire', on appelle la fonction traiterInscription()
        traiterInscription();
        break;
// NOUVEAUX CAS
    case 'login':
        // Affiche le formulaire de connexion
        afficherFormulaireLogin();
        break;
    
    case 'dashboard':
        afficherDashboard();
        break ;

//    case 'logout':
//        session_destroy() ;
//        header('Location: index.php?action=login');
//        exit ;
    case 'logout':
        // 1. On tue le cookie en le périmant dans le passé
        setcookie('auth_token', '', time() - 3600, '/', '', false, true);
        // 2. On vide la variable pour la suite du script (au cas où)
        $_SESSION = [];
        // 3. Redirection
        header('Location: index.php?action=login');
        exit;
    
    case 'connexion':
        // Traite la tentative de connexion
        traiterConnexion();
        break;
    // --- NOUVELLES ROUTES INFIRMIERES ---

    case 'infirmerie': 
        // Affiche la liste des documents médicaux
        afficherDashboardInfirmiere();
        break;

    case 'telecharger_medical':
        // Action de déchiffrement et téléchargement
        telechargerDocument();
        break;

    case 'accueil':
    default:
        // Par défaut, ou si l'action est 'accueil', on appelle afficherFormulaire()
        afficherFormulaire();
        break;
}