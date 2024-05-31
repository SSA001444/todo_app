@extends('auth.layouts')

@section('content')
    <main class="login-form">
        <div class="container">
            <div class="reset-password-container">
                <div class="reset-password-card">
                    <div class="reset-password-header">{{ __('messages.reset_password') }}</div>
                    <div class="reset-password-body">
                        @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        <form action="{{ route('forget.password.post') }}" method="POST" class="reset-password-form">
                            @csrf
                            <div class="form-group">
                                <label for="email_address" class="reset-password-label">{{ __('messages.email_address') }}</label>
                                <input type="text" id="email_address" class="reset-password-input" name="email" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="reset-password-error">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <button type="submit" class="reset-password-button">
                                    {{ __('messages.send_password_reset_link') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
