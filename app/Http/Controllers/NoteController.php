<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    public function destroy(string $id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $note->delete();
        return redirect()->route('note.index')
            ->with('success', "Element deleted successfully");
    }
}
