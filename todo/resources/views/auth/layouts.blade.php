<!DOCTYPE html>
<html>
<head>
    <title>Todo App by SSA001444</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

        body{
            margin: 0;
            font-size: .9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #212529;
            text-align: left;
            background-color: #f5f8fa;
        }
        .navbar-laravel
        {
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
        }
        .navbar-brand , .nav-link, .my-form, .login-form
        {
            font-family: Raleway, sans-serif;
        }
        .my-form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .my-form .row
        {
            margin-left: 0;
            margin-right: 0;
        }
        .log_reg_form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .log_reg_form .row
        {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
</head>
<body>

<script>
    $(document).ready(function() {
        $('#profile-photo-input').change(function() {
            var formData = new FormData();
            formData.append('profile_photo', $(this)[0].files[0]);
            formData.append('_token', "{{ csrf_token() }}");

            $.ajax({
                type: 'POST',
                url: '/profile/update-photo',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Profile photo updated successfully');
                },
                error: function(error) {
                    console.error('Error updating profile photo: ' + error);
                }
            });
        });
    });
</script>

<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Todo app by SSA001444</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-link">
                        <div>
                            <img src="{{asset(auth()->user()->profile_photo) }}" alt="Profile Photo">
                        </div>
                        <div class="mt-2">
                            <input type="file" id="profile-photo-input" style="display: none;">
                            <label for="profile-photo-input" class="cursor-pointer text-primary">Change Photo</label>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link">{{auth()->user()->email}}</div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('todos.index')}}">Todos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('groups.index')}}">Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                    </li>

                @endguest
            </ul>

        </div>
    </div>
</nav>

@yield('content')

</body>
</html>
