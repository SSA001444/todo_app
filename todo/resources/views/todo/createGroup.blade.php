@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="text-center mt-5">
        <h2>Add Group</h2>

        <form class="row g-3 justify-content-center" method="POST" action="{{ route('groups.store') }}">
            @csrf
            <div class="col-6">
                <input type="text" class="form-control" name="name" placeholder="Title group">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Submit</button>
            </div>
        </form>
    </div>
    <div class="text-center">
        <h2>All Groups</h2>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Created at</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php $counter=1 @endphp
                    @foreach ($groups as $group)
                        <tr>
                            <th>{{ $counter }}</th>
                            <th>{{ $group->name }}</th>
                            <th>{{ $group->created_at }}</th>
                            <td>
                                <a href="{{ route('groups.edit', ['group' => $group->id]) }}" class="btn btn-info">Edit</a>
                                <a href="{{ route('groups.destroy', ['group' => $group->id] )}}" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
