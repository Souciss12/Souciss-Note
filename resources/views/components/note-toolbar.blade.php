@vite(['resources/css/note-toolbar.css'])
<div class="note-toolbar">
    <div class="toolbar-url">
        <span class="url">Url de la note</span>
    </div>
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
        updateNoteUrl();

        window.addEventListener('storage', function(e) {
            if (e.key === 'active_note') {
                updateDeleteFormAction();
                updateNoteUrl();
            }
        });

        document.addEventListener('click', function() {
            updateDeleteFormAction();
            updateNoteUrl();
        });

        function updateDeleteFormAction() {
            const activeNoteId = localStorage.getItem('active_note');
            if (activeNoteId) {
                deleteForm.action = `/notes/${activeNoteId}`;
            }
        }

        function updateNoteUrl() {
            const activeNoteId = localStorage.getItem('active_note');
            const urlSpan = document.querySelector('.toolbar-url .url');
            if (!activeNoteId || !urlSpan) {
                urlSpan.textContent = 'Url de la note';
                return;
            }

            let noteElem = document.querySelector(`.note[data-note-id="${activeNoteId}"]`);
            if (!noteElem) {
                urlSpan.textContent = 'Url de la note';
                return;
            }

            let path = [];
            let parent = noteElem.closest('.folder');
            while (parent) {
                const folderHeader = parent.querySelector('.folder-header .folder-name');
                if (folderHeader) {
                    let name = folderHeader.textContent.trim();
                    name = name.replace(/📁\s*/, '');
                    path.unshift(name);
                }
                parent = parent.parentElement.closest('.folder');
            }

            const noteName = noteElem.querySelector('.note-name');
            if (noteName) {
                path.push(noteName.textContent.trim());
            }
            urlSpan.textContent = path.length ? path.join('/') : 'Url de la note';
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
