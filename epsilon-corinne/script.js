const ALLOWED_TYPES = ['application/pdf', 'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg', 'image/png'];
const ALLOWED_EXT  = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
const MAX_SIZE_MB   = 10;

const fichierInput   = document.getElementById('fichierInput');
const dropZone    = document.getElementById('dropZone');
const dropContent = document.getElementById('dropContent');
const fichierPreview = document.getElementById('fichierPreview');
const fichierName    = document.getElementById('fichierName');
const fichierSize    = document.getElementById('fichierSize');
const fichierIcon    = document.getElementById('fichierIcon');
const btnBrowse   = document.getElementById('btnBrowse');
const btnRemove   = document.getElementById('btnRemove');
const btnSubmit   = document.getElementById('btnSubmit');
const errorMsg    = document.getElementById('errorMsg');
const uploadForm  = document.getElementById('uploadForm');

const ICONS = { pdf: '📄', doc: '📝', docx: '📝', jpg: '🖼️', jpeg: '🖼️', png: '🖼️' };

btnBrowse.addEventListener('click', () => fichierInput.click());
dropZone.addEventListener('click', (e) => {
    if (e.target === dropZone) fichierInput.click();
});

// Drag & drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const fichier = e.dataTransfer.fichiers[0];
    if (fichier) handlefichier(fichier);
});

fichierInput.addEventListener('change', () => {
    if (fichierInput.fichiers[0]) handlefichier(fichierInput.fichiers[0]);
});

btnRemove.addEventListener('click', () => resetUI());

uploadForm.addEventListener('submit', (e) => {
    if (!fichierInput.fichiers[0]) {
        e.preventDefault();
        showError('Veuillez sélectionner un fichier avant d\'envoyer.');
    }
});

function handlefichier(fichier) {
    const ext = fichier.name.split('.').pop().toLowerCase();

    if (!ALLOWED_EXT.includes(ext) || !ALLOWED_TYPES.includes(fichier.type)) {
        showError('Format non autorisé. Formats acceptés : PDF, DOC, DOCX, JPG, PNG.');
        return;
    }
    if (fichier.size > MAX_SIZE_MB * 1024 * 1024) {
        showError(`Fichier trop lourd. Taille maximale : ${MAX_SIZE_MB} Mo.`);
        return;
    }

    hideError();

    // Inject fichier into the real input via DataTransfer
    const dt = new DataTransfer();
    dt.items.add(fichier);
    fichierInput.fichiers = dt.fichiers;

    fichierName.textContent = fichier.name;
    fichierSize.textContent = formatSize(fichier.size);
    fichierIcon.textContent = ICONS[ext] || '📁';
    dropContent.style.display = 'none';
    fichierPreview.style.display = 'flex';
    btnSubmit.disabled = false;
}

function resetUI() {
    fichierInput.value = '';
    dropContent.style.display = 'block';
    fichierPreview.style.display  = 'none';
    btnSubmit.disabled = true;
    hideError();
}

function showError(msg) {
    errorMsg.textContent = msg;
    errorMsg.style.display = 'block';
}
function hideError() {
    errorMsg.style.display = 'none';
}

function formatSize(bytes) {
    if (bytes < 1024)        return bytes + ' o';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / (1024 * 1024)).toFixed(1) + ' Mo';
}
