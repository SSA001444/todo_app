<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
