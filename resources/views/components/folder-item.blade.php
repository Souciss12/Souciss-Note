<div class="folder" data-folder-id="{{ $folder->id }}">
    <div class="folder-header" draggable="true" data-type="folder" data-id="{{ $folder->id }}">
        <div class="folder-name">
            <span class="open-icon bi bi-chevron-right"></span>
            <span class="folder-icon">📁</span>
            <span class="folder-name">{{ $folder->name }}</span>
        </div>
        <div class="d-flex">
            <span class="add-icon bi bi-plus-square"></span>
            <form class="arbo-delete-folder-form" method="POST" data-folder-id="{{ $folder->id }}"
                action="{{ route('folder.destroy', $folder->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete-folder">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>
        </div>
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
                <div class="note-header" draggable="true" data-type="note" data-id="{{ $note->id }}">
                    <div>
                        <span class="file-icon">📄</span>
                        <span class="note-name">{{ $note->title }}</span>
                    </div>
                    <form class="arbo-delete-note-form" method="POST" data-note-id="{{ $note->id }}"
                        action="{{ route('note.destroy', $note->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete-note">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
