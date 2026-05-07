<?php
/**
 * Configuration centralisée du projet Upload
 * 
 * Ce fichier contient toutes les constantes et paramètres configurables.
 * Modifier les valeurs ici affectera tout l'application.
 */

// ============================================================================
// CONFIGURATION DES RÉPERTOIRES
// ============================================================================
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('PERMISSIONS_UPLOAD_DIR', 0755);
define('PERMISSIONS_UPLOADED_FILE', 0644);

// ============================================================================
// CONFIGURATION DES RESTRICTIONS DE FICHIERS
// ============================================================================
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB - À modifier ici pour changer la limite
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx']); // Extensions autorisées
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// ============================================================================
// CONFIGURATION DES MESSAGES (Facile à traduire ou modifier)
// ============================================================================
define('MESSAGES', [
    'NO_FILE_SELECTED' => 'Aucun fichier sélectionné.',
    'FILE_TOO_LARGE' => 'Fichier trop volumineux (max 5 MB).',
    'INVALID_FORMAT' => 'Format de fichier non autorisé. Formats acceptés : ',
    'INVALID_MIME' => 'Type de fichier invalide détecté.',
    'SAVE_ERROR' => 'Erreur lors de la sauvegarde du fichier.',
    'UPLOAD_SUCCESS' => 'Fichier téléchargé avec succès : ',
    'ACCEPTED_FORMATS' => 'Formats acceptés : PDF',
    'MAX_FILE_SIZE_TEXT' => 'Taille max : 5 MB',
    'TITLE' => '📤 Upload de fichiers',
    'SUBTITLE' => 'Sélectionnez et envoyez un fichier',
    'FILE_INPUT_LABEL' => 'Cliquez pour sélectionner un fichier',
    'FILE_INPUT_BROWSE' => 'Parcourir documents :',
    'BTN_SUBMIT' => '✓ Envoyer',
    'BTN_RESET' => '✕ Annuler',
    'AUTHOR' => 'Enzo Deyrich'
]);

// ============================================================================
// CONFIGURATION DES UPLOAD ERRORS (Traduction des erreurs PHP)
// ============================================================================
define('UPLOAD_ERRORS', [
    UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximum définie dans php.ini',
    UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximum du formulaire',
    UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement transféré',
    UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été envoyé',
    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire absent',
    UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier',
    UPLOAD_ERR_EXTENSION => 'Upload rejeté par l\'extension PHP'
]);

// ============================================================================
// CONFIGURATION DES STYLES ET COULEURS (Facile à personnaliser)
// ============================================================================
define('STYLES', [
    'primary_color' => '#00ff40',
    'success_color' => '#4CAF50',
    'error_color' => '#f44336',
    'border_radius' => '4px',
    'max_width_container' => '500px'
]);

// ============================================================================
// CONFIGURATION DU PROJET
// ============================================================================
define('PROJECT_NAME', 'EPS | LON');
define('PROJECT_DESCRIPTION', 'Système de gestion d\'uploads de documents');
define('CHARSET', 'UTF-8');
define('LANG', 'fr');
?>
