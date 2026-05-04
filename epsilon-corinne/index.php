<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de fichiers</title>
    <link rel="stylesheet" href="style.css">
</head> 
<body>
    <div class="container">
    <?php 
    include 'header.html'; 
    ?>
            <form id="telechargerFichier" action="telecharger.php" method="POST" enctype="multipart/form-data">
                <div class="drop-zone" id="dropZone">
                    <input type="file" id="fichierInput" name="fichier" accept=".pdf,.jpg,.png" hidden>
                    <div class="drop-content" id="dropContent">
                        <p class="drop-text">Glissez votre fichier ici</p>
                        <p class="drop-or">ou</p>
                        <button type="button" class="btn-browse" id="btnBrowse">
                            Parcourir les documents
                        </button>
                    </div>
                    <div class="file-preview" id="filePreview" style="display:none;">
                        <div class="file-icon" id="fileIcon"></div>
                        <div class="file-info">
                            <span class="file-name" id="fileName"></span>
                            <span class="file-size" id="fileSize"></span>
                        </div>
                        <button type="button" class="btn-remove" id="btnRemove" title="Supprimer">&#x2715;</button>
                    </div>
                </div>

            <div id="errorMsg" class="error-msg" style="display:none;"></div>

            <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                Envoyer
            </button>
        </form>
    </div>

    <footer>
        <p> HURTAUX Corinne ;) </p> 
    </footer>

    <script src="script.js"></script>
</body>
</html>
