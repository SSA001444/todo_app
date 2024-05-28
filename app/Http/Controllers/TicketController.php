<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        $tickets = Ticket::with('user', 'tags')->get();
        return view('tickets.index', compact('tickets', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'team_id' => $validated['team_id'],
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        $ticket->tags()->sync($request->tags);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');
    }
}