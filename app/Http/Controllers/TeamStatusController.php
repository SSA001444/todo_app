<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class TeamStatusController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->team_id) {
            $teamName = $user->team->name;
            return view('main.team-status', ['teamName' => $teamName]);
        } else {
            return view('main.team-status', ['teamName' => null]);
        }
    }

    public function showTeamStatus()
    {
        $user = Auth::user();

        if (!$user->team) {
            return view('main.team-status', ['teamName' => null]);
        }

        $team = $user->team;
        $teamName = $team->name;

        $memberCount = $team->users()->count();
        $openTicketsCount = $team->tickets()->where('status', 'open')->count();
        $existingTicketsCount = $team->tickets()->whereIn('status', ['open', 'closed'])->count();
        $admin = $team->users->whereIn('role', 'admin')->first();
        $mostActiveTicket = Ticket::withCount('comments')
            ->where('team_id', $team->id)
            ->orderBy('comments_count', 'desc')
            ->first();

        return view('main.team-status', [
            'teamName' => $teamName,
            'memberCount' => $memberCount,
            'openTicketsCount' => $openTicketsCount,
            'existingTicketsCount' => $existingTicketsCount,
            'admin' => $admin,
            'mostActiveTicket' => $mostActiveTicket,
        ]);
    }
}
