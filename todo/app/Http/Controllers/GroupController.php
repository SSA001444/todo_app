<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    public function index()
    {
        // Passing variable to the view for database relationship
        $groups = Group::where('user_id', auth()->id())->get();
        return view('todo.createGroup', compact('groups'));

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('groups.index')->withErrors($validator);
        }

        Group::create([
            'name' => $request->get('name'),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('groups.index')->with('success', 'Inserted');
    }

    public function edit(string $id)
    {
        // Passing variable to the edit view to display available groups to the user
        $group = Group::where('id', $id)->where('user_id', auth()->id())->first();
        return view('todo.editGroup', compact('group'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('groups.edit',['group'=>$id])->withErrors($validator);
        }

        $group = Group::where('id', $id)->first();
        $group->name = $request->get('name');
        $group->save();

        return redirect()->route('groups.index')->with('success','Updated Group');
    }

    public function destroy(string $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return redirect()->route('groups.index')->with('error', 'Group not found');
        }
        // Find all groups connection to todos and setting group_id to null to prevent database error
        $group->todo()->update(['group_id' => null]);
        $group->delete();

        return redirect()->route('groups.index')->with('success','Deleted Group');
    }

    public function reorder(Request $request)
    {
        $groupId = $request->input('groupId');

        return response()->json(['message' => 'Order updated successfully']);
    }
    public function loadGroups()
    {
        $group = Group::all();
        return response()->json($group);
    }
}
