@vite(['resources/css/note-content.css'])

<div class="container note-content">
    <textarea class="note-content-header fw-semibold" id="note-content-header" data-note-id="" readonly></textarea>
    <div class="editor-container" id="editor-container">
        <div class="editor-wrapper">
            <textarea class="note-content-body" id="note-content-body" data-note-id=""></textarea>
        </div>
        <div class="preview-wrapper" id="preview-wrapper" style="display: none;">
            <div class="custom-preview" id="custom-preview"></div>
        </div>
    </div>
</div>
<script>
    function updateNoteContentNoteId() {
        const textarea = document.getElementById('note-content-body');
        const noteId = localStorage.getItem('active_note');
        if (textarea && noteId) {
            textarea.setAttribute('data-note-id', noteId);
        }
    }

    function updateNoteTitleNoteId() {
        const textarea = document.getElementById('note-content-header');
        const noteId = localStorage.getItem('active_note');
        if (textarea && noteId) {
            textarea.setAttribute('data-note-id', noteId);
        }
    }

    let easyMDE;
    let isCustomSideBySideActive = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Configuration personnalisée d'EasyMDE
        easyMDE = new EasyMDE({
            element: document.getElementById('note-content-body'),
            spellChecker: false,
            status: false,
            toolbar: [
                "bold", "italic", "heading", "|",
                "unordered-list", "ordered-list", "|",
                "link", "image", "table", "|",
                "guide", "preview",
                {
                    name: "side-by-side",
                    action: toggleCustomSideBySide,
                    className: "fa fa-columns",
                    title: "Aperçu côte à côte"
                }
            ],
            maxHeight: '200px',
            renderingConfig: {
                codeSyntaxHighlighting: true
            },
            // Désactiver le mode plein écran par défaut
            shortcuts: {
                toggleSideBySide: null,
                toggleFullScreen: null
            }
        });

        updateNoteContentNoteId();
        easyMDE.codemirror.on('change', function() {
            const textarea = document.getElementById('note-content-body');
            const currentNoteId = textarea.getAttribute('data-note-id');
            if (!currentNoteId) return;
            fetch(`/notes/${currentNoteId}/update-content`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: easyMDE.value()
                })
            });
        });

        window.addEventListener('storage', function(e) {
            if (e.key === 'active_note') {
                updateNoteContentNoteId();
            }
        });

        document.addEventListener('click', function() {
            updateNoteContentNoteId();
        });
    });

    // Fonction pour gérer le mode côte à côte personnalisé
    function toggleCustomSideBySide() {
        const editorContainer = document.getElementById('editor-container');
        const previewWrapper = document.getElementById('preview-wrapper');
        const customPreview = document.getElementById('custom-preview');

        if (!isCustomSideBySideActive) {
            // Activer le mode côte à côte
            editorContainer.classList.add('side-by-side-active');
            previewWrapper.style.display = 'block';

            // Générer et afficher la prévisualisation
            updatePreview();

            // Mettre à jour la prévisualisation en temps réel
            easyMDE.codemirror.on('change', updatePreview);

            isCustomSideBySideActive = true;

            // Mettre à jour l'apparence du bouton
            const sideBySideBtn = document.querySelector('.fa-columns').parentElement;
            sideBySideBtn.classList.add('active');
        } else {
            // Désactiver le mode côte à côte
            editorContainer.classList.remove('side-by-side-active');
            previewWrapper.style.display = 'none';

            // Retirer l'événement de mise à jour
            easyMDE.codemirror.off('change', updatePreview);

            isCustomSideBySideActive = false;

            // Mettre à jour l'apparence du bouton
            const sideBySideBtn = document.querySelector('.fa-columns').parentElement;
            sideBySideBtn.classList.remove('active');
        }
    }

    // Fonction pour mettre à jour la prévisualisation
    function updatePreview() {
        if (!isCustomSideBySideActive) return;

        const customPreview = document.getElementById('custom-preview');
        const markdownText = easyMDE.value();

        // Utiliser la fonction de rendu d'EasyMDE pour convertir le markdown
        const htmlContent = easyMDE.markdown(markdownText);
        customPreview.innerHTML = htmlContent;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('note-content-header');
        updateNoteTitleNoteId();
        textarea.setAttribute('readonly', 'readonly');
    });

    window.setNoteContentBody = function(content) {
        if (easyMDE) {
            easyMDE.value(content || '');
        } else {
            document.getElementById('note-content-body').value = content || '';
        }
    };
</script>
