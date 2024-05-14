@extends('auth.layouts')

@section('content')
    <div class="messenger-container">
        <div class="sidebar">
            <div class="sidebar-header">
                Contacts
            </div>
            <div class="contacts">
                @foreach($contacts as $contact)
                    <div class="contact-item">
                        <div class="contact-avatar">
                            <img src="{{ $contact->profile_photo ? asset($contact->profile_photo) : asset('images/auth/login/2.png') }}" alt="{{ $contact->username }}" class="contact-avatar-img">
                        </div>
                        <div class="contact-name">
                            <a href="{{ route('messenger.dialog', ['userId' => $contact->id]) }}">{{ $contact->username }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                @isset($selectedUser)
                    Chat with {{ $selectedUser->username }}
                @else
                    Select a contact to start chatting
                @endisset
            </div>
            <div class="messages">
                @isset($messages)
                    @foreach($messages as $message)
                        <div class="message {{ $message->user_id == Auth::id() ? 'sent' : 'received' }}">
                            <strong>{{ $message->user->username }}:</strong> {{ $message->message }}
                        </div>
                    @endforeach
                @endisset
            </div>
            @isset($selectedUser)
                <div class="message-input-container">
                    <form action="{{ route('messenger.send', ['userId' => $selectedUser->id]) }}" method="POST" class="d-flex w-100">
                        @csrf
                        <input type="text" name="message" class="message-input" placeholder="Type a message">
                        <button type="submit" class="send-button">Send</button>
                    </form>
                </div>
            @endisset
        </div>
    </div>
@endsection
