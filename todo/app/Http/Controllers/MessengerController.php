<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function index()
    {
        $contacts = User::where('id', '!=', Auth::id())->get();
        return view('messenger.messenger', compact('contacts'));
    }

    public function showDialog($userId)
    {
        $contacts = User::where('id', '!=', Auth::id())->get();

        $messages = Message::where(function ($query) use ($userId) {
            $query->where('user_id', Auth::id())
                  ->where('recipient_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('recipient_id', Auth::id());
        })->get();

        $selectedUser = User::findOrFail($userId);

        return view('messenger.messenger', compact('contacts', 'messages', 'selectedUser'));
    }

    public function sendMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $selectedUser = User::findOrFail($userId);
        $recipient_id = $selectedUser->id;

        Message::create([
            'user_id' => Auth::id(),
            'recipient_id' => $recipient_id,
            'message' => $request->message,
        ]);

        return redirect()->route('messenger.dialog', ['userId' => $userId]);
    }
}
