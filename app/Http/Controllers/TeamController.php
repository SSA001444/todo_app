<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\User;
use App\Models\ChatContact;
use Illuminate\Support\Facades\Crypt;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'teamName' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if ($user->team_id) {
            return redirect()->route('team-status')->withErrors(['team' => __('messages.team_creation_failed')]);
        }

        $team = Team::create([
            'name' => $request->teamName,
        ]);

//        $chat = ChatContact::create([
//            'team_id' => $team->id,
//            'name' => Crypt::encryptString($user->username),
//        ]);

         ChatContact::create([
            'team_id' => $team->id,
            'name' => Crypt::encryptString($team->name . ' Chat')
        ]);

        $user->team_id = $team->id;
//        $user->chat_id = $chat->id;
        $user->role = User::ROLE_ADMIN;
        $user->save();

        return redirect()->route('team-status')->with('success', __('messages.team_created_success'));
    }

    public function leave()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->back()->withErrors(__('messages.admin_cannot_leave'));
        }

        $user->team_id = null;
        $user->role = 'user';
        $user->save();

        return redirect()->route('team-status')->with('success', __('messages.left_team_successfully'));
    }

    public function dissolve()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return redirect()->back()->withErrors(__('messages.no_permission'));
        }

        $team = $user->team;
        $team->users()->update(['team_id' => null, 'role' => 'user']);
        $team->delete();

        return redirect()->route('team-status')->with('success', __('messages.team_dissolved_successfully'));
    }
}

