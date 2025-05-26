@vite(['resources/css/note-toolbar.css'])
<div class="note-toolbar">
    <div class="btns-toolbar">
        <button class="btn btn-toolbar" id="copy-content-btn">
            <i class="bi bi-copy"></i>
        </button>
        <button class="btn btn-toolbar" id="clear-content-btn">
            <i class="bi bi-eraser-fill"></i>
        </button>
        <form id="delete-note-form" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-toolbar">
                <i class="bi bi-trash bi-15rem"></i>
            </button>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('delete-note-form');

        updateDeleteFormAction();

        window.addEventListener('storage', function(e) {
            if (e.key === 'active_note') {
                updateDeleteFormAction();
            }
        });

        document.addEventListener('click', function() {
            updateDeleteFormAction();
        });

        function updateDeleteFormAction() {
            const activeNoteId = localStorage.getItem('active_note');
            if (activeNoteId) {
                deleteForm.action = `/notes/${activeNoteId}`;
            }
        }

        const clearContentBtn = document.getElementById('clear-content-btn');
        clearContentBtn.addEventListener('click', function() {
            const activeNoteId = localStorage.getItem('active_note');
            if (!activeNoteId) return;

            const noteContent = document.querySelector('.note-content-body');
            if (noteContent) {
                noteContent.value = '';

                fetch(`/notes/${activeNoteId}/update-content`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        content: ''
                    })
                }).catch(error => console.error('Erreur lors de la suppression du contenu:', error));
            }
        });

        const copyContentBtn = document.getElementById('copy-content-btn');
        copyContentBtn.addEventListener('click', function() {
            const activeNoteId = localStorage.getItem('active_note');
            if (!activeNoteId) return;

            const noteContent = document.querySelector('.note-content-body');
            if (noteContent) {
                navigator.clipboard.writeText(noteContent.value)
                    .catch(error => console.error('Erreur lors de la copie du contenu:', error));
            }
        });
    });
</script>
