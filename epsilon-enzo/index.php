<?php

session_start();

// Importer les configurations et fonctions
require_once 'config.php';
require_once 'functions.php';

// Initialiser le répertoire d'upload
if (!initUploadDirectory()) {
    die('Erreur : Impossible de créer le répertoire d\'upload.');
}

// ============================================================================
// 2. TRAITEMENT DU FORMULAIRE (POST)
// ============================================================================

$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = processFileUpload($_FILES['file']);
    
    if ($result['success']) {
        $success = true;
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

?>
<?php include 'header.php'; ?>

<!-- ========================================================================== -->
<!-- 3. AFFICHAGE HTML -->
<!-- ========================================================================== -->

<div class="container">
    <h1><?php echo MESSAGES['TITLE']; ?></h1>
    <p class="info"><?php echo MESSAGES['SUBTITLE']; ?></p>
    
    <!-- Affichage des messages d'erreur ou de succès -->
    <?php if ($error): ?>
        <div class="alert error">
            <strong>✗ Erreur !</strong>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($success): ?>
        <div class="alert success">
            <strong>✓ Succès !</strong>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire d'upload -->
    <form method="POST" enctype="multipart/form-data">
        <!-- Informations sur les restrictions -->
        <div class="form-group">
            <div class="restrictions">
                <strong><?php echo MESSAGES['ACCEPTED_FORMATS']; ?></strong><br>
                <strong><?php echo MESSAGES['MAX_FILE_SIZE_TEXT']; ?></strong>
            </div>
        </div>
        
        <!-- Sélection du fichier -->
        <div class="form-group">
            <label for="file"><?php echo MESSAGES['FILE_INPUT_BROWSE']; ?></label>
            <div class="file-input-wrapper">
                <input type="file" id="file" name="file" accept=".pdf" required>
                <label for="file" class="file-input-label">
                    <?php echo MESSAGES['FILE_INPUT_LABEL']; ?>
                </label>
                <span class="file-name" id="fileName"></span>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="button-group">
            <button type="submit" class="btn-submit">
                <?php echo MESSAGES['BTN_SUBMIT']; ?>
            </button>
            <button type="reset" class="btn-reset">
                <?php echo MESSAGES['BTN_RESET']; ?>
            </button>
        </div>
    </form>
</div>

<!-- Script pour afficher le nom du fichier sélectionné -->
<script>
(function() {
    'use strict';
    
    const fileInput = document.getElementById('file');
    const fileNameDisplay = document.getElementById('fileName');
    
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = 'Fichier sélectionné : ' + this.files[0].name;
            }
        });
    }
})();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
