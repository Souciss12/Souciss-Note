<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<div class="container note-content mt-3">
    <div class="note-content-header">
        <h2 class="fw-semibold">Note Content</h2>
    </div>
    <textarea class="note-content-body" id="note-content-body" data-note-id=""></textarea>
</div>

<style>
    .note-content {
        background-color: #FFFFFF;
        border-radius: 10px;
        padding: 20px;
    }

    .note-content-header {
        justify-content: center;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .note-content-body {
        width: 100%;
        height: 100%;
        border: none;
        outline: none;
        resize: none;
        padding: 10px;
        border-radius: 8px;
    }
</style>
<script>
    function updateNoteContentNoteId() {
        const textarea = document.getElementById('note-content-body');
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
</script>
