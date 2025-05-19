<div class="folder">
    <div class="folder-header">
        <span class="folder-icon">📁</span>
        <span class="folder-name">{{ $folder->name }}</span>
    </div>

    <div class="folder-content">
        {{-- Afficher les sous-dossiers de ce dossier --}}
        @foreach ($folders->where('parent_id', $folder->id) as $subfolder)
            @include('components.folder-item', [
                'folder' => $subfolder,
                'folders' => $folders,
                'notes' => $notes,
            ])
        @endforeach

        {{-- Afficher les notes de ce dossier --}}
        @foreach ($notes->where('folder_id', $folder->id) as $note)
            <div class="note">
                <span class="file-icon">📄</span>
                <span class="note-name">{{ $note->title }}</span>
            </div>
        @endforeach
    </div>
</div>
