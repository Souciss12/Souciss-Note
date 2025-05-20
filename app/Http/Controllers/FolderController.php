<?php

namespace App\Http\Controllers;

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

    /**
     * Remove the specified resource from storage.
     */
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
