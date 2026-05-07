/**
 * DIAGRAMME D'ARCHITECTURE DU PROJET
 * 
 * Ce fichier montre comment les composants travaillent ensemble
 */

/*
┌─────────────────────────────────────────────────────────────────────────┐
│                        ARCHITECTURE GLOBALE                             │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────┐
│                      UTILISATEUR (Navigateur)                       │
└────────────────────────────┬─────────────────────────────────────────┘
                             │
                    Soumet le formulaire
                             │
                             ▼
┌──────────────────────────────────────────────────────────────────────┐
│                    index.php (Point d'entrée)                       │
├──────────────────────────────────────────────────────────────────────┤
│  1. session_start()                                                  │
│  2. require_once 'config.php'   ──────────┐                        │
│  3. require_once 'functions.php' ─────────┼──┐                     │
│  4. initUploadDirectory()        ─────────┼──┼──┐                  │
│  5. if (POST) processFileUpload() ────────┼──┼──┼──┐               │
│  6. include 'header.php'  ────────────────┼──┼──┼──┼───┐           │
│  7. Affiche le formulaire et les messages │  │  │  │   │           │
└──────────────────┬───────────────────────┬──┬──┬──┬───┬───┘
                   │                       │  │  │  │   │
     ┌─────────────┴───────────┐          │  │  │  │   │
     │                         │          │  │  │  │   │
     ▼                         ▼          ▼  ▼  ▼  ▼   ▼
┌──────────────┐  ┌─────────────────┐  ┌─────────────────────────┐
│  header.php  │  │  config.php     │  │  functions.php          │
├──────────────┤  ├─────────────────┤  ├─────────────────────────┤
│ <!DOCTYPE>   │  │ define()        │  │ function validate...()  │
│ <html>       │  │ UPLOAD_DIR      │  │ function save...()      │
│ <head>       │  │ MAX_FILE_SIZE   │  │ function process...()   │
│ <meta>       │  │ ALLOWED_*       │  │ function generate...()  │
│ <title>      │  │ MESSAGES[]      │  │ function format...()    │
│ <link CSS>   │  │ STYLES[]        │  │                         │
│ </head>      │  │ UPLOAD_ERRORS[] │  │ + 6 autres fonctions    │
│ <body>       │  └─────────────────┘  └─────────────────────────┘
└──────────────┘        ▲                        ▲
                        │                        │
                        │ Importe              Importe
                        │                        │
                        └────────────────────────┘


┌──────────────────────────────────────────────────────────────────────┐
│                     FLUX DE TRAITEMENT                               │
└──────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│ Utilisateur POST │  [Sélectionne un fichier et submit]
└────────┬─────────┘
         │
         ▼
┌──────────────────────────────────────────────────┐
│ index.php: $_FILES['file'] reçu                  │
└────────┬─────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────┐
│ functions.php: processFileUpload()               │
│  ├─ validateUploadedFile()                       │
│  │  ├─ isAllowedExtension()                      │
│  │  ├─ isAllowedFileSize()                       │
│  │  └─ isAllowedMimeType()                       │
│  │                                               │
│  └─ saveUploadedFile()                           │
│     ├─ generateSecureFilename()                  │
│     ├─ move_uploaded_file()                      │
│     └─ chmod()                                   │
└────────┬─────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────┐
│ Retourne: ['success' => bool, 'message' => str] │
└────────┬─────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────┐
│ index.php: Affiche le résultat                   │
│  ├─ Si success: message de succès (vert)         │
│  └─ Si erreur: message d'erreur (rouge)          │
└────────┬─────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────┐
│ Utilisateur voit la confirmation                 │
└──────────────────────────────────────────────────┘


┌──────────────────────────────────────────────────────────────────────┐
│              DÉPENDANCES ENTRE FICHIERS                              │
└──────────────────────────────────────────────────────────────────────┘

index.php
    ├─ Importe: config.php        [Constantes]
    ├─ Importe: functions.php     [Logique]
    ├─ Importe: header.php        [Structure HTML]
    │   └─ header.php importe: config.php
    │   └─ header.php charge: style.css
    │
    └─ Utilise: style.css         [Styles CSS]

Dépendance circulaire? NON ✅
Chaîne de dépendance claire? OUI ✅


┌──────────────────────────────────────────────────────────────────────┐
│             MODIFICATION IMPACT                                      │
└──────────────────────────────────────────────────────────────────────┘

Modifier config.php
    → Affecte: index.php, header.php, functions.php
    → Impact: GLOBAL (tout le projet)
    → Exemple: Changer MAX_FILE_SIZE → nouvelle limite partout

Modifier functions.php
    → Affecte: index.php
    → Impact: Logique métier uniquement
    → Exemple: Ajouter fonction → Disponible dans index.php

Modifier style.css
    → Affecte: Présentation uniquement
    → Impact: VISUEL (pas de logique)
    → Exemple: Changer couleur → Visible immédiatement

Modifier header.php
    → Affecte: Structure HTML
    → Impact: Layout et organisation
    → Exemple: Ajouter un div → Nouveau conteneur disponible

Modifier index.php
    → Affecte: Logique de la page principale
    → Impact: Comportement de l'application
    → Exemple: Ajouter condition → Nouveau flux


┌──────────────────────────────────────────────────────────────────────┐
│              SCALABILITÉ FUTURE                                      │
└──────────────────────────────────────────────────────────────────────┘

Pour ajouter une nouvelle page (ex: admin.php)

    admin.php (nouvelle page)
        ├─ require_once 'config.php'      [Réutiliser config]
        ├─ require_once 'functions.php'   [Réutiliser fonctions]
        └─ include 'header.php'            [Réutiliser header]

Avantage: Zéro duplication! Tout est centralisé.


┌──────────────────────────────────────────────────────────────────────┐
│              POINTS D'EXTENSION                                      │
└──────────────────────────────────────────────────────────────────────┘

1. Ajouter une validation personnalisée
   → Modifier functions.php: validateUploadedFile()

2. Ajouter un traitement post-upload (ex: compression)
   → Ajouter fonction dans functions.php
   → Appeler depuis index.php après saveUploadedFile()

3. Ajouter une base de données
   → Créer database.php
   → Appeler depuis functions.php pour enregistrer les uploads

4. Ajouter une authentification
   → Créer auth.php
   → Vérifier au début de index.php

5. Ajouter un système de notifications
   → Créer email.php
   → Appeler depuis functions.php après succès


┌──────────────────────────────────────────────────────────────────────┐
│              PRINCIPES APPLIQUÉS                                     │
└──────────────────────────────────────────────────────────────────────┘

✅ DRY (Don't Repeat Yourself)
   Chaque fonction n'existe qu'une fois

✅ KISS (Keep It Simple, Stupid)
   Code lisible et simple à comprendre

✅ YAGNI (You Aren't Gonna Need It)
   Pas de sur-engineering

✅ SOC (Separation of Concerns)
   Config ≠ Logique ≠ HTML ≠ CSS

✅ SRP (Single Responsibility Principle)
   Chaque module a UNE responsabilité

✅ DIP (Dependency Inversion Principle)
   Dépendances claires et unidirectionnelles

*/
