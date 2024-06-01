<!DOCTYPE html>
<html lang="en">
<head>
    <title>Papers Planes</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/bird.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

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

        .header-center-img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .header-center-img img {
            max-width: 100px;
            max-height: 100px;
        }

    </style>
</head>
<body>

<script>
    $(document).ready(function() {
        $('.header-img-php').click(function() {
            $('#profile-photo-input').click();
        });
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
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error updating profile photo:', error.responseJSON || error.responseText);
                }
            });
        });
    });
</script>

<header>
    <div class="container">
        @guest
            <div class="header-butt-2">
                    <form class="header-form" action="{{ route('locale.switch') }}" method="POST">
                        @csrf
                        <label>
                            <select class="header-select" name="locale" onchange="this.form.submit()">
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                                <option value="lv" {{ app()->getLocale() == 'lv' ? 'selected' : '' }}>LV</option>
                                <option value="ru" {{ app()->getLocale() == 'ru' ? 'selected' : '' }}>RU</option>
                            </select>
                        </label>
                </form>
                <a class="header-logreg" href="{{ route('login') }}">{{ __('messages.login') }}</a>
                <a class="header-logreg" href="{{ route('register') }}">{{ __('messages.register') }}</a>
            </div>
        @else
            <div class="header-item">
                <div class="header-logo">
                    <div class="header-php-div">
                        <label for="profile-photo-input" class="header-php">
                            @php
                                $userPhoto = auth()->user()->profile_photo;
                                $defaultPhoto = asset('images/header/profile_photo_default.png');
                                $photoPath = $userPhoto && file_exists(public_path($userPhoto)) ? asset($userPhoto) : asset($defaultPhoto);
                            @endphp
                            <img src="{{$photoPath}}" alt="Profile Photo" class="header-img-php">
                            <div class="overlay"></div>
                            <input type="file" name="profile_photo" id="profile-photo-input" style="display: none;">
                            <img src="{{ asset('images/header/edit_profile_photo.png') }}" alt="Profile Photo" class="header-hover-img">
                        </label>
                    </div>
                    <div class="user-info">
                        <span class="username">{{ Crypt::decryptString(auth()->user()->username) }}</span>
                        <span class="role">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <a class="header-button" href="{{ route('profile.index') }}">{{ __('messages.profile') }}</a>
                    <a class="header-button" href="{{ route('logout') }}">{{ __('messages.logout') }}</a>
                </div>
                <a href="{{ route('team-status') }}" class="header-center-img">
                    <img src="{{ asset('images/header/bird_wbg.png') }}" alt="Main Page">
                </a>
                <div class="header-butt">
                    <form class="header-form" action="{{ route('locale.switch') }}" method="POST">
                        @csrf
                        <label>
                            <select class="header-select" name="locale" onchange="this.form.submit()">
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                                <option value="lv" {{ app()->getLocale() == 'lv' ? 'selected' : '' }}>LV</option>
                                <option value="ru" {{ app()->getLocale() == 'ru' ? 'selected' : '' }}>RU</option>
                            </select>
                        </label>
                    </form>
                    @if (auth()->user()->role == 'admin' || auth()->user()->role == 'moderator')
                        <a class="header-button" href="{{route('admin.users')}}">{{ __('messages.admin_panel') }}</a>
                    @endif
                    <a class="header-button" href="{{route('messenger.index')}}">{{ __('messages.messenger') }}</a>
                    <a class="header-button" href="{{route('tickets.index')}}">{{ __('messages.tickets') }}</a>
                    <a class="header-button" href="{{route('tags.index')}}">{{ __('messages.tags') }}</a>
                </div>
            </div>
        @endguest
    </div>
</header>
<div class="header-line">
    <hr class="header-hr">
</div>

@yield('content')

</body>
</html>
