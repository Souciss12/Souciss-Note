<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', Auth::user()->id)->orderBy('title', 'asc')->get();
        $folders = Folder::where('user_id', Auth::user()->id)->orderBy('name', 'asc')->get();

        return view('note.index', ['notes' => $notes, 'folders' => $folders]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json(['notes' => []]);
        }

        $notes = Note::search($query, Auth::user()->id);

        return response()->json([
            'notes' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'folder_id' => $note->folder_id,
                    'folder_name' => $note->folder ? $note->folder->name : null,
                ];
            })
        ]);
    }

    public function getContent(string $id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        return response()->json([
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
            'created_at' => $note->created_at,
            'updated_at' => $note->updated_at
        ]);
    }

    public function updateContent(Request $request, string $id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $note->content = $request->input('content');
        $note->save();
    }

    public function updateTitle(Request $request, string $id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $note->title = $request->input('title');
        $note->save();
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['success' => true]);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $note = new Note();
        $note->title = $request->input('title', 'Nouvelle note');
        $note->content = $request->input('content', '');
        $note->folder_id = $request->input('folder_id');
        $note->user_id = Auth::user()->id;
        $note->save();

        return response()->json([
            'success' => true,
            'note' => $note
        ]);
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
        $note = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $note->title = $request->input('title');
        $note->save();

        return response()->json([
            'success' => true,
            'note' => $note
        ]);
    }
}
