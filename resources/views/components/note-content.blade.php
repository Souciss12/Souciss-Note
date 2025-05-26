@vite(['resources/css/note-content.css'])

<div class="container note-content">
    <textarea class="note-content-header fw-semibold" id="note-content-header" data-note-id="" readonly></textarea>
    <textarea class="note-content-body" id="note-content-body" data-note-id=""></textarea>
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
    document.addEventListener('DOMContentLoaded', function() {
        easyMDE = new EasyMDE({
            element: document.getElementById('note-content-body'),
            spellChecker: false,
            status: false,
            toolbar: ["bold", "italic", "heading", "|", "unordered-list", "ordered-list", "|",
                "link", "image", "table", "|", "guide", "preview"
            ],
            maxHeight: '200px',
            renderingConfig: {
                codeSyntaxHighlighting: true
            },
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
