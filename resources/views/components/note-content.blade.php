@vite(['resources/css/note-content.css'])

<div class="container note-content">
    <textarea class="note-content-header fw-semibold" id="note-content-header" data-note-id=""></textarea>
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

    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('note-content-body');
        updateNoteContentNoteId();
        textarea.addEventListener('input', function() {
            const currentNoteId = textarea.getAttribute('data-note-id');
            if (!currentNoteId) return;
            fetch(`/notes/${currentNoteId}/update-content`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: textarea.value
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
        textarea.addEventListener('input', function() {
            const currentNoteId = textarea.getAttribute('data-note-id');
            if (!currentNoteId) return;
            fetch(`/notes/${currentNoteId}/update-title`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: textarea.value
                })
            });
        });

        window.addEventListener('storage', function(e) {
            if (e.key === 'active_note') {
                updateNoteTitleNoteId();
            }
        });

        document.addEventListener('click', function() {
            updateNoteTitleNoteId();
        });
    });
</script>
