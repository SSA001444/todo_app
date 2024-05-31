@extends('auth.layouts')

@section('content')
    <div class="messenger-container">
        <div class="sidebar">
            <div class="sidebar-header">
                {{ __('messages.contacts') }}
            </div>
            <div class="contacts">
                @foreach($chatContacts as $chatContact)
                    <div class="contact-item">
                        <div class="contact-avatar">
                            <img src="https://via.placeholder.com/40" alt="{{ Crypt::decryptString($chatContact->name) }}" class="contact-avatar-img">
                        </div>
                        <div class="contact-name">
                            <a href="{{ route('messenger.dialog', ['contactId' => $chatContact->id]) }}">{{ Crypt::decryptString($chatContact->name) }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                @isset($selectedUser)
                    {{ __('messages.chat_with', ['username' => Crypt::decryptString($selectedUser->username)]) }}
                @elseif(isset($selectedChatContact))
                    {{ __('messages.chat_with', ['username' => Crypt::decryptString($selectedChatContact->name)]) }}
                @else
                    {{ __('messages.select_contact') }}
                @endisset
            </div>
            <div class="messages">
                @isset($messages)
                    @foreach($messages as $message)
                        <div class="message {{ $message->user_id == Auth::id() ? 'sent' : 'received' }}">
                            <div class="message-text">
                                <strong>{{ Crypt::decryptString($message->user->username) }}:</strong> {{ Crypt::decryptString($message->message) }}
                            </div>
                            @if($message->edited)
                                <div class="edited-label">
                                    {{ __('messages.edited') }} {{ $message->updated_at->addHours(3)->format('H:i') }}
                                </div>
                            @endif
                            <div class="sent-label">
                                {{ __('messages.sent') }} {{ $message->created_at->addHours(3)->format('H:i') }}
                            </div>
                            @if($message->user_id == Auth::id() || (Auth::user()->role == 'admin'))
                                <div class="message-actions">
                                    <form action="{{ route('messenger.delete', ['messageId' => $message->id]) }}" method="POST" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    @if($message->user_id == Auth::id())
                                        <button type="button" class="btn-icon" data-message-id="{{ $message->id }}" data-message-text="{{ Crypt::decryptString($message->message) }}"><i class="fas fa-edit"></i></button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p>{{ __('messages.no_messages') }}</p>
                @endisset
            </div>
            @isset($selectedUser)
                <div class="message-input-container">
                    <form action="{{ route('messenger.send', ['contactId' => $selectedUser->id]) }}" method="POST" class="d-flex w-100">
                        @csrf
                        <input type="text" name="message" class="message-input" placeholder="{{ __('messages.type_message') }}">
                        <button type="submit" class="send-button">{{ __('messages.send') }}</button>
                    </form>
                </div>
            @elseif(isset($selectedChatContact))
                <div class="message-input-container">
                    <form action="{{ route('messenger.send', ['contactId' => $selectedChatContact->id]) }}" method="POST" class="d-flex w-100">
                        @csrf
                        <input type="text" name="message" class="message-input" placeholder="{{ __('messages.type_message') }}">
                        <button type="submit" class="send-button">{{ __('messages.send') }}</button>
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
                    <label for="editMessageText">{{ __('messages.edit_message') }}</label>
                    <input type="text" id="editMessageText" name="message" class="message-input">
                </div>
                <button type="submit" class="send-button">{{ __('messages.save_changes') }}</button>
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
