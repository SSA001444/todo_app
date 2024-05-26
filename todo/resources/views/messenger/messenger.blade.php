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
                            <img src="{{ $contact->profile_photo ? asset($contact->profile_photo) : 'https://via.placeholder.com/40' }}" alt="{{ $contact->username }}" class="contact-avatar-img">
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
                            @if($message->edited)
                                <div class="edited-label">
                                    edited {{ $message->updated_at->addHours(3)->format('H:i') }}
                                </div>
                            @endif
                            <div class="sent-label">
                                sent {{ $message->created_at->addHours(3)->format('H:i') }}
                            </div>
                            @if($message->user_id == Auth::id())
                                <div class="message-actions">
                                    <form action="{{ route('messenger.delete', ['messageId' => $message->id]) }}" method="POST" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    <button type="button" class="btn-icon" data-message-id="{{ $message->id }}" data-message-text="{{ $message->message }}"><i class="fas fa-edit"></i></button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p>No messages yet.</p>
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

    <div id="editMessageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editMessageForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="editMessageText">Message</label>
                    <input type="text" id="editMessageText" name="message" class="message-input">
                </div>
                <button type="submit" class="send-button">Save changes</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var modal = $('#editMessageModal');
            var span = $('.close');
            span.on('click', function() {
                modal.hide();
            });

            $('.btn-icon[data-message-id]').on('click', function() {
                var messageId = $(this).data('message-id');
                var messageText = $(this).data('message-text');
                $('#editMessageText').val(messageText);
                $('#editMessageForm').attr('action', '/messenger/' + messageId);
                modal.show();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });
        });
    </script>
@endsection
