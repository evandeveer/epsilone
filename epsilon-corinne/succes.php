<?php
$filename = isset($_GET['file']) ? htmlspecialchars($_GET['file'], ENT_QUOTES, 'UTF-8') : '';
if (!$filename) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichier envoyé</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-card { text-align: center; }
        .check { font-size: 3.5rem; margin-bottom: 1rem; }
        .success-name {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 8px;
            padding: 0.4rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0.75rem 0 1.5rem;
            word-break: break-all;
        }
        .btn-back {
            display: inline-block;
            padding: 0.7rem 1.8rem;
            background: #3b82f6;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .btn-back:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="upload-card success-card">
            <div class="check">✅</div>
            <h1>Fichier déposé avec succès !</h1>
            <p class="subtitle">Votre fichier a bien été enregistré sur le serveur.</p>
            <span class="success-name"><?= $filename ?></span>
            <br>
            <a href="index.html" class="btn-back">Déposer un autre fichier</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
