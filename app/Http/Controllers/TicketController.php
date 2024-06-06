<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

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

        return redirect()->route('tickets.index')->with('success', __('messages.ticket_created'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['tasks', 'comments' => function ($query) {
            $query->withTrashed()->with('user');
        }])->findOrFail($id);
        $tasks = $ticket->tasks;
        $comments = $ticket->comments;

        return view('tickets.show', compact('ticket', 'tasks', 'comments'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'task' => 'required|string|max:255',
        ]);

        Task::create([
            'ticket_id' => $request->ticket_id,
            'name' => $request->task,
            'completed' => false,
        ]);

        return back()->with('success', __('messages.task_added'));
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'comment' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = 'comment_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'comment_photos/' . $imageName;

            $image->move(public_path('comment_photos'), $imageName);

            $path = '/' . $imagePath;
        }

        Comment::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => auth()->id(),
            'text' => $request->comment,
            'photo' => $path,
        ]);

        return back()->with('success', __('messages.comment_added'));
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);

        if (($comment->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') && $comment->ticket->team_id == Auth::user()->team_id )
        {
            $comment->text = $request->comment;
            $comment->save();

            return back()->with('success', __('messages.comment_updated'));
        }

        return back()->with('error', __('messages.unauthorized_update_comment'));
    }

    public function destroyComment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if (($comment->user_id == Auth::id() || $user->role == 'moderator' || $user->role == 'admin') && $comment->ticket->team_id == $user->team_id )
        {
            $comment->deleted_by = $user->username;
            $comment->deleted_by_role = $user->role;
            $comment->deletion_reason = $request->reason;
            $comment->deleted_at = now();
            $comment->save();

            return back()->with('success', __('messages.comment_deleted'));
        }

        return back()->with('error', __('messages.unauthorized_delete_comment'));
    }

    public function updateTask(Request $request, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            $request->validate([
                'completed' => 'required|boolean',
            ]);

            $task->completed = filter_var($request->completed, FILTER_VALIDATE_BOOLEAN);
            $task->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        if (($ticket->user_id == Auth::id() || $user->role == 'moderator' || $user->role == 'admin') && $ticket->team_id == $user->team_id )
        {
            $ticket->deleted_by = $user->username;
            $ticket->deleted_by_role = $user->role;
            $ticket->deletion_reason = $request->reason;
            $ticket->deleted_at = now();
            $ticket->save();

            return back()->with('success', __('messages.ticket_deleted'));
        }

        return back()->with('error', __('messages.unauthorized_delete_ticket'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        if (($ticket->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') && $ticket->team_id == Auth::user()->team_id )
        {
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->save();

            return back()->with('success', __('messages.ticket_updated'));
        }
        return back()->with('error', __('messages.unauthorized_update_ticket'));
    }

    public function toggleStatus($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ((Auth::user()->role == 'admin' || Auth::user()->role == 'moderator' || Auth::id() == $ticket->user_id) && $ticket->team_id == Auth::user()->team_id) {
            $ticket->status = $ticket->status == 'open' ? 'closed' : 'open';
            $ticket->save();

            session()->flash('success', __('messages.ticket_status_updated'));

            return response()->json([
                'success' => true,
                'status' => $ticket->status,
            ]);
        }

        session()->flash('error', __('messages.not_authorized'));

        return response()->json(['success' => false], 403);
    }
}