<?php
// Fichier : index.php (Le Contrôleur Frontal)
// Gestion des sessions pour naviguer de page en page
session_start() ;

// On inclut le contrôleur des étudiants
require_once 'controller/etudiantController.php';

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

    case 'logout':
        session_destroy() ;
        header('Location: index.php?action=login');
        exit ;
    
    case 'connexion':
        // Traite la tentative de connexion
        traiterConnexion();
        break;
    // FIN NOUVEAUX CAS
    case 'accueil':
    default:
        // Par défaut, ou si l'action est 'accueil', on appelle afficherFormulaire()
        afficherFormulaire();
        break;
}