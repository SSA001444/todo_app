@extends('auth.layouts')

@section('content')
<div class="input-center">
    <div class="input-item">
        <div class="input-item-logo">
            <img class="input-logo" src="{{ asset('images/auth/login/login.png') }}" alt="">
        </div>
        <form action="{{ route('store') }}" method="post">
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
                <input type="text" class="input @error('username') is-invalid @enderror" name="username"  placeholder="Enter your username" value="{{ old('username') }}">
                @if ($errors->has('username'))
                    <span class="invalid-feedback" role="alert">{{ $errors->first('username') }}</span>
                @endif
            </div>

            <div class="input-input">
                <input type="email" class="input @error('email') is-invalid @enderror" name="email"  placeholder="Enter your email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div class="input-input">
                <input type="password" class="input @error('password') is-invalid @enderror" name="password" placeholder="Enter your password">
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="input-input">
                <input type="password" class="input" name="password_confirmation" placeholder="Enter your password again">
            </div>

            <div class="input-item-but">
                <button type="submit" class="add-input">Register</button>
            </div>

            <div class="input-item-text">
                <p class="input-title">Registration</p>
            </div>
        </form>
    </div>
</div>

@endsection
