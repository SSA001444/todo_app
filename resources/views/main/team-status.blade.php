@extends('auth.layouts')

@section('content')
    <div class="container">
        @if ($teamName)
            <h1 class="team-status-header">You are a member of the team: {{ $teamName }}</h1>
        @else
            <div class="no-team-container">
                <h1 class="no-team-header">You need to create your own team or wait to be invited</h1>
                <button id="createTeamBtn" class="create-team-btn">Create Team</button>
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div id="createTeamModal" class="modal-team">
        <div class="modal-content-team">
            <span class="close-team">&times;</span>
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
            var span = $('.close-team');

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
