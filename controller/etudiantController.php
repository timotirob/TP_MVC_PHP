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
    // 1. Vérifier si les données POST existent (ajout de 'section')
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['section']) && isset($_POST['mot_de_passe'])) {
        
        // 2. Récupérer les données
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mdp = $_POST['mot_de_passe'];

        // CORRECTION "A FAIRE" (Etape 3.3) [cite: 94]
        $section = $_POST['section'];

        // 3. Tenter d'inscrire l'étudiant (Appel au Modèle)
        try {
            // Le contrôleur demande au modèle d'inscrire l'étudiant
            
            // CORRECTION "A FAIRE" (Etape 3.4) [cite: 98]
            $succes = inscrireEtudiant($nom, $prenom, $email, $section, $mdp);

            // 4. Afficher la vue correspondante
            if ($succes) {
                // Si le modèle dit que c'est OK, on affiche la vue "succès"
                require_once __DIR__ . '/../view/succesInscription.php';
            } else {
                // Si le modèle dit que ça n'a pas marché (cas rare ici)
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
    // 1. Vérifier si les données POST existent
    if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
        $email = $_POST['email'];
        $mdp_saisi = $_POST['mot_de_passe'];

        // 2. Demander au Modèle de trouver l'étudiant par email
        $etudiant = getEtudiantByEmail($email);

        // 3. Vérifier si l'étudiant existe
        if ($etudiant) {
            
            // ATTENTION : FAILLE DE SÉCURITÉ VOLONTAIRE POUR LE TP
            if (password_verify($mdp_saisi, $etudiant['mot_de_passe'])) {
                // Mot de passe correct !
                require_once __DIR__ . '/../view/succesConnexion.php';
            } else {
                // Mauvais mot de passe
                require_once __DIR__ . '/../view/erreurConnexion.php';
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