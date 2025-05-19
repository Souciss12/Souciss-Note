<div class="note-arbo">
    {{-- Dossiers racines --}}
    @foreach ($folders->where('parent_id', null) as $folder)
        @include('components.folder-item', [
            'folder' => $folder,
            'folders' => $folders,
            'notes' => $notes,
        ])
    @endforeach

    {{-- Notes sans dossiers --}}
    @foreach ($notes->where('folder_id', null) as $note)
        <div class="note" data-note-id="{{ $note->id }}">
            <span class="file-icon">📄</span>
            <span>{{ $note->title }}</span>
        </div>
    @endforeach
</div>

<style>
    .note-arbo {
        padding: 10px;
        background-color: #F5F3FF;
        min-height: 100%;
        min-width: 100%;
        color: #1F2937;
        border-right: 2px solid #A3A3A3;
        border-top: 2px solid #A3A3A3;
    }

    .folder {
        padding: 2px 0px 2px 0px;
        margin: 2px 0;
        border-radius: 4px;
    }

    .note {
        padding: 2px 0px 2px 5px;
        margin: 2px 0;
        border-radius: 4px;
    }

    .folder-content {
        margin-left: 6px;
        padding-left: 6px;
    }

    .note:hover,
    .folder-header:hover {
        cursor: pointer;
        background-color: #DDD6FE;
        color: #1F2937;
    }

    .folder-icon,
    .file-icon {
        margin-right: 3px;
    }

    .folder-name,
    .note-name {
        cursor: pointer;
    }

    .active {
        background-color: #A78BFA;
        color: #F5F3FF;
    }

    .folder-header {
        display: flex;
        border-radius: 4px;
        padding: 2px 2px 2px 2px;
        justify-content: space-between;
    }
</style>

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
                        const noteTitle = document.querySelector(
                            '.note-content-header');
                        if (noteTitle) {
                            noteTitle.innerHTML = `
                                    <h2 class="fw-semibold">${data.title}</h2>
                                `;
                        }
                        const noteContent = document.querySelector(
                            '.note-content-body');
                        if (noteContent) {
                            noteContent.value = data.content;
                            if (window.easymde) {
                                window.easymde.value(data.content);
                            }
                        }

                    })
                    .catch(error => console.error('Erreur lors du chargement de la note:',
                        error));
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
                                noteTitle.innerHTML = `
                                    <h2 class="fw-semibold">${data.title}</h2>
                                `;
                            }
                            const noteContent = document.querySelector(
                                '.note-content-body');
                            if (noteContent) {
                                noteContent.value = data.content;
                                if (window.easymde) {
                                    window.easymde.value(data.content);
                                }
                            }

                        })
                        .catch(error => console.error('Erreur lors du chargement de la note:',
                            error));
                }
            });
        });
    });
</script>
