@extends('auth.layouts')

@section('content')
<div class="input-center">
    <div class="input-item">
        <div class="input-item-logo">
            <img class="input-logo" src="{{ asset('images/auth/login/login.png') }}" alt="">
        </div>
        <form action="{{ route('authenticate') }}" method="post">
            @csrf
            <div id="error-container" class="alert alert-danger" style="display: none;">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="input-lox">
                <img class="input-img" src="{{ asset('images/auth/login/1.png') }}" alt="">
            </div>
            <div class="input-input">
                <input type="text" class="input @error('identity') is-invalid @enderror" name="identity" placeholder="Enter your email or username...">
                @error ('identity')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-input">
                <input type="password" class="input" name="password" placeholder="Enter your password">
                @if ($errors->has('password'))
                    <span class="invalid-feedback">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="input-item-but">
                <button type="submit" class="add-input">Login</button>
            </div>

            <div class="input-item-text">
                <p class="input-title">Login</p>
            </div>

            <div class="input-controls">
                <a href="{{ route('forget.password.get') }}" class="reset-password-link">Reset Password</a>
            </div>
        </form>
    </div>
</div>

@endsection
