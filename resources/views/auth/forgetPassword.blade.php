@extends('auth.layouts')

@section('content')
    <main class="login-form">
        <div class="container">
            <div class="reset-password-container">
                <div class="reset-password-card">
                    <div class="reset-password-header">Reset Password</div>
                    <div class="reset-password-body">
                        @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        <form action="{{ route('forget.password.post') }}" method="POST" class="reset-password-form">
                            @csrf
                            <div class="form-group">
                                <label for="email_address" class="reset-password-label">E-Mail Address</label>
                                <input type="text" id="email_address" class="reset-password-input" name="email" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="reset-password-error">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <button type="submit" class="reset-password-button">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
