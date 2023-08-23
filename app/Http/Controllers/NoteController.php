<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userID = Auth::id();
        $notes = Note::where('user_id', $userID)->latest('created_at')->paginate(5);
        return view('notes.index')->with('notes', $notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required | max:120',
            'text' => 'required'
        ]);

        Note::create([
            'uuid'=>Str::uuid(),
            'user_id'=> Auth::id(),
            'title'=>$request->title,
            'text'=>$request->text,

        ]);

        return to_route('notes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note) // Use route model binding
    {
        // $note = Note::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
        if($note->user_id != Auth::id()){
            return abort(403);
        }
        return view('notes.show')->with('note', $note);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }
        return view('notes.edit')->with('note', $note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }

        $request->validate([
            'title'=>'required | max:120',
            'text' => 'required'
        ]);

        $note->update([
            'title'=>$request->title,
            'text'=>$request->text
        ]);

        return to_route('notes.show', $note)->with('success', 'Note Updated Succesfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if($note->user_id != Auth::id()){
            return abort(403);
        }

        $note->delete();
        return to_route('notes.index')->with('success', 'Your Note Deleted Succesfully!');

    }
}
