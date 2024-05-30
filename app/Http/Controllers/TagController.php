<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') {

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            Tag::create([
                'name' => $request->name,
                'team_id' => Auth::user()->team_id
            ]);

            return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
        } else {
            return back()->with('error', 'You are not authorized to create new tag');
        }
    }

    public function edit($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tags.index')->with('error', 'Tag not found');
        }

        return response()->json($tag);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tags.index')->with('error', 'Tag not found');
        }

        if (($tag->team_id == Auth::user()->team_id) && (Auth::user()->role == 'moderator' || Auth::user()->role == 'admin')) {

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag->update($request->all());

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
        } else {
            return redirect()->route('tags.index')->with('error', 'Tag not found');
        }
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        $user = Auth::user();
        if (!$tag) {
            return redirect()->route('tags.index')->with('error', 'Tag not found');
        }

        if (($tag->team_id == $user->team_id) && ($user->role == 'moderator' || $user->role == 'admin') ) {

            $tag->deleted_by = $user->username;
            $tag->deleted_at = now();
            $tag->save();

            return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
        } else {
            return redirect()->route('tags.index')->with('error', 'Tag not found');
        }
    }
}