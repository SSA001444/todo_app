@extends('auth.layouts')

@section('content')
    <main class="login-form">
        <div class="container reset-password-container">
            <div class="reset-password-card">
                <div class="reset-password-header">{{ __('messages.reset_password') }}</div>
                <div class="reset-password-body">
                    @if (Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    <form action="{{ route('reset.password.post') }}" method="POST" class="reset-password-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            <label for="email_address" class="reset-password-label">{{ __('messages.email_address') }}</label>
                            <input type="text" id="email_address" class="reset-password-input" name="email" required autofocus>
                            @if ($errors->has('email'))
                                <span class="reset-password-error">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password" class="reset-password-label">{{ __('messages.password') }}</label>
                            <input type="password" id="password" class="reset-password-input" name="password" required>
                            @if ($errors->has('password'))
                                <span class="reset-password-error">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password-confirm" class="reset-password-label">{{ __('messages.confirm_password') }}</label>
                            <input type="password" id="password-confirm" class="reset-password-input" name="password_confirmation" required>
                            @if ($errors->has('password_confirmation'))
                                <span class="reset-password-error">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="reset-password-button">{{ __('messages.reset_password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
