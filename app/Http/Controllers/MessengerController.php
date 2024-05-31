<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatContact;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MessengerController extends Controller
{
    public function index()
    {
        $chatContacts = ChatContact::where('team_id', Auth::user()->team_id)->whereNot('id', Auth::user()->chat_id)->get();

        return view('messenger.messenger', compact( 'chatContacts'));
    }

    public function showDialog($contactId)
    {
        $chatContacts = ChatContact::where('team_id', Auth::user()->team_id)->whereNot('id', Auth::user()->chat_id)->get();

        $selectedChatContact = ChatContact::where('id', $contactId)
                                          ->where('team_id', Auth::user()->team_id)
                                          ->whereNot('id', Auth::user()->chat_id)
                                          ->first();

        $messages = Message::where(function ($query) use ($contactId) {
            $query->where('recipient_id', $contactId);
        })->get();


        if($selectedChatContact) {
            return view('messenger.messenger', compact( 'chatContacts', 'messages', 'selectedChatContact'));
        } else {
            return redirect()->back()->with('error', 'You cant see this chat.');
        }
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->user_id !== Auth::id() && !(Auth::user()->role == 'admin')) {
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
            'message' => Crypt::encryptString($request->message),
            'edited' => true,
        ]);

        return redirect()->back()->with('success', 'Message edited successfully.');
    }

    public function sendMessage(Request $request, $contactId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $selectedChatContact = ChatContact::where('id', $contactId)
                                          ->where('team_id', Auth::user()->team_id)
                                          ->whereNot('id', Auth::user()->chat_id)
                                          ->first();

        Message::create([
            'user_id' => Auth::id(),
            'recipient_id' => $contactId,
            'message' => Crypt::encryptString($request->message),
        ]);

        if($selectedChatContact) {
            return redirect()->route('messenger.dialog', ['contactId' => $contactId]);
        } else {
            return redirect()->back()->with('error', 'You cant send to this chat.');
        }
    }
}
