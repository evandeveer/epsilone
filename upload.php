<?php
if (isset($_FILES['mon_fichier']) && $_FILES['mon_fichier']['error'] == 0) {
    
    $nomFichier = $_FILES['mon_fichier']['name'];
    $tempFichier = $_FILES['mon_fichier']['tmp_name'];

    $extensionsAutorisees = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    $infosFichier = pathinfo($nomFichier);
    $extensionExtraction = strtolower($infosFichier['extension']);

    if (in_array($extensionExtraction, $extensionsAutorisees)) {
        
        $dossierCible = 'uploads/' . basename($nomFichier);

        move_uploaded_file($tempFichier, $dossierCible);
        echo "Succès ! Le fichier <strong>$nomFichier</strong> a été téléchargé.";
       
    } else {
        echo "Erreur : Seuls les fichiers PDF et images sont autorisés (JPG, PNG, GIF, WEBP, BMP).";
    }

} else {
    echo "Erreur : Aucun fichier sélectionné ou erreur lors de l'envoi.";
}

echo '<br><a href="upload_page.php">Retour au formulaire</a>';
?>