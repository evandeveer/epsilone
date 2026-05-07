// ============================================================================
// BONNES PRATIQUES DE CODE APPLIQUÉES AU PROJET
// ============================================================================

/*
Ce document explique les bonnes pratiques utilisées dans ce projet
et les principes de clean code appliqués.
*/

// ============================================================================
// 1. ORGANISATION DU CODE
// ============================================================================

/*
AVANT (Mauvais - Tout mélangé):
*/
<?php
// Mauvaise pratique: tout dans un seul fichier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload logic ici
    // Styles ici
    // HTML ici
    // Configuration ici
}
?>

/*
APRÈS (Bon - Séparation claire):
*/
<?php
// ✅ config.php        = Configuration
// ✅ functions.php     = Logique métier
// ✅ index.php         = Orchestration
// ✅ header.php        = Structure HTML
// ✅ style.css         = Présentation
?>

// ============================================================================
// 2. UTILISATION DE CONSTANTES
// ============================================================================

/*
AVANT (Mauvais - Magic numbers partout):
*/
<?php
if ($file['size'] > 5242880) {  // Qu'est-ce que c'est? 5MB? 10MB?
    // Où d'autre 5242880 est utilisé?
    // Difficile à modifier
}
?>

/*
APRÈS (Bon - Constante centralisée):
*/
<?php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5 MB - Clair et lisible

if ($file['size'] > MAX_FILE_SIZE) {
    // Lire une seule fois
    // Modifier une seule fois
    // Réutiliser partout
}
?>

// ============================================================================
// 3. FONCTIONS AVEC UNE SEULE RESPONSABILITÉ
// ============================================================================

/*
AVANT (Mauvais - Responsabilités multiples):
*/
<?php
function handleUpload($file) {
    // Vérifier l'extension
    // Vérifier le type MIME
    // Vérifier la taille
    // Générer le nom
    // Sauvegarder
    // Afficher le message
    // Envoyer un email
    // Ajouter à la DB
    // Tout faire en une seule fonction!
}
?>

/*
APRÈS (Bon - Responsabilité unique):
*/
<?php
function isAllowedExtension($filename) {
    // Responsabilité 1: Valider l'extension
    return in_array(getExtension($filename), ALLOWED_EXTENSIONS);
}

function isAllowedMimeType($filepath) {
    // Responsabilité 2: Valider le MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    return in_array(finfo_file($finfo, $filepath), ALLOWED_MIME_TYPES);
}

function isAllowedFileSize($filesize) {
    // Responsabilité 3: Valider la taille
    return $filesize <= MAX_FILE_SIZE;
}

function generateSecureFilename($original) {
    // Responsabilité 4: Générer un nom unique
    return uniqid('upload_') . '_' . $original;
}

// Composer les responsabilités
function validateUploadedFile($file) {
    if (!isAllowedExtension($file['name'])) return false;
    if (!isAllowedMimeType($file['tmp_name'])) return false;
    if (!isAllowedFileSize($file['size'])) return false;
    return true;
}
?>

// ============================================================================
// 4. GESTION D'ERREURS EXPLICITE
// ============================================================================

/*
AVANT (Mauvais - Pas d'erreurs, tout échoue silencieusement):
*/
<?php
$result = move_uploaded_file($file['tmp_name'], $final_path);
// Et après? C'est quoi le problème s'il échoue?
?>

/*
APRÈS (Bon - Retourner les erreurs):
*/
<?php
function saveUploadedFile($file) {
    $secure_filename = generateSecureFilename($file['name']);
    $final_path = UPLOAD_DIR . $secure_filename;
    
    if (!move_uploaded_file($file['tmp_name'], $final_path)) {
        return [
            'success' => false,
            'message' => MESSAGES['SAVE_ERROR']
        ];
    }
    
    chmod($final_path, PERMISSIONS_UPLOADED_FILE);
    
    return [
        'success' => true,
        'message' => MESSAGES['UPLOAD_SUCCESS']
    ];
}

// L'appelant sait exactement ce qui s'est passé
$result = saveUploadedFile($file);
if ($result['success']) {
    // Succès
} else {
    // Erreur - Message clair du problème
}
?>

// ============================================================================
// 5. ÉVITER LA DUPLICATION
// ============================================================================

/*
AVANT (Mauvais - Messages répétés):
*/
<?php
$error_messages = [
    'file_too_large' => 'Fichier trop volumineux',
    'invalid_format' => 'Format invalide',
];

// Plus tard en HTML:
if (isset($messages['file_too_large'])) {
    echo $messages['file_too_large'];
}
if (isset($messages['invalid_format'])) {
    echo $messages['invalid_format'];
}
// Même message à plusieurs places = risque d'inconsistance
?>

/*
APRÈS (Bon - Constante unique):
*/
<?php
define('MESSAGES', [
    'FILE_TOO_LARGE' => 'Fichier trop volumineux',
    'INVALID_FORMAT' => 'Format invalide',
]);

// Utiliser partout:
echo MESSAGES['FILE_TOO_LARGE'];  // Même message garantis
echo MESSAGES['INVALID_FORMAT'];   // Cohérent partout
?>

// ============================================================================
// 6. NOMS SIGNIFICATIFS
// ============================================================================

/*
AVANT (Mauvais - Noms non significatifs):
*/
<?php
function vf($f) {  // Qu'est-ce que vf? Vérifier fichier?
    return true;
}

function p($x) {  // Qu'est-ce que p? Processus?
    echo $x;
}

$tmp = $file['name'];  // Qu'est-ce que tmp?
?>

/*
APRÈS (Bon - Noms clairs):
*/
<?php
function validateUploadedFile($file) {
    // Immédiatement clair: on valide un fichier uploadé
    return true;
}

function displayErrorMessage($message) {
    // Clair: on affiche un message d'erreur
    echo $message;
}

$original_filename = $file['name'];
// Clair: c'est le nom original du fichier
?>

// ============================================================================
// 7. MAINTENIR LA SÉCURITÉ
// ============================================================================

/*
AVANT (Mauvais - Failles de sécurité):
*/
<?php
$filename = $_FILES['file']['name'];  // Dangerous!
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $filename);
echo "Vous avez uploadé: " . $_FILES['file']['name'];  // XSS!
?>

/*
APRÈS (Bon - Sécurisé):
*/
<?php
// 1. Ne jamais faire confiance au nom original
$secure_filename = generateSecureFilename($_FILES['file']['name']);

// 2. Vérifier le type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
if (!in_array($mime, ALLOWED_MIME_TYPES)) {
    die('Invalid file type');
}

// 3. Utiliser htmlspecialchars() pour afficher les données utilisateur
echo "Vous avez uploadé: " . htmlspecialchars($_FILES['file']['name']);

// 4. Définir les permissions appropriées
chmod('uploads/' . $secure_filename, 0644);
?>

// ============================================================================
// 8. DOCUMENTATION CLAIRE
// ============================================================================

/*
AVANT (Mauvais - Pas de documentation):
*/
<?php
function processFileUpload($file) {
    // Qu'est-ce que cette fonction fait?
    // Quels paramètres prend-elle?
    // Que retourne-elle?
    // Quand l'utiliser?
}
?>

/*
APRÈS (Bon - Documentation complète):
*/
<?php
/**
 * Traite l'upload d'un fichier (validation + sauvegarde)
 * 
 * Cette fonction combine la validation complète d'un fichier
 * et sa sauvegarde sécurisée sur le serveur.
 * 
 * @param array $file Élément $_FILES provenant du formulaire
 * @return array Associatif avec 'success' (bool) et 'message' (string)
 * 
 * Exemple d'utilisation:
 * $result = processFileUpload($_FILES['file']);
 * if ($result['success']) {
 *     echo "Succès: " . $result['message'];
 * } else {
 *     echo "Erreur: " . $result['message'];
 * }
 */
function processFileUpload($file) {
    // Code
}
?>

// ============================================================================
// 9. CONFIGURATION CENTRALISÉE
// ============================================================================

/*
AVANT (Mauvais - Configuration partout):
*/
<?php
// index.php
$max_size = 5 * 1024 * 1024;
$allowed = ['pdf'];

// admin.php
$max_size = 10 * 1024 * 1024;  // Oops, différent!
$allowed = ['pdf', 'doc'];     // Incohérent!

// api.php
$max_size = 5 * 1024 * 1024;   // Quelle valeur est correcte?
?>

/*
APRÈS (Bon - Configuration unique):
*/
<?php
// config.php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['pdf']);

// index.php, admin.php, api.php
if ($file['size'] > MAX_FILE_SIZE) {  // Même valeur partout
    // ...
}
?>

// ============================================================================
// 10. LISIBILITÉ DU CODE
// ============================================================================

/*
AVANT (Mauvais - Difficile à lire):
*/
<?php
if($file['error']===UPLOAD_ERR_NO_FILE){$error='No file';}elseif($file['error']!==UPLOAD_ERR_OK){$error=getUploadError($file['error']);}elseif($file['size']>MAX_FILE_SIZE){$error='File too large';}else{$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));if(!in_array($ext,ALLOWED_EXTENSIONS)){$error='Format not allowed';}
?>

/*
APRÈS (Bon - Facile à lire):
*/
<?php
// Vérifier si un fichier a été envoyé
if ($file['error'] === UPLOAD_ERR_NO_FILE) {
    $error = 'Aucun fichier sélectionné.';
}
// Vérifier les erreurs PHP
elseif ($file['error'] !== UPLOAD_ERR_OK) {
    $error = getUploadErrorMessage($file['error']);
}
// Vérifier la taille
elseif ($file['size'] > MAX_FILE_SIZE) {
    $error = MESSAGES['FILE_TOO_LARGE'];
}
// Vérifier l'extension
else {
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $error = MESSAGES['INVALID_FORMAT'];
    }
}
?>

// ============================================================================
// RÉSUMÉ DES BONNES PRATIQUES APPLIQUÉES
// ============================================================================

/*
1. ✅ Séparation des responsabilités
   - config.php = Configuration
   - functions.php = Logique
   - index.php = Orchestration
   - style.css = Présentation

2. ✅ Utilisation de constantes
   - MAX_FILE_SIZE une seule fois
   - ALLOWED_EXTENSIONS centralisé
   - MESSAGES centralisé

3. ✅ Responsabilité unique des fonctions
   - validateUploadedFile()
   - saveUploadedFile()
   - processFileUpload()

4. ✅ Gestion d'erreurs explicite
   - Retourner des résultats structurés
   - Messages d'erreur clairs

5. ✅ Pas de duplication
   - DRY: Don't Repeat Yourself
   - Chaque logique écrite une seule fois

6. ✅ Noms significatifs
   - generateSecureFilename() vs fn()
   - $original_filename vs $f

7. ✅ Sécurité par défaut
   - htmlspecialchars() pour XSS
   - Vérification MIME pour fichiers
   - Permissions appropriées

8. ✅ Documentation complète
   - Commentaires explicatifs
   - Docstrings pour les fonctions
   - README.md et MAINTENANCE.md

9. ✅ Configuration centralisée
   - Modifier une fois = change partout
   - Facile à maintenir à long terme

10. ✅ Lisibilité du code
    - Code bien formaté
    - Logique claire
    - Facile à déboguer

RÉSULTAT: Code professionnel, maintenable, évolutif, sécurisé.
*/
