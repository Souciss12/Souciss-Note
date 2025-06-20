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
            <div class="note-header" title="Rename : F2" draggable="true" data-type="note" data-id="{{ $note->id }}">
                <div>
                    <span class="file-icon">📄</span>
                    <span class="note-name">{{ $note->title }}</span>
                </div>
                <form class="arbo-delete-note-form" method="POST" action="{{ route('note.destroy', $note->id) }}"
                    data-note-id="{{ $note->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Delete" class="btn btn-delete-note">
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
    <div title="Ctrl Shift N" class="context-menu-item add-menu-item d-flex" data-action="add-folder">
        <div class="add-folder-icon">📁</div>
        <div>New folder</div>
    </div>
    <div title="Ctrl Shift M" class="context-menu-item add-menu-item d-flex" data-action="add-note">
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
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
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

        function restoreFolderSelection() {
            const pendingFolderId = localStorage.getItem('pending_selection_folder_id');
            console.log('Restoring folder selection, pending ID:', pendingFolderId);

            if (pendingFolderId) {
                const folderHeader = document.querySelector(`[data-id="${pendingFolderId}"]`);
                console.log('Found folder header for ID:', pendingFolderId, folderHeader);

                if (folderHeader && folderHeader.classList.contains('folder-header')) {
                    document.querySelectorAll('.folder-header').forEach(h => h.classList.remove('selected'));

                    folderHeader.classList.add('selected');

                    const folderNameSpan = folderHeader.querySelector('.folder-name .folder-name');
                    if (folderNameSpan) {
                        const folderName = folderNameSpan.textContent.trim();
                        localStorage.setItem('active_folder', folderName);
                        localStorage.setItem('last_selected_type', 'folder');
                        console.log('Selected folder:', folderName);
                    }
                }
                localStorage.removeItem('pending_selection_folder_id');
                return;
            }

            const activeFolderName = localStorage.getItem('active_folder');
            if (activeFolderName) {
                const folderHeaders = document.querySelectorAll('.folder-header');
                folderHeaders.forEach(header => {
                    const folderNameSpan = header.querySelector('.folder-name .folder-name');
                    if (folderNameSpan) {
                        const folderName = folderNameSpan.textContent.trim();
                        if (folderName === activeFolderName) {
                            header.classList.add('selected');
                            console.log('Restored selection for folder:', folderName);
                        }
                    }
                });
            }
        }

        restoreFolderStates();
        restoreActiveNote();
        restoreFolderSelection();

        const noteArbo = document.querySelector('.note-arbo');
        if (noteArbo) {
            noteArbo.focus();
        }

        folderHeaders.forEach(header => {
            header.addEventListener('click', function(e) {
                const folder = this.closest('.folder');
                const icon = this.querySelector('.open-icon');
                const folderName = this.querySelector('.folder-name').textContent.trim();
                const folderId = 'folder_' + folderName;
                const folderContent = folder.querySelector('.folder-content');

                const isCurrentlyOpen = folderContent.style.display !== 'none';

                if (!isCurrentlyOpen) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                } else {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-right');
                }

                if (folderContent) {
                    const isCurrentlyOpen = folderContent.style.display !== 'none';
                    folderContent.style.display = isCurrentlyOpen ? 'none' : 'block';
                    localStorage.setItem(folderId, isCurrentlyOpen ? 'closed' : 'open');
                }

                document.querySelectorAll('.folder-header').forEach(h => h.classList.remove(
                    'selected'));
                this.classList.add('selected');
                localStorage.setItem('active_folder', folderName);
                localStorage.setItem('last_selected_type', 'folder');
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
                    localStorage.setItem('last_selected_type', 'note');

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

                if (confirm("Are you sure you want to delete this note ?")) {
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
                                    if (window.updateNoteContentContainerVisibility) {
                                        window.updateNoteContentContainerVisibility();
                                    }
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Delete') {
                const active = document.activeElement;
                const isTextInput = (
                    active && (
                        active.tagName === 'INPUT' ||
                        active.tagName === 'TEXTAREA' ||
                        active.isContentEditable
                    )
                );
                if (isTextInput) {
                    return;
                }
                const lastType = localStorage.getItem('last_selected_type');
                if (lastType === 'note') {
                    const activeNote = document.querySelector('.note.active');
                    if (activeNote) {
                        const deleteForm = activeNote.querySelector('.arbo-delete-note-form');
                        if (deleteForm) {
                            e.preventDefault();
                            deleteForm.dispatchEvent(new Event('submit', {
                                cancelable: true,
                                bubbles: true
                            }));
                            return;
                        }
                    }
                } else if (lastType === 'folder') {
                    const selectedFolderHeader = document.querySelector('.folder-header.selected');
                    if (selectedFolderHeader) {
                        const folder = selectedFolderHeader.closest('.folder');
                        if (folder) {
                            const deleteFolderForm = folder.querySelector('.arbo-delete-folder-form');
                            if (deleteFolderForm) {
                                e.preventDefault();
                                deleteFolderForm.dispatchEvent(new Event('submit', {
                                    cancelable: true,
                                    bubbles: true
                                }));
                            }
                        }
                    }
                }
            } else if (e.ctrlKey && e.shiftKey && e.key === 'N') {
                e.preventDefault();
                e.stopPropagation();

                let parentFolderId = null;
                const selectedFolderHeader = document.querySelector('.folder-header.selected');
                if (selectedFolderHeader) {
                    parentFolderId = selectedFolderHeader.dataset.id;
                }

                const folderName = prompt('Folder name:', 'New folder');
                if (folderName && folderName.trim()) {
                    fetch('/folders', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                name: folderName.trim(),
                                parent_id: parentFolderId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.folder) {
                                localStorage.setItem('active_folder', data.folder.name);
                                localStorage.setItem('last_selected_type', 'folder');
                                localStorage.setItem('pending_selection_folder_id', data.folder.id);
                                location.reload();
                            } else if (data.success) {
                                location.reload();
                            } else {
                                alert('Error while creating folder');
                            }
                        })
                        .catch(err => {
                            console.error('Error creating folder:', err);
                            alert('Network error while creating folder');
                        });
                }
            } else if (e.ctrlKey && e.shiftKey && e.key === 'M') {
                e.preventDefault();
                e.stopPropagation();

                let parentFolderId = null;
                const selectedFolderHeader = document.querySelector('.folder-header.selected');
                if (selectedFolderHeader) {
                    parentFolderId = selectedFolderHeader.dataset.id;
                }

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
                            if (data.success && data.note) {
                                localStorage.setItem('active_note', data.note.id);
                                localStorage.setItem('last_selected_type', 'note');
                                location.reload();
                            } else if (data.success) {
                                location.reload();
                            } else {
                                alert('Error while creating note');
                            }
                        });
                }
            } else if (e.key === 'F2') {
                e.preventDefault();
                e.stopPropagation();

                const lastType = localStorage.getItem('last_selected_type');
                if (lastType === 'note') {
                    const activeNote = document.querySelector('.note.active');
                    if (activeNote) {
                        const noteTitleInput = activeNote.querySelector('.note-name');
                        if (noteTitleInput) {
                            const noteTitle = prompt('Rename note:', noteTitleInput.textContent.trim());
                            if (noteTitle) {
                                const noteId = activeNote.dataset.noteId;
                                fetch(`/notes/${noteId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content,
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            title: noteTitle
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            noteTitleInput.textContent = noteTitle;
                                            localStorage.setItem('active_note', noteId);
                                            localStorage.setItem('last_selected_type', 'note');
                                            location.reload();
                                        } else {
                                            alert('Error while renaming note');
                                        }
                                    });
                            }
                        }
                    }
                } else if (lastType === 'folder') {
                    const selectedFolderHeader = document.querySelector('.folder-header.selected');
                    if (selectedFolderHeader) {
                        const folderName = prompt('Rename folder:', selectedFolderHeader.querySelector(
                            '.folder-name .folder-name').textContent.trim());
                        if (folderName) {
                            const folderId = selectedFolderHeader.dataset.id;
                            fetch(`/folders/${folderId}`, {
                                    method: 'PUT',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        name: folderName
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        selectedFolderHeader.querySelector(
                                                '.folder-name .folder-name')
                                            .textContent = folderName;
                                        localStorage.setItem('active_folder', folderName);
                                        localStorage.setItem('last_selected_type', 'folder');
                                    } else {
                                        alert('Error while renaming folder');
                                    }
                                });
                        }
                    }
                }
            }
        });

        const deleteFolderForms = document.querySelectorAll('.arbo-delete-folder-form');

        deleteFolderForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const folderId = this.dataset.folderId;

                if (confirm("Are you sure you want to delete this folder ?")) {
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
                                if (data.success && data.folder) {
                                    localStorage.setItem('active_folder', data.folder.name);
                                    localStorage.setItem('last_selected_type', 'folder');
                                    localStorage.setItem('pending_selection_folder_id', data
                                        .folder.id);
                                    location.reload();
                                } else if (data.success) {
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
