@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            @if(session()->has('success'))
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

    <?php
    $groups = \App\Models\Group::where('user_id', auth()->id())->get();
    ?>

    <div class="text-center mt-5">
        <h2>Edit Todo</h2>
    </div>

    <form method="POST" action="{{ route('todos.update', ['todo' => $todo->id]) }}">
        @csrf
        {{ method_field('PUT') }}

        <div class="row justify-content-center mt-5">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" placeholder="Title" value="{{ $todo->title }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Commentary</label>
                    <input type="text" class="form-control" name="commentary" placeholder="Commentary" value="{{ $todo->commentary }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_completed" id="" class="form-control">
                        <option value="1" @if($todo->is_completed == 1) selected @endif>Complete</option>
                        <option value="0" @if($todo->is_completed == 0) selected @endif>Not Complete</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Group</label>
                    <select name="group_id" id="" class="form-control">
                        <option value="">None</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" @if($group->id == $todo->group_id) selected @endif>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
@endsection
