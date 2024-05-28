@extends('auth.layouts')

@section('content')
    <div class="alert-container">
        <div class="col-lg-6">
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
            <h2 class="ticket-title">Tickets</h2>

            <div class="filter-bar">
                <div class="filter-item">
                    <label for="filter-status">Status:</label>
                    <select id="filter-status" name="status" class="ticket-filter-select">
                        <option value="">All</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="filter-tag">Tag:</label>
                    <select id="filter-tag" name="tag" class="ticket-filter-select">
                        <option value="">All</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <button id="createTicketBtn" class="ticket-button">Create New Ticket</button>
                </div>
            </div>

            <div class="ticket-list">
                @foreach($tickets as $ticket)
                    <div class="ticket-item">
                        <div class="ticket-header">
                            <span class="ticket-id">#{{ $ticket->team_ticket_id }}</span>
                            <span class="ticket-user">Author: {{ $ticket->user->username }}</span>
                        </div>
                        <div class="ticket-body">
                            <span class="ticket-title">{{ $ticket->title }}</span>
                            <p class="ticket-description">{{ $ticket->description }}</p>
                            <div class="ticket-meta">
                                <span class="ticket-status {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
                                @foreach($ticket->tags as $tag)
                                    <span class="badge badge-primary">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- The Modal -->
    <div id="createTicketModal" class="modal-ticket">
        <div class="modal-content-ticket">
            <span class="close-ticket">&times;</span>
            <h2 class="ticket-title">Create Ticket</h2>
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf
                <input type="text" name="title" class="ticket-input" placeholder="Title" required>
                <textarea name="description" class="ticket-input" placeholder="Description" required></textarea>
                <label for="ticket-tags">Tags:</label>
                <div id="ticket-tags">
                    @foreach($tags as $tag)
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                            <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="team_id" value="{{ auth()->user()->team_id }}">
                <button type="submit" class="ticket-submit">Create Ticket</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var modal = $('#createTicketModal');
            var btn = $('#createTicketBtn');
            var span = $('.close-ticket');

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
