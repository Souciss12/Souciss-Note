<div class="folder">
    <div class="folder-header">
        <div class="folder-name">
            <span class="open-icon bi bi-caret-right"></span>
            <span class="folder-icon">📁</span>
            <span class="folder-name">{{ $folder->name }}</span>
        </div>
        <a class="btn-delete-folder bi bi-trash-fill"></a>
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
            <div class="note-header">
                <div class="note" data-note-id="{{ $note->id }}">
                    <div class="note-header">
                        <div>
                            <span class="file-icon">📄</span>
                            <span class="note-name">{{ $note->title }}</span>
                        </div>
                        <a class="btn-delete-note bi bi-trash-fill"></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
