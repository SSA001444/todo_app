<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::where('team_id', Auth::user()->team_id)->get();
        return view('admin.admin', compact('users'));
    }

    public function inviteUser(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        if ($user) {
            if ($user->team_id) {
                return redirect()->route('admin.users')->withErrors(['User is already in a team.']);
            }

            $user->team_id = Auth::user()->team_id;
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User added to the team successfully.');
        } else {
            return redirect()->route('admin.users')->withErrors(['User with this email or username not found.']);
        }
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['user', 'moderator'])],
        ]);

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.users')->withErrors(['You do not have permission to change roles.']);
        }

        if ($user->id == Auth::user()->id) {
            return redirect()->route('admin.users')->withErrors(['You cannot change your own role.']);
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User role updated successfully.');
    }

    public function removeUser(User $user)
    {
        if (Auth::user()->role !== 'admin' && ($user->role === 'admin' || $user->role === 'moderator')) {
            return redirect()->route('admin.users')->withErrors(['You do not have permission to remove this user.']);
        }

        if ($user->id == Auth::user()->id) {
            return redirect()->route('admin.users')->withErrors(['You cannot remove yourself.']);
        }

        $user->team_id = null;
        $user->role = 'user';
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User removed from the team successfully.');
    }
}
