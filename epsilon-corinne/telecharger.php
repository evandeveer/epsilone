<?php
declare(strict_types=1);

// ── Configuration ────────────────────────────────────────────────────────────
const UPLOAD_DIR   = __DIR__ . '/uploads/';
const MAX_SIZE     = 10 * 1024 * 1024; 
const ALLOWED_MIME = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    #'image/jpeg',
    #'image/png',
];
const ALLOWED_EXT  = ['pdf','jpg', 'jpeg', 'png', 'doc', 'docx']; 

// ── Helpers ──────────────────────────────────────────────────────────────────
function redirigerAvcErreur(string $message): never
{
    $encoded = urlencode($message);
    header("Location: index.html?error=$encoded");
    exit;
}

function redirigerAvcSucces(string $filename): never
{
    $encoded = urlencode($filename);
    header("Location: success.php?file=$encoded");
    exit;
}

// ── Vérifications préliminaires ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] === UPLOAD_ERR_NO_FILE) {
    redirigerAvcErreur('Aucun fichier sélectionné.');
}

$file  = $_FILES['fichier'];
$error = $file['error'];

// Codes d'erreur PHP natifs
if ($error !== UPLOAD_ERR_OK) {
    $phpErrors = [
        UPLOAD_ERR_INI_SIZE   => 'Le fichier dépasse la limite autorisée par le serveur.',
        UPLOAD_ERR_FORM_SIZE  => 'Le fichier dépasse la limite définie dans le formulaire.',
        UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement téléversé.',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
        UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture sur le disque.',
        UPLOAD_ERR_EXTENSION  => 'Upload bloqué par une extension PHP.',
    ];
    redirigerAvcErreur($phpErrors[$error] ?? 'Erreur inconnue lors de l\'upload.');
}

// ── Validation de la taille ───────────────────────────────────────────────────
if ($file['size'] > MAX_SIZE) {
    redirigerAvcErreur('Fichier trop lourd. Taille maximale : 10 Mo.');
}

// ── Validation de l'extension ─────────────────────────────────────────────────
$originalName = basename($file['name']);
$ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($ext, ALLOWED_EXT, true)) {
    redirigerAvcErreur("Extension « .$ext » non autorisée. Formats acceptés : PDF, DOC, DOCX.");
}

// ── Validation du type MIME réel (via finfo, pas le header HTTP) ──────────────
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, ALLOWED_MIME, true)) {
    redirigerAvcErreur("Type de fichier non autorisé (MIME : $mimeType).");
}

// ── Cohérence extension / MIME (évite renommage malveillant) ──────────────────
$mimeToExt = [
    'application/pdf'      => ['pdf'],
    'application/msword'   => ['doc'],
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
    #'image/jpeg'           => ['jpg', 'jpeg'],
    #'image/png'            => ['png'],
];

if (!in_array($ext, $mimeToExt[$mimeType] ?? [], true)) {
    redirigerAvcErreur("L'extension du fichier ne correspond pas à son contenu réel.");
}

// ── Création du dossier uploads si nécessaire ─────────────────────────────────
if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) {
    redirigerAvcErreur('Impossible de créer le dossier de dépôt.');
}

// ── Génération d'un nom sécurisé ──────────────────────────────────────────────
// On conserve le nom original (sans l'extension) et on ajoute un suffixe unique.
$baseName    = pathinfo($originalName, PATHINFO_FILENAME);
$safeName    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
$safeName    = substr($safeName, 0, 64);                         // limite la longueur
$uniqueName  = $safeName . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destination = UPLOAD_DIR . $uniqueName;

// ── Déplacement du fichier ────────────────────────────────────────────────────
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    redirigerAvcErreur('Une erreur est survenue lors de l\'enregistrement du fichier.');
}

redirigerAvcSucces($uniqueName);
