<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<div class="container note-content mt-3">
    <div class="note-content-header">
        <h2>Note Content</h2>
    </div>
    <textarea class="note-content-body" id="note-content-area"></textarea>
</div>

<style>
    .note-content {
        background-color: #FFFFFF;
        border-radius: 8px;
        padding: 20px;
    }

    .note-content-header {
        justify-content: center;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.easymde = new EasyMDE({
            element: document.getElementById('note-content-area'),
            spellChecker: false,
            autofocus: true,
            toolbar: ["bold", "italic", "heading", "|", "unordered-list", "ordered-list", "|",
                "link", "image"
            ]
        });
    });
</script>
