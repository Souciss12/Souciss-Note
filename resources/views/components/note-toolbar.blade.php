@vite(['resources/css/note-toolbar.css'])
<div class="note-toolbar">
    <div class="toolbar-url">
        <span class="url">/</span>
    </div>
    <div class="btns-toolbar">
        <button class="btn btn-toolbar" id="download-content-btn">
            <i class="bi bi-download"></i>
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
                urlSpan.textContent = '/';
                return;
            }

            let noteElem = document.querySelector(`.note[data-note-id="${activeNoteId}"]`);
            if (!noteElem) {
                urlSpan.textContent = '/';
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
            urlSpan.textContent = path.length ? path.join('/') : '';
        }

        const clearContentBtn = document.getElementById('clear-content-btn');
        clearContentBtn.addEventListener('click', function() {
            const activeNoteId = localStorage.getItem('active_note');
            if (!activeNoteId) return;

            if (window.easyMDE) {
                window.easyMDE.value('');
            } else {
                const noteContent = document.querySelector('.note-content-body');
                if (noteContent) noteContent.value = '';
            }

            if (!confirm(
                    `Are you sure you want to clear the note ?`
                )) {
                return;
            }

            fetch(`/notes/${activeNoteId}/update-content`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        content: ''
                    })
                })
                .then(() => location.reload())
                .catch(error => console.error('Erreur lors de la suppression du contenu:', error));
        });

        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const activeNoteId = localStorage.getItem('active_note');
            if (!activeNoteId) return;

            const noteElem = document.querySelector(`.note[data-note-id="${activeNoteId}"]`);
            const noteName = noteElem.querySelector('.note-name')?.textContent?.trim();

            if (!confirm(
                    `Are you sure you want to delete "${noteName}" ?`
                )) {
                return;
            }

            fetch(`/notes/${activeNoteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(() => {
                    localStorage.removeItem('active_note');
                    location.reload();
                })
                .catch(error => console.error('Erreur lors de la suppression de la note:', error));
        });

        document.getElementById('download-content-btn').addEventListener('click', async () => {
            const activeNoteId = localStorage.getItem('active_note');
            if (!activeNoteId) return;

            const title = document.getElementById('note-content-header').value;
            const content = marked.parse(easyMDE.value());

            const container = Object.assign(document.createElement('div'), {
                innerHTML: `<h1 style="text-align:center;">${title}</h1><div>${content}</div>`,
                style: `
            width: 210mm;
            padding: 70px;
            font-family: Verdana, sans-serif;
            font-size: 12pt;
            position: fixed;
            top: -10000px;
            background: white;
        `
            });

            document.body.appendChild(container);

            const images = container.querySelectorAll('img');
            await Promise.all(Array.from(images).map(img => {
                return new Promise(resolve => {
                    if (img.complete) resolve();
                    else img.onload = img.onerror = resolve;
                });
            }));

            try {
                const canvas = await html2canvas(container, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: false
                });

                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = 210;
                const pdfHeight = 297;

                const imgProps = pdf.getImageProperties(imgData);
                const imgHeight = (imgProps.height * pdfWidth) / imgProps.width;

                let position = 0;

                while (position < imgHeight) {
                    if (position > 0) pdf.addPage();

                    pdf.addImage(
                        imgData,
                        'PNG',
                        0,
                        -position,
                        pdfWidth,
                        imgHeight
                    );

                    position += pdfHeight;
                }

                pdf.save('note.pdf');
            } finally {
                document.body.removeChild(container);
            }
        });

    });
</script>
