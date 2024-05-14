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

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own messages.');
        }

        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }

    public function editMessage(Request $request, $messageId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own messages.');
        }

        $message->update([
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message edited successfully.');
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
