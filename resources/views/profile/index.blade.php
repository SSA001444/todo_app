@extends('auth.layouts')

@section('content')
    <div class="profile-container">
        <h2 class="profile-title">Profile</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                @error('username')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group profile-checkbox-container">
                <label for="changePasswordCheckbox" class="profile-checkbox-label">Change Password</label>
                <input type="checkbox" id="changePasswordCheckbox" class="profile-checkbox" name="change_password" {{ old('change_password') ? 'checked' : '' }}>
                <input type="hidden" id="changePasswordHidden" name="change_password_hidden" value="{{ old('change_password') }}">
            </div>

            <div id="passwordFields" style="display: {{ old('change_password') ? 'block' : 'none' }};">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                    @error('current_password')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                    @error('new_password')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control">
                    @error('new_password_confirmation')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <script>
        document.getElementById('changePasswordCheckbox').addEventListener('change', function() {
            var passwordFields = document.getElementById('passwordFields');
            var hiddenField = document.getElementById('changePasswordHidden');
            if (this.checked) {
                passwordFields.style.display = 'block';
                hiddenField.value = '1';
            } else {
                passwordFields.style.display = 'none';
                hiddenField.value = '';
            }
        });

        // Ensure the password fields are shown if the checkbox is checked on page load
        window.onload = function() {
            var changePasswordCheckbox = document.getElementById('changePasswordCheckbox');
            var passwordFields = document.getElementById('passwordFields');
            var hiddenField = document.getElementById('changePasswordHidden');
            if (changePasswordCheckbox.checked) {
                passwordFields.style.display = 'block';
                hiddenField.value = '1';
            }
        }
    </script>
@endsection
