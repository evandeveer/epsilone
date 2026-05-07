<?php
/**
 * Fonctions utilitaires du projet
 * 
 * Ce fichier contient toutes les fonctions réutilisables.
 * Centralisé pour faciliter la maintenance et les tests.
 */

// ============================================================================
// GESTION DES RÉPERTOIRES
// ============================================================================

/**
 * Initialise le répertoire d'upload s'il n'existe pas
 * 
 * @return bool true si le répertoire existe ou a été créé
 */
function initUploadDirectory() {
    if (!is_dir(UPLOAD_DIR)) {
        return mkdir(UPLOAD_DIR, PERMISSIONS_UPLOAD_DIR, true);
    }
    return true;
}

// ============================================================================
// GESTION DES ERREURS
// ============================================================================

/**
 * Retourne le message d'erreur correspondant au code d'upload PHP
 * 
 * @param int $error_code Code d'erreur PHP
 * @return string Message d'erreur localisé
 */
function getUploadErrorMessage($error_code) {
    return UPLOAD_ERRORS[$error_code] ?? 'Erreur inconnue';
}

// ============================================================================
// VALIDATION DES FICHIERS
// ============================================================================

/**
 * Valide l'extension du fichier
 * 
 * @param string $filename Nom du fichier
 * @return bool true si l'extension est autorisée
 */
function isAllowedExtension($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

/**
 * Valide le type MIME du fichier
 * 
 * @param string $filepath Chemin temporaire du fichier
 * @return bool true si le type MIME est autorisé
 */
function isAllowedMimeType($filepath) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    
    return in_array($mime_type, ALLOWED_MIME_TYPES);
}

/**
 * Valide la taille du fichier
 * 
 * @param int $filesize Taille du fichier en bytes
 * @return bool true si la taille est acceptable
 */
function isAllowedFileSize($filesize) {
    return $filesize <= MAX_FILE_SIZE;
}

// ============================================================================
// TRAITEMENT DES FICHIERS
// ============================================================================

/**
 * Génère un nom de fichier sécurisé et unique
 * 
 * @param string $original_filename Nom original du fichier
 * @return string Nom de fichier sécurisé et unique
 */
function generateSecureFilename($original_filename) {
    $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $basename = pathinfo($original_filename, PATHINFO_FILENAME);
    
    // Nettoyer le nom: remplacer les caractères spéciaux par des underscores
    $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
    
    // Générer un nom unique avec timestamp
    return uniqid('upload_', true) . '_' . $basename . '.' . $extension;
}

/**
 * Valide complètement un fichier uploadé
 * 
 * @param array $file Élément $_FILES
 * @return array ['success' => bool, 'message' => string]
 */
function validateUploadedFile($file) {
    // Vérifier si un fichier a été envoyé
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [
            'success' => false,
            'message' => MESSAGES['NO_FILE_SELECTED']
        ];
    }
    
    // Vérifier les erreurs PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => getUploadErrorMessage($file['error'])
        ];
    }
    
    // Vérifier la taille
    if (!isAllowedFileSize($file['size'])) {
        return [
            'success' => false,
            'message' => MESSAGES['FILE_TOO_LARGE']
        ];
    }
    
    // Vérifier l'extension
    if (!isAllowedExtension($file['name'])) {
        return [
            'success' => false,
            'message' => MESSAGES['INVALID_FORMAT'] . implode(', ', ALLOWED_EXTENSIONS)
        ];
    }
    
    // Vérifier le type MIME
    if (!isAllowedMimeType($file['tmp_name'])) {
        return [
            'success' => false,
            'message' => MESSAGES['INVALID_MIME']
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Fichier valide'
    ];
}

/**
 * Enregistre le fichier uploadé
 * 
 * @param array $file Élément $_FILES
 * @return array ['success' => bool, 'message' => string, 'filename' => string|null]
 */
function saveUploadedFile($file) {
    // Générer un nom de fichier sécurisé
    $secure_filename = generateSecureFilename($file['name']);
    $final_path = UPLOAD_DIR . $secure_filename;
    
    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $final_path)) {
        return [
            'success' => false,
            'message' => MESSAGES['SAVE_ERROR'],
            'filename' => null
        ];
    }
    
    // Définir les permissions du fichier
    chmod($final_path, PERMISSIONS_UPLOADED_FILE);
    
    return [
        'success' => true,
        'message' => MESSAGES['UPLOAD_SUCCESS'] . htmlspecialchars($file['name']),
        'filename' => $secure_filename
    ];
}

/**
 * Traite l'upload d'un fichier (validation + sauvegarde)
 * 
 * @param array $file Élément $_FILES
 * @return array ['success' => bool, 'message' => string]
 */
function processFileUpload($file) {
    // Valider le fichier
    $validation = validateUploadedFile($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Enregistrer le fichier
    return saveUploadedFile($file);
}

// ============================================================================
// UTILITAIRES
// ============================================================================

/**
 * Formate la taille d'un fichier de manière lisible
 * 
 * @param int $bytes Taille en bytes
 * @return string Taille formatée (ex: "5.2 MB")
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

?>
