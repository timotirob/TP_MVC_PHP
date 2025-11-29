<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - ENC</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        
        /* Styles pour la confidentialité */
        .confidential { color: #dc3545; font-weight: bold; } /* Rouge pour les données sensibles */
        .locked { color: #6c757d; font-style: italic; }      /* Gris pour accès refusé */
        
        /* Surbrillance de ma propre ligne */
        tr.own-row { background-color: #e8f4ff; border: 2px solid #007bff; }
        
        nav { margin-bottom: 20px; background: #eee; padding: 10px; border-radius: 5px;}
    </style>
</head>
<body>
    <nav>
        Bonjour, <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong>
        (Rôle : <?= $_SESSION['user_role'] ?>) - 
        <a href="index.php?action=logout">Se déconnecter</a>
    </nav>

    <h1>Tableau de bord</h1>
    
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <p>Vue Administrateur : Accès complet aux données chiffrées.</p>
    <?php else: ?>
        <p>Vue Étudiant : Vous ne pouvez voir que votre propre numéro de dossier.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Section</th>
                <th>Numéro de Dossier (Donnée Chiffrée)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($etudiantsPourVue as $ligne): ?>
            
            <tr class="<?= ($ligne['is_owner']) ? 'own-row' : '' ?>">
                
                <td><?= htmlspecialchars($ligne['email']) ?></td>
                <td><?= htmlspecialchars($ligne['section']) ?></td>
                
                <?php if ($ligne['dossier'] === "[ACCÈS REFUSÉ]"): ?>
                    <td class="locked"><?= htmlspecialchars($ligne['dossier']) ?></td>
                <?php else: ?>
                    <td class="confidential"><?= htmlspecialchars($ligne['dossier']) ?></td>
                <?php endif; ?>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>