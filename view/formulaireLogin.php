<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-T">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ENC</title>
    <style> /* (Même style que le formulaire d'inscription) */
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; cursor: pointer; }
        /* Ajoute ce code DANS la balise <style> */
        nav { background: #f4f4f4; padding: 10px; text-align: center; margin-bottom: 20px; border-radius: 5px; }
        nav a { margin: 0 15px; text-decoration: none; font-weight: bold; color: #007bff; font-size: 1.1em; }
        nav a:hover, nav a.active { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php?action=accueil">Enregistrement</a>
        <a href="index.php?action=login" class="active">Connexion</a>
    </nav>
    <h1>Connexion à votre espace</h1>

    <form action="index.php?action=connexion" method="POST">
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mot_de_passe" required>
        </div>
        <div>
            <button type="submit">Se connecter</button>
        </div>
    </form>
    <p>Pas encore de compte ? <a href="index.php">Inscrivez-vous</a>.</p>
</body>
</html>
