<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function move(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $targetFolderId = $request->input('target_folder_id');

        if ($type === 'note') {
            $note = Note::find($id);
            if ($note) {
                $note->folder_id = $targetFolderId;
                $note->save();
                return response()->json(['success' => true]);
            }
        } elseif ($type === 'folder') {
            $folder = Folder::find($id);
            if ($folder) {
                $folder->parent_id = $targetFolderId;
                $folder->save();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false], 400);
    }

    public function destroy(Folder $folder)
    {
        // Supprimer récursivement les sous-dossiers et leurs notes
        $this->deleteFolderRecursively($folder);
        return response()->json(['success' => true]);
    }

    private function deleteFolderRecursively(Folder $folder)
    {
        // Supprimer toutes les notes du dossier
        foreach ($folder->notes as $note) {
            $note->delete();
        }
        // Supprimer tous les sous-dossiers récursivement
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursively($child);
        }
        // Supprimer le dossier lui-même
        $folder->delete();
    }
}
