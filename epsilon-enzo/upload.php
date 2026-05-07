<?php
// Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png'
]);

class FileUploader {
    private $errors = [];
    private $success = false;
    private $message = '';
    private $file_name = '';
    
    public function __construct() {
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
    }
    
    public function upload($file) {
        // Vérifier qu'un fichier a été envoyé
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->errors[] = 'Aucun fichier sélectionné';
            return false;
        }
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($file['error']);
            return false;
        }
        
        // Vérifier la taille
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->errors[] = 'Fichier trop volumineux (max 5 MB)';
            return false;
        }
        
        // Vérifier l'extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $this->errors[] = 'Format de fichier non autorisé. Formats acceptés : ' . implode(', ', ALLOWED_EXTENSIONS);
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, ALLOWED_MIME_TYPES)) {
            $this->errors[] = 'Type de fichier invalide détecté';
            return false;
        }
        
        // Générer un nom de fichier sécurisé
        $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
        $original_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
        $final_name = uniqid('upload_') . '_' . $original_name . '.' . $ext;
        $final_path = UPLOAD_DIR . $final_name;
        
        // Déplacer le fichier temporaire
        if (move_uploaded_file($file['tmp_name'], $final_path)) {
            chmod($final_path, 0644);
            $this->success = true;
            $this->message = 'Fichier téléchargé avec succès : ' . $file['name'];
            $this->file_name = $final_name;
            return true;
        } else {
            $this->errors[] = 'Erreur lors de la sauvegarde du fichier';
            return false;
        }
    }
    
    private function getUploadError($error_code) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximum définie dans php.ini',
            UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximum du formulaire',
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement transféré',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été envoyé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire absent',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier',
            UPLOAD_ERR_EXTENSION => 'Upload rejeté par l\'extension PHP'
        ];
        return $errors[$error_code] ?? 'Erreur inconnue';
    }
    
    public function getResult() {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'errors' => $this->errors,
            'file_name' => $this->file_name
        ];
    }
}

// Traitement de la requête
$result = ['success' => false, 'message' => '', 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploader = new FileUploader();
    
    if (isset($_FILES['file'])) {
        $uploader->upload($_FILES['file']);
    } else {
        $uploader->upload(null);
    }
    
    $result = $uploader->getResult();
}

header('Content-Type: application/json');
echo json_encode($result);
exit();
?>
