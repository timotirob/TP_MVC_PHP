<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription ENC - Parcoursup</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        /* Ajoute ce code DANS la balise <style> */
        nav { background: #f4f4f4; padding: 10px; text-align: center; margin-bottom: 20px; border-radius: 5px; }
        nav a { margin: 0 15px; text-decoration: none; font-weight: bold; color: #007bff; font-size: 1.1em; }
        nav a:hover, nav a.active { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php?action=accueil" class="active">Enregistrement</a>
        <a href="index.php?action=login">Connexion</a>
    </nav>
    <h1>Inscription administrative - ENC</h1>
    <p>Veuillez finaliser votre inscription en créant votre compte étudiant.</p>

    <form action="index.php?action=inscrire" method="POST">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>


        <div>
            <label for="numero_dossier">Nulmero de dossier:</label>
            <input type="text" id="numero_dossier" name="numero_dossier" required>
        </div>

        <div>
            <label for="section">Section :</label>
            <select id="section" name="section" required>
                <option value="">-- Choisissez une section --</option>
                <option value="SIO1-SLAM">SIO1 SLAM</option>
                <option value="SIO1-SISR">SIO1 SISR</option>
                <option value="SIO2-SLAM">SIO2 SLAM</option>
                <option value="SIO2-SISR">SIO2 SISR</option>
                <option value="CG1">CG1</option>
                <option value="CG2">CG2</option>
            </select>
        </div>

        <div>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mot_de_passe" required>
        </div>
        <div>
            <button type="submit">Finaliser l'inscription</button>
        </div>
    </form>
</body>
</html>