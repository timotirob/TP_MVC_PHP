<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Infirmerie - Documents Confidentiels</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8d7da; color: #721c24; } /* Ton rouge "médical" */
        .btn-download {
            background-color: #dc3545; color: white; padding: 8px 12px; 
            text-decoration: none; border-radius: 4px; font-size: 0.9em;
        }
        .btn-download:hover { background-color: #c82333; }
        nav { background: #eee; padding: 10px; margin-bottom: 20px; border-radius: 5px;}
    </style>
</head>
<body>
    <nav>
        Espace <strong>INFIRMERIE</strong> | Connecté en tant que : <?= htmlspecialchars($_SESSION['user_email']) ?>
        <a href="index.php?action=logout" style="float:right">Se déconnecter</a>
    </nav>

    <h1>Documents Médicaux Reçus</h1>

    <?php if (empty($documents)): ?>
        <p>Aucun document médical n'a été uploadé pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Étudiant</th>
                    <th>Fichier Original</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($doc['date_ajout'])) ?></td>
                    <td>
                        <?= htmlspecialchars($doc['nom'] . ' ' . $doc['prenom']) ?><br>
                        <small><?= htmlspecialchars($doc['email']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($doc['nom_fichier_origine']) ?></td>
                    <td>
                        <a href="index.php?action=telecharger_medical&doc_id=<?= $doc['id'] ?>" class="btn-download">
                            🔒 Déchiffrer & Télécharger
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>