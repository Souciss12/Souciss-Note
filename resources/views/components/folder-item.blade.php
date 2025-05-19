<div class="folder">
    <div class="folder-header">
        <div class="folder-name">
            <span class="folder-icon">📁</span>
            <span class="folder-name">{{ $folder->name }}</span>
        </div>
        <span class="open-icon bi bi-caret-right"></span>
    </div>

    <div class="folder-content" style="display: none;">
        @foreach ($folders->where('parent_id', $folder->id) as $subfolder)
            @include('components.folder-item', [
                'folder' => $subfolder,
                'folders' => $folders,
                'notes' => $notes,
            ])
        @endforeach

        @foreach ($notes->where('folder_id', $folder->id) as $note)
            <div class="note" data-note-id="{{ $note->id }}">
                <span class="file-icon">📄</span>
                <span class="note-name">{{ $note->title }}</span>
            </div>
        @endforeach
    </div>
</div>
