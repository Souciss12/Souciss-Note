@vite(['resources/css/note-arbo.css'])

<div class="note-arbo">
    @foreach ($folders->where('parent_id', null) as $folder)
        @include('components.folder-item', [
            'folder' => $folder,
            'folders' => $folders,
            'notes' => $notes,
        ])
    @endforeach

    @foreach ($notes->where('folder_id', $folder->id) as $note)
        <div class="note-header">
            <div class="note" data-note-id="{{ $note->id }}">
                <div class="note-header">
                    <div>
                        <span class="file-icon">📄</span>
                        <span class="note-name">{{ $note->title }}</span>
                    </div>
                    <form id="arbo-delete-note-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete-note">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const folderHeaders = document.querySelectorAll('.folder-header');

        function restoreFolderStates() {
            const folders = document.querySelectorAll('.folder');
            folders.forEach(folder => {
                const icon = folder.querySelector('.open-icon');
                const folderName = folder.querySelector('.folder-name').textContent.trim();
                const folderId = 'folder_' + folderName;
                const folderContent = folder.querySelector('.folder-content');

                const isOpen = localStorage.getItem(folderId) === 'open';

                if (folderContent && isOpen) {
                    folderContent.style.display = 'block';
                    icon.classList.remove('bi-caret-right');
                    icon.classList.add('bi-caret-down');
                }
            });
        }

        function restoreActiveNote() {
            const activeNoteId = localStorage.getItem('active_note');
            if (activeNoteId) {
                const note = document.querySelector(`.note[data-note-id="${activeNoteId}"]`);
                if (note) {
                    note.classList.add('active');
                }

                fetch(`/notes/${activeNoteId}/content`)
                    .then(response => response.json())
                    .then(data => {
                        const noteTitle = document.querySelector('.note-content-header');
                        if (noteTitle) {
                            noteTitle.value = data.title;
                        }
                        const noteContent = document.querySelector('.note-content-body');
                        if (noteContent) {
                            noteContent.value = data.content;
                        }
                    })
                    .catch(error => console.error('Erreur lors du chargement de la note:', error));
            }
        }

        restoreFolderStates();
        restoreActiveNote();

        folderHeaders.forEach(header => {
            header.addEventListener('click', function(e) {
                const folder = this.closest('.folder');
                const icon = this.querySelector('.open-icon');
                const folderName = this.querySelector('.folder-name').textContent.trim();
                const folderId = 'folder_' + folderName;
                const folderContent = folder.querySelector('.folder-content');

                const isCurrentlyOpen = folderContent.style.display !== 'none';

                if (!isCurrentlyOpen) {
                    icon.classList.remove('bi-caret-right');
                    icon.classList.add('bi-caret-down');
                } else {
                    icon.classList.remove('bi-caret-down');
                    icon.classList.add('bi-caret-right');
                }

                if (folderContent) {
                    const isCurrentlyOpen = folderContent.style.display !== 'none';
                    folderContent.style.display = isCurrentlyOpen ? 'none' : 'block';

                    localStorage.setItem(folderId, isCurrentlyOpen ? 'closed' : 'open');
                }
            });
        });

        const notes = document.querySelectorAll('.note');
        notes.forEach(note => {
            note.addEventListener('click', function(e) {
                notes.forEach(n => n.classList.remove('active'));
                this.classList.add('active');

                const noteId = this.dataset.noteId;
                if (noteId) {
                    localStorage.setItem('active_note', noteId);

                    fetch(`/notes/${noteId}/content`)
                        .then(response => response.json())
                        .then(data => {
                            const noteTitle = document.querySelector(
                                '.note-content-header');
                            if (noteTitle) {
                                noteTitle.value = data.title;
                            }
                            const noteContent = document.querySelector(
                                '.note-content-body');
                            if (noteContent) {
                                noteContent.value = data.content;
                            }
                        })
                        .catch(error => console.error('Erreur lors du chargement de la note:',
                            error));
                }
            });
        });

        const deleteForm = document.getElementById('arbo-delete-note-form');

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
    });
</script>
