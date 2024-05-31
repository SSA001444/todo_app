@extends('auth.layouts')

@section('content')
    <div class="input-center">
        <div class="input-item">
            <div class="input-item-logo">
                <img class="input-logo" src="{{ asset('images/logo_bg.png') }}" alt="">
            </div>
            <form action="{{ route('store') }}" method="post">
                @csrf
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="input-img-cont">
                    <img class="input-img" src="{{ asset('images/auth/mail.png') }}" alt="">
                </div>
                <div class="input-input">
                    <input type="text" class="input @error('username') is-invalid @enderror" name="username"  placeholder="{{ __('messages.enter_username') }}" value="{{ old('username') }}" required>
                    @if ($errors->has('username'))
                        <span class="invalid-feedback" role="alert">{{ $errors->first('username') }}</span>
                    @endif
                </div>

                <div class="input-input">
                    <input type="email" class="input @error('email') is-invalid @enderror" name="email"  placeholder="{{ __('messages.enter_email') }}" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="input-input">
                    <input type="password" class="input @error('password') is-invalid @enderror" name="password" placeholder="{{ __('messages.enter_password') }}" required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="input-input">
                    <input type="password" class="input" name="password_confirmation" placeholder="{{ __('messages.enter_password_again') }}" required>
                </div>

                <div class="input-item-but">
                    <button type="submit" class="add-input">{{ __('messages.register') }}</button>
                </div>

                <div class="input-item-text">
                    <p class="input-title">{{ __('messages.registration') }}</p>
                </div>
            </form>
        </div>
    </div>
@endsection
