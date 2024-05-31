@extends('auth.layouts')

@section('content')
    <div class="container groups">
        <div class="groups-center">
            <div class="groups-item-fullwidth">
                <h1 class="group-title">Admin Panel</h1>

                <div class="alert-container">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            @endif
                        </div>

                        <div class="table-wrapper">
                            <table class="fl-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ Crypt::decryptString($user->username) }}</td>
                                        <td>{{ Crypt::decryptString($user->email) }}</td>
                                        <td>
                                            @if($user->id !== Auth::user()->id)
                                                @if(Auth::user()->role === 'admin')
                                                    <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                                        @csrf
                                                        <div class="select-wrapper">
                                                            <select name="role" class="group-select" onchange="this.form.submit()">
                                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                                <option value="moderator" {{ $user->role == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                            </select>
                                                        </div>
                                                    </form>
                                                @else
                                                    {{ ucfirst($user->role) }}
                                                @endif
                                            @else
                                                {{ ucfirst($user->role) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->id !== Auth::user()->id)
                                                @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'moderator' && $user->role !== 'moderator' && $user->role !== 'admin'))
                                                    <form action="{{ route('admin.users.remove', $user) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="but-group">Remove</button>
                                                    </form>
                                                @else
                                                    -
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                            <h2 class="group-title2">Invite New User</h2>
                            <form action="{{ route('admin.users.invite') }}" method="POST" class="group-form">
                                @csrf
                                <input type="text" name="identifier" class="group-select" placeholder="User Email or Username" required>
                                <button type="submit" class="but-group">Send Invitation</button>
                            </form>
                </div>
            </div>
        </div>
@endsection
