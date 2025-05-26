@vite(['resources/css/note-arbo.css'])

<div class="note-arbo" tabindex="0">
    @foreach ($folders->where('parent_id', null) as $folder)
        @include('components.folder-item', [
            'folder' => $folder,
            'folders' => $folders,
            'notes' => $notes,
        ])
    @endforeach

    @foreach ($notes->where('folder_id', null) as $note)
        <div class="note" data-note-id="{{ $note->id }}">
            <div class="note-header" draggable="true" data-type="note" data-id="{{ $note->id }}">
                <div>
                    <span class="file-icon">📄</span>
                    <span class="note-name">{{ $note->title }}</span>
                </div>
                <form class="arbo-delete-note-form" method="POST" action="{{ route('note.destroy', $note->id) }}"
                    data-note-id="{{ $note->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete-note">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <div id="root-drop-zone" class="root-drop-zone" data-folder-id="">
        Drop here to root
    </div>
</div>
<div id="context-menu" class="context-menu add-menu">
    <div class="context-menu-item add-menu-item d-flex" data-action="add-folder">
        <div class="add-folder-icon">📁</div>
        <div>New folder</div>
    </div>
    <div class="context-menu-item add-menu-item d-flex" data-action="add-note">
        <div class="add-note-icon">📄</div>
        <div>New note</div>
    </div>
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
                        if (window.setNoteContentBody) {
                            window.setNoteContentBody(data.content);
                        } else {
                            const noteContent = document.querySelector('.note-content-body');
                            if (noteContent) {
                                noteContent.value = data.content;
                            }
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
                            if (window.setNoteContentBody) {
                                window.setNoteContentBody(data.content);
                            } else {
                                const noteContent = document.querySelector(
                                    '.note-content-body');
                                if (noteContent) {
                                    noteContent.value = data.content;
                                }
                            }
                        })
                        .catch(error => console.error('Erreur lors du chargement de la note:',
                            error));
                }
            });
        });

        const deleteNoteForms = document.querySelectorAll('.arbo-delete-note-form');

        deleteNoteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const noteId = this.dataset.noteId;

                if (confirm("Voulez-vous vraiment supprimer cette note ?")) {
                    fetch(`/notes/${noteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                const noteElem = this.closest('.note');
                                if (noteElem.classList.contains('active')) {
                                    localStorage.removeItem('active_note');
                                    const noteTitle = document.querySelector(
                                        '.note-content-header');
                                    if (noteTitle) noteTitle.value = '';
                                    const noteContent = document.querySelector(
                                        '.note-content-body');
                                    if (noteContent) noteContent.value = '';
                                }
                                noteElem.remove();
                            } else {
                                alert('Échec de la suppression.');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur de suppression :', error);
                        });
                }
            });
        });

        const noteArbo = document.querySelector('.note-arbo');
        noteArbo.addEventListener('keydown', function(e) {
            if (e.key === 'Delete') {
                const activeNote = document.querySelector('.note.active');
                if (activeNote) {
                    const deleteForm = activeNote.querySelector('.arbo-delete-note-form');
                    if (deleteForm) {
                        deleteForm.dispatchEvent(new Event('submit', {
                            cancelable: true,
                            bubbles: true
                        }));
                    }
                }
            }
        });

        const deleteFolderForms = document.querySelectorAll('.arbo-delete-folder-form');

        deleteFolderForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const folderId = this.dataset.folderId;

                if (confirm("Voulez-vous vraiment supprimer cette note ?")) {
                    fetch(`/folders/${folderId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                const folderElem = this.closest('.folder');
                                const activeNoteId = localStorage.getItem('active_note');
                                if (activeNoteId) {
                                    if (folderElem.querySelector(
                                            `.note[data-note-id="${activeNoteId}"]`)) {
                                        localStorage.removeItem('active_note');
                                        const noteTitle = document.querySelector(
                                            '.note-content-header');
                                        if (noteTitle) noteTitle.value = '';
                                        const noteContent = document.querySelector(
                                            '.note-content-body');
                                        if (noteContent) noteContent.value = '';

                                    }
                                }
                                folderElem.remove();
                            } else {
                                alert('Échec de la suppression.');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur de suppression :', error);
                        });
                }
            });
        });

        document.querySelectorAll('.note-header, .folder-header').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('type', this.dataset.type);
                e.dataTransfer.setData('id', this.dataset.id);
            });
        });

        document.querySelectorAll('.folder-header').forEach(folder => {
            folder.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            folder.addEventListener('dragleave', function(e) {
                this.classList.remove('drag-over');
            });
            folder.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                const type = e.dataTransfer.getData('type');
                const id = e.dataTransfer.getData('id');
                const targetFolderId = this.dataset.id;

                fetch('/move', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            type: type,
                            id: id,
                            target_folder_id: targetFolderId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location
                                .reload();
                        } else {
                            alert('Erreur lors du déplacement');
                        }
                    });
            });
        });

        const arboBg = document.getElementById('root-drop-zone');

        arboBg.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        arboBg.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        arboBg.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const type = e.dataTransfer.getData('type');
            const id = e.dataTransfer.getData('id');
            const targetFolderId = this.dataset.id;

            fetch('/move', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: type,
                        id: id,
                        target_folder_id: targetFolderId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location
                            .reload();
                    } else {
                        alert('Erreur lors du déplacement');
                    }
                });
        });

        const contextMenu = document.getElementById('context-menu');
        let contextMenuTarget = null;

        document.querySelectorAll('.add-icon').forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.stopPropagation();
                contextMenuTarget = this;
                const rect = this.getBoundingClientRect();
                contextMenu.style.left = rect.left + window.scrollX + 'px';
                contextMenu.style.top = rect.bottom + window.scrollY + 'px';
                contextMenu.style.display = 'block';
            });
        });

        document.querySelectorAll('.add-icon-root').forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.stopPropagation();
                contextMenuTarget = this;
                const rect = this.getBoundingClientRect();
                contextMenu.style.left = rect.left + window.scrollX + 'px';
                contextMenu.style.top = rect.bottom + window.scrollY + 'px';
                contextMenu.style.display = 'block';
            });
        });

        contextMenu.querySelectorAll('.context-menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const action = this.dataset.action;
                let parentFolderId = null;

                if (contextMenuTarget) {
                    const folderHeader = contextMenuTarget.closest('.folder-header');
                    if (folderHeader) {
                        parentFolderId = folderHeader.dataset.id;
                    }
                }

                if (action === 'add-folder') {
                    const folderName = prompt('Folder name:', 'New folder');
                    if (folderName) {
                        fetch('/folders', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    name: folderName,
                                    parent_id: parentFolderId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Error while creating folder');
                                }
                            });
                    }
                } else if (action === 'add-note') {
                    const noteTitle = prompt('Note title:', 'New note');
                    if (noteTitle) {
                        fetch('/notes', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    title: noteTitle,
                                    folder_id: parentFolderId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Error while creating note');

                                }
                            });
                    }
                }

                contextMenu.style.display = 'none';
            });
        });

        document.addEventListener('click', function() {
            contextMenu.style.display = 'none';
        });
    });
</script>
