@extends('auth.layouts')

@section('content')
    <div class="alert-container">
        <div>
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <section class="tickets">
        <div class="container-ticket">
            <h2 class="ticket-title">{{ __('messages.tickets') }}</h2>
            <div class="filter-bar">
                <div class="filter-item">
                    <label for="filter-status">{{ __('messages.status') }}:</label>
                    <select id="filter-status" name="status" class="ticket-filter-select">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="open">{{ __('messages.open') }}</option>
                        <option value="closed">{{ __('messages.closed') }}</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="filter-tag">{{ __('messages.tag') }}:</label>
                    <select id="filter-tag" name="tag" class="ticket-filter-select">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <button id="createTicketBtn" class="ticket-button">{{ __('messages.create_new_ticket') }}</button>
                </div>
            </div>

            <div class="ticket-list">
                @foreach($tickets as $ticket)
                    @if(!($ticket->trashed()) && $ticket->team_id == Auth::user()->team_id)
                        <div class="ticket-item">
                            <div class="ticket-header">
                                <span class="ticket-id">#{{ $ticket->team_ticket_id }}</span>
                                <span class="ticket-user">{{ __('messages.author') }}: {{ Crypt::decryptString($ticket->user->username) }}</span>
                                @if( ( $ticket->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin' ) && $ticket->team_id == Auth::user()->team_id )
                                    <button class="delete-ticket-btn" data-id="{{ $ticket->id }}">{{ __('messages.delete') }}</button>
                                @endif
                            </div>
                            <div class="ticket-body">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-title">{{ $ticket->title }}</a>
                                <p class="ticket-description">{{ $ticket->description }}</p>
                                <div class="ticket-meta">
                                    <span class="ticket-status {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
                                    @foreach($ticket->tags as $tag)
                                        <span class="badge badge-primary">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <!-- Create Ticket Modal -->
    <div id="createTicketModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="ticket-title">{{ __('messages.create_ticket') }}</h2>
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf
                <input type="text" name="title" class="ticket-input" placeholder="{{ __('messages.title') }}" required>
                <textarea name="description" class="ticket-input" placeholder="{{ __('messages.description') }}" required></textarea>
                <label for="ticket-tags">{{ __('messages.tags') }}:</label>
                <div id="ticket-tags">
                    @foreach($tags as $tag)
                        @if($tag->team_id == Auth::user()->team_id)
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                                <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                        @endif
                    @endforeach
                </div>
                <input type="hidden" name="team_id" value="{{ auth()->user()->team_id }}">
                <button type="submit" class="ticket-submit">{{ __('messages.submit') }}</button>
            </form>
        </div>
    </div>
    <!-- Delete Ticket Modal -->
    <div id="deleteTicketModal" class="modal">
        <div class="modal-content">
            <span class="close" id="close_delete_ticket">&times;</span>
            <h2>{{ __('messages.delete_ticket') }}</h2>
            <form id="deleteTicketForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <input name="reason" id="delete-ticket-reason" class="ticket-input" placeholder="{{ __('messages.reason_for_deletion') }}" required>
                <button type="submit" class="ticket-submit">{{ __('messages.delete_ticket') }}</button>
            </form>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            var modal = $('#createTicketModal');
            var btn = $('#createTicketBtn');
            var span = $('.close');
            var deleteTicketModal = $('#deleteTicketModal');
            var deleteSpan = $('#close_delete_ticket');

            btn.on('click', function() {
                modal.show();
            });

            span.on('click', function() {
                modal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });

            $('.delete-ticket-btn').on('click', function() {
                const ticketId = $(this).data('id');
                $('#deleteTicketForm').attr('action', `/tickets/${ticketId}`);
                deleteTicketModal.show();
            });

            deleteSpan.on('click', function() {
                deleteTicketModal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(deleteTicketModal)) {
                    deleteTicketModal.hide();
                }
            });

            // Filter tickets by status and tag
            $('#filter-status, #filter-tag').on('change', function() {
                var status = $('#filter-status').val();
                var tag = $('#filter-tag').val().toLowerCase();

                $('.ticket-item').each(function() {
                    var item = $(this);
                    var itemStatus = item.find('.ticket-status').text().toLowerCase();
                    var itemTags = item.find('.badge').map(function() { return $(this).text().toLowerCase(); }).get();
                    var showStatus = (status === '' || itemStatus === status);
                    var showTag = (tag === '' || itemTags.includes(tag));

                    if (showStatus && showTag) {
                        item.show();
                    } else {
                        item.hide();
                    }
                });
            });
        });
    </script>
@endsection
