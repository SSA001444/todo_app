@extends('auth.layouts')

@section('content')
    <div class="input-center">
        <div class="input-item">
            <div class="input-item-logo">
                <img class="input-logo" src="{{ asset('images/logo_bg.png') }}" alt="">
            </div>
            <form action="{{ route('authenticate') }}" method="post">
                @csrf
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('message'))
                        <div class="alert alert-danger">
                            {{ session('message') }}
                        </div>
                    @endif

                <div class="input-img-cont">
                    <img class="input-img" src="{{ asset('images/auth/lock.png') }}" alt="">
                </div>
                <div class="input-input">
                    <input type="text" class="input @error('identity') is-invalid @enderror" name="identity" placeholder="{{ __('messages.enter_email_username') }}" required>
                    @error ('identity')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                    @enderror
                </div>

                <div class="input-input">
                    <input type="password" class="input" name="password" placeholder="{{ __('messages.enter_password') }}" required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="input-item-but">
                    <button type="submit" class="add-input">{{ __('messages.login') }}</button>
                </div>

                <div class="input-item-text">
                    <p class="input-title">{{ __('messages.login') }}</p>
                </div>

                <div class="input-controls">
                    <a href="{{ route('forget.password.get') }}" class="btn btn-primary">{{ __('messages.reset_password') }}</a>
                </div>
            </form>
        </div>
    </div>

@endsection
