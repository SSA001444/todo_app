<?php

namespace App\Http\Controllers;

use App\Mail\ShareTodo;
use App\Models\Group;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::where('user_id', auth()->id())->get();
        $groups = Group::where('user_id', auth()->id())->get();
        return view('todo.todo', compact('todos', 'groups'));
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required',

        ]);

        if ($validator->fails()) {
            return redirect()->route('todos.index')->withErrors($validator);
        }

        Todo::create([
            'title' => $request->get('title'),
            'group_id' => $request->get('group_id'),
            'commentary'=>$request->get('commentary'),
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('todos.index')->with('success', 'Inserted');
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
        $todo = Todo::where('id', $id)->first();
        return view('todo.editTodo', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('todos.edit', ['todo' => $id])->withErrors($validator);
        }

        $todo = Todo::where('id', $id)->first();
        $todo->title = $request->get('title');
        $todo->commentary = $request->get('commentary');
        $todo->is_completed = $request->get('is_completed');
        $todo->group_id = $request->get('group_id');
        $todo->save();

        return redirect()->route('todos.index')->with('success', 'Updated Todo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Todo::where('id', $id)->delete();
        return redirect()->route('todos.index')->with('success', 'Deleted Todo');
    }

    public function shareForm(Todo $todo)
    {
        return view('todo.shareTodo', compact('todo'));
    }

    public function share(Request $request, Todo $todo)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $recipientEmail = $request->input('email');
        $recipientUser = User::where('email', $recipientEmail) ->first();
        if(!$recipientUser)
        {
            return back()->withErrors('User with this email does not found!');
        }
        if($recipientUser == auth()->user())
        {
            return back()->withErrors('You cannot share a todo with yourself!');

        }
        Todo::create([
            'title'=>$todo->title,
            'is_completed'=>$todo->is_completed,
            'group_id'=>$todo->group_id,
            'commentary'=>$todo->commentary,
            'user_id'=>$recipientUser->id,
            'shared_from'=>User::where('id', $todo->user_id)->first()->email,
        ]);
        $shareTodo = new ShareTodo($todo);
        Mail::to($recipientEmail)->send($shareTodo);

        return redirect()->route('todos.index')->with('success', 'Todo is shared');
    }

}
