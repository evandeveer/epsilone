<?php
/**
 * En-tête HTML du projet
 * 
 * Ce fichier contient la déclaration HTML de base, les métadonnées et l'import des styles.
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo PROJECT_DESCRIPTION; ?>">
    
    <title><?php echo PROJECT_NAME; ?></title>
    
    <!-- Feuille de styles externe - Pour meilleure maintenabilité -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
