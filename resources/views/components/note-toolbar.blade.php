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
<style>
    .note-toolbar {
        height: 100%;
        align-content: center;
        display: flex;
        padding: 2px;
        padding-right: 11px;
        justify-content: flex-end;
    }

    .btns-toolbar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .btn-toolbar {
        background-color: #A78BFA;
        margin-left: 10px;
        border: none;
        color: #F5F3FF;
        font-size: 1rem;
        height: 26px;
        width: 26px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-toolbar:hover {
        background-color: #DDD6FE;
        color: #1F2937;
        cursor: pointer;
    }

    #delete-note-form {
        padding: 0px;
        margin: 0px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

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
