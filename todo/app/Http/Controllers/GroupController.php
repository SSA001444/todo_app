<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    public function index()
    {
        $groups=Group::where('user_id', auth()->id())->get();
        return view('todo.createGroup', compact('groups'));

    }
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required'
            ]);

        if($validator->fails())
        {
            return redirect()->route('groups.index')->withErrors($validator);
        }
        Group::create([
            'name'=>$request->get('name'),
            'user_id'=>auth()->id(),
        ]);
        return redirect()->route('groups.index')->with('success', 'Inserted');
    }

    public function edit(string $id)
    {
        $group=Group::where('id',$id)->where('user_id', auth()->id())->first();
        return view('todo.editGroup', compact('group'));
    }
    public function update(Request $request, string $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails())
        {
            return redirect()->route('groups.edit',['group'=>$id])->withErrors($validator);
        }

        $group=Group::where('id', $id)->first();
        $group->name=$request->get('name');
        $group->save();

        return redirect()->route('groups.index')->with('success','Updated Group');
    }
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->todo()->update(['group_id' => null]);
        $group->delete();
        return redirect()->route('groups.index')->with('success','Deleted Group');
    }
}
