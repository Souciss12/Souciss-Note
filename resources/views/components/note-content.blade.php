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

    function updateNoteContentContainerVisibility() {
        const noteContainer = document.querySelector('.note-content');
        const noteId = localStorage.getItem('active_note');
        if (!noteId && noteContainer) {
            noteContainer.style.display = 'none';
        } else if (noteContainer) {
            noteContainer.style.display = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
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
            shortcuts: {
                toggleSideBySide: null,
                toggleFullScreen: null
            }
        });

        updateNoteContentNoteId();
        updateNoteContentContainerVisibility();
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

        // Masquer le container si aucune note n'est sélectionnée
        const noteContainer = document.querySelector('.note-content');
        const noteId = localStorage.getItem('active_note');
        if (!noteId && noteContainer) {
            noteContainer.style.display = 'none';
        } else if (noteContainer) {
            noteContainer.style.display = '';
        }
        window.addEventListener('storage', function(e) {
            if (e.key === 'active_note') {
                updateNoteContentNoteId();
                updateNoteContentContainerVisibility();
            }
        });
        document.addEventListener('click', function() {
            updateNoteContentNoteId();
            updateNoteContentContainerVisibility();
        });
    });

    function toggleCustomSideBySide() {
        const editorContainer = document.getElementById('editor-container');
        const previewWrapper = document.getElementById('preview-wrapper');
        const customPreview = document.getElementById('custom-preview');

        if (!isCustomSideBySideActive) {
            editorContainer.classList.add('side-by-side-active');
            previewWrapper.style.display = 'block';

            updatePreview();

            easyMDE.codemirror.on('change', updatePreview);

            isCustomSideBySideActive = true;

            const sideBySideBtn = document.querySelector('.fa-columns').parentElement;
            sideBySideBtn.classList.add('active');
        } else {
            editorContainer.classList.remove('side-by-side-active');
            previewWrapper.style.display = 'none';

            easyMDE.codemirror.off('change', updatePreview);

            isCustomSideBySideActive = false;

            const sideBySideBtn = document.querySelector('.fa-columns').parentElement;
            sideBySideBtn.classList.remove('active');
        }
    }

    function updatePreview() {
        if (!isCustomSideBySideActive) return;

        const customPreview = document.getElementById('custom-preview');
        const markdownText = easyMDE.value();

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

    // Rends la fonction accessible globalement pour l'appeler depuis d'autres scripts
    window.updateNoteContentContainerVisibility = updateNoteContentContainerVisibility;
</script>
