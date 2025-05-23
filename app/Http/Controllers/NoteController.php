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
        $notes = Note::where('user_id', Auth::user()->id)->get();
        $folders = Folder::where('user_id', Auth::user()->id)->get();

        return view('note.index', ['notes' => $notes, 'folders' => $folders]);
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
        //
    }
}
