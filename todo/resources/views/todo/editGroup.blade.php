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
                    {{$error}}
                </div>
            @endforeach
        @endif
    </div>
</div>
<?php
?>
<div class="text-center mt-5">
    <h2>Edit Group</h2>
</div>

    <form method="POST" action="{{route('groups.update',['group'=>$group->id])}}">
        @csrf

        {{method_field('PUT')}}

        <div class="row justify-content-center mt-5">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="name" placeholder="Title" value="{{$group->name}}">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>

        </div>

    </form>
@endsection
