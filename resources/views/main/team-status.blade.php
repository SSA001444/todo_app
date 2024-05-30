@extends('auth.layouts')

@section('content')
    <div class="alert-container">
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

    @if ($teamName)
        <section class="team-status">
            <div class="section-1-item">
                <div class="section-1-logo">
                    <img alt="Logo" class="section-1-img" src="{{ asset('images/logo_wbg.png') }}">
                </div>
            </div>

            <div class="team-info">
                <h1 class="team-status-header">Team: {{ $teamName }}</h1>
            </div>

            <div class="team-statistics">
                <div class="stat-row">
                    <div class="stat-item">
                        <h2>Team Members</h2>
                        <p>{{ $memberCount }}</p>
                    </div>
                    <div class="stat-item">
                        <h2>Open Tickets</h2>
                        <p>{{ $openTicketsCount }}</p>
                    </div>
                    <div class="stat-item">
                        <h2>Total Existing Tickets</h2>
                        <p>{{ $existingTicketsCount }}</p>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-item">
                        <h2>Team Admin</h2>
                        <p>{{ $admin->username }}</p>
                    </div>
                    <div class="stat-item">
                        <h2>Most Active Ticket</h2>
                        @if ($mostActiveTicket)
                            <p><a href="{{ route('tickets.show', $mostActiveTicket->id) }}">{{ $mostActiveTicket->title }}</a> ({{ $mostActiveTicket->comments_count }} comments)</p>
                        @else
                            <p>No tickets with comments</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="no-team-container">
            <h1 class="no-team-header">You need to create your own team or wait to be invited</h1>
            <button id="createTeamBtn" class="create-team-btn">Create Team</button>
        </div>
    @endif

    <!-- Modal for creating team -->
    <div id="createTeamModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create a New Team</h2>
            <form id="createTeamForm" action="{{ route('teams.store') }}" method="POST">
                @csrf
                <label for="teamName">Team Name</label>
                <input type="text" id="teamName" name="teamName" class="input-team-name" required>
                <button type="submit" class="submit-team-btn">Create</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var modal = $('#createTeamModal');
            var btn = $('#createTeamBtn');
            var span = $('.close');

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
        });
    </script>
@endsection
