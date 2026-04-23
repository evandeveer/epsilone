<?php
include('header.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader un fichier</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Envoyer un document</h1>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
        
        <label for="mon_fichier" class="input-red">Déposer votre fichier :</label><br>
        <input type="file" class="input-red" name="mon_fichier" id="mon_fichier"><br><br>
        
        <button type="submit">Envoyer</button>
        
    </form>
</body>
</html>

<?php
include('footer.php');
?>