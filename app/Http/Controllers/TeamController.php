<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'teamName' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if ($user->team_id) {
            return redirect()->route('team-status')->withErrors(['You are already a member of a team.']);
        }

        $team = Team::create([
            'name' => $request->teamName,
        ]);

        $user->team_id = $team->id;
        $user->role = User::ROLE_ADMIN;
        $user->save();

        return redirect()->route('team-status')->with('success', 'Team created successfully!');
    }
}

