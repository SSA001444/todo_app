@extends('auth.layouts')

@section('content')
    <div class="container">
        <div class="dashboard-container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    {{ $message }}
                </div>
                <script>
                    setTimeout(function () {
                        window.location.href = "{{ route('team-status') }}";
                    }, 2000);
                </script>
            @else
                <div class="alert alert-success">
                    {{ __('messages.dashboard_logged_in') }}
                </div>
                <script>
                    setTimeout(function () {
                        window.location.href = "{{ route('team-status') }}";
                    }, 2000);
                </script>
            @endif
        </div>
    </div>
@endsection
