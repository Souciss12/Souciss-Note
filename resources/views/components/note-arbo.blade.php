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
        <div class="note">
            <span class="file-icon">📄</span>
            <span>{{ $note->title }}</span>
        </div>
    @endforeach
</div>

<style>
    .folder,
    .note {
        padding: 5px;
        margin: 2px 0;
        border-radius: 4px;
    }

    .folder:hover,
    .note:hover {
        background-color: #f5f5f5;
    }

    .folder-content {
        margin-left: 20px;
        border-left: 1px dashed #ccc;
        padding-left: 10px;
    }

    .folder-icon,
    .file-icon {
        margin-right: 5px;
    }

    .folder-name,
    .note-name {
        cursor: pointer;
    }
</style>
