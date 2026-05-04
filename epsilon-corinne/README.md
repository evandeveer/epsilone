# Dépôt de documents — EPSILON

Application web permettant à un utilisateur de déposer un fichier via une interface drag & drop. Le fichier est validé côté client et côté serveur avant d'être enregistré sur le serveur.

## Fonctionnalités

- Glisser-déposer ou sélection manuelle d'un fichier
- Prévisualisation du fichier sélectionné (nom, taille, icône selon le format)
- Validation côté client (format, taille) avant envoi
- Validation côté serveur : taille, extension, type MIME réel, cohérence extension/MIME
- Nom de fichier sécurisé généré automatiquement à la réception
- Page de confirmation après un dépôt réussi

## Formats acceptés

| Format | Extension |
|--------|-----------|
| PDF    | `.pdf`    |
| JPG    | `.jpg`    |
| JPEG   | `.jpeg`   |
| PNG    | `.png`    |

## Taille maximale

**10 Mo** par fichier.

## Structure du projet

```
EPSILON/
├── index.php          # Page principale avec le formulaire d'upload
├── header.html        # En-tête de la carte (titre, icône, sous-titre)
├── telecharger.php    # Script PHP de traitement et validation du fichier
├── succes.php         # Page affichée après un dépôt réussi
├── script.js          # Validation et interactions côté client
├── style.css          # Styles de l'interface
└── fichiers/          # Dossier de stockage des fichiers déposés
```

## Installation (avec MAMP)

1. Cloner le dépôt dans le dossier `htdocs` de MAMP :
   ```bash
   git clone https://github.com/Co-rin-ne/EPSILON.git
   ```
2. Démarrer MAMP et s'assurer que PHP est actif.
3. Ouvrir `http://localhost/EPSILON/` dans un navigateur.

## Sécurité

- Le type MIME est vérifié via `finfo` (lecture réelle du fichier, pas l'en-tête HTTP).
- L'extension est vérifiée en cohérence avec le MIME pour éviter les renommages malveillants.
- Le nom du fichier est assaini (`preg_replace`) et un suffixe aléatoire (`bin2hex(random_bytes(6))`) est ajouté pour éviter les collisions et les accès prédictibles.

## Auteur

Corinne Hurtaux
