@vite(['resources/css/note-search.css'])
<div class="note-search">
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="Research notes..." autocomplete="off">
        <div id="search-results" class="search-results" style="display: none;"></div>
    </div>
    <div class="btns">
        <span class="add-icon-root bi bi-plus"></span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/notes/search?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data.notes);
                    })
                    .catch(error => {
                        console.error('Erreur de recherche:', error);
                    });
            }, 200);
        });

        function displaySearchResults(notes) {

            const resultsHtml = notes.map(note => `
            <div class="search-result-item" data-note-id="${note.id}">
                <div class="search-result-title">📄 ${note.title}</div>
            </div>
        `).join('');

            searchResults.innerHTML = resultsHtml;
            searchResults.style.display = 'block';

            searchResults.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    const noteId = this.dataset.noteId;
                    selectNote(noteId);
                    searchInput.value = '';
                    searchResults.style.display = 'none';
                });
            });
        }

        function selectNote(noteId) {
            document.querySelectorAll('.note').forEach(n => n.classList.remove('active'));

            const noteElement = document.querySelector(`.note[data-note-id="${noteId}"]`);
            if (noteElement) {
                noteElement.classList.add('active');

                localStorage.setItem('active_note', noteId);
                localStorage.setItem('last_selected_type', 'note');

                fetch(`/notes/${noteId}/content`)
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
                        if (window.easyMDE && window.easyMDE.codemirror) {
                            window.easyMDE.codemirror.focus();
                        }
                    })
                    .catch(error => console.error('Erreur lors du chargement de la note:', error));
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            const items = searchResults.querySelectorAll('.search-result-item');
            let selectedIndex = Array.from(items).findIndex(item => item.classList.contains(
                'selected'));

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (selectedIndex < items.length - 1) {
                    if (selectedIndex >= 0) items[selectedIndex].classList.remove('selected');
                    items[selectedIndex + 1].classList.add('selected');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (selectedIndex > 0) {
                    items[selectedIndex].classList.remove('selected');
                    items[selectedIndex - 1].classList.add('selected');
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0) {
                    items[selectedIndex].click();
                }
            } else if (e.key === 'Escape') {
                searchResults.style.display = 'none';
                searchInput.blur();
            }
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'r') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    });
</script>
