<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatContact;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

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

        $identifier = $request->input('identifier');

        $users = User::all();

        $user = $users->first(function ($user) use ($identifier) {
                $decryptedEmail = Crypt::decryptString($user->email);
                $decryptedUsername = Crypt::decryptString($user->username);
                return $decryptedEmail === $identifier || $decryptedUsername === $identifier;
        });

        if ($user) {
            if ($user->team_id) {
                return redirect()->route('admin.users')->withErrors([__('messages.user_already_in_team')]);
            }

            //Create a chat ( not working due fatal error )
//            $chat = ChatContact::create([
//                'team_id' => Auth::user()->team_id,
//                'name' => Crypt::encryptString($user->username)
//            ]);

            $user->team_id = Auth::user()->team_id;
//            $user->chat_id = $chat->id;
            $user->save();

            return redirect()->route('admin.users')->with('success', __('messages.user_added_success'));
        } else {
            return redirect()->route('admin.users')->withErrors([__('messages.user_not_found')]);
        }
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['user', 'moderator'])],
        ]);

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.users')->withErrors([__('messages.no_permission_change_roles')]);
        }

        if ($user->id == Auth::user()->id) {
            return redirect()->route('admin.users')->withErrors([__('messages.cannot_change_own_role')]);
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')->with('success', __('messages.user_role_updated'));
    }

    public function removeUser(User $user)
    {
        if (Auth::user()->role !== 'admin' && ($user->role === 'admin' || $user->role === 'moderator')) {
            return redirect()->route('admin.users')->withErrors([__('messages.permission_denied')]);
        }

        if ($user->id == Auth::user()->id) {
            return redirect()->route('admin.users')->withErrors([__('messages.cannot_remove_self')]);
        }

//        $chat = ChatContact::where('name', Crypt::decryptString($user->username))->first();

 //       $chat->team_id = null;
 //       $chat->save();

        $user->team_id = null;
//        $user->chat_id = null;
        $user->role = 'user';
        $user->save();

        return redirect()->route('admin.users')->with('success', __('messages.user_removed_success'));
    }
}
