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

    <section class="section-1">
        <div class="section-1-item">
            <div class="section-1-logo">
                <img alt="Logo" class="section-1-img" src="{{ asset('images/todo/header/to_do_3.png') }}">
            </div>
        </div>
        <div class="container-section-1">
            <div class="section-1-block">
                <form method="POST" action="{{ route('todos.store') }}">
                    @csrf
                <div class="lop">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="input-sec1-tit" name="title" placeholder="Title">
                        </div>
                        <div class="col-6">
                            <label class="sec1-label">
                                <select class="sec1-select" name="group_id" id="group_id">
                                    <option value disabled selected>Select group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" name="group_id">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button class="but-sec1" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
                    <div class="sec1-com">
                        <div class="aboba">
                            <input class="input-sec1-com" type="text" name="commentary" placeholder="Commentary">
                        </div>
                    </div>
                </form>



    <script>
        $(document).ready(function () {

            $('.delete-button').click(function () {
                var todoId = $(this).data('todo-id');

                if (confirm('Are you sure you want to delete this todo?')) {
                    window.location.href = '/todos/delete/' + todoId;
                }
            });
        });
    </script>
    {{--Script to drop-down todos--}}
    <script>
        $(document).ready(function () {
            $("#sortable-table tbody").sortable({
                axis: "y",
                update: function (event, ui) {
                    var todoIds = $("#sortable-table tbody tr").map(function () {
                        return $(this).data("todo-id");
                    }).get();

                    console.log(todoIds);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('todos.reorder') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            todoIds: todoIds
                        },
                        success: function (data) {
                            console.log("Order updated successfully.");
                        },
                        error: function (error) {
                            console.log("Error updating order: " + error);
                        }
                    });
                }
            });
            $("#sortable-table tbody").disableSelection();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.todo-status-checkbox').change(function () {
                var todoId = $(this).data('todo-id');
                var isChecked = $(this).prop('checked');
                var statusBadge = $(this).siblings('.badge');

                $.ajax({
                    type: "POST",
                    url: "{{ route('todos.update-status') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        todo_id: todoId,
                        is_checked: isChecked,
                    },
                    success: function (data) {
                        console.log("Status updated successfully");

                        statusBadge.text(isChecked ? 'Completed' : 'Not Completed');

                        statusBadge.removeClass('bg-success bg-warning').addClass(isChecked ? 'bg-success' : 'bg-warning');
                    },
                    error: function (error) {
                        console.log("Error updating status: " + error);
                    }
                });
            });
        });
    </script>

        <h2 class="group-title2">All Todos</h2>
            <div class="table-wrapper">
                <table class="fl-table" id="sortable-table">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Commentary</th>
                        <th scope="col">Created at</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                        <th scope="col">Shared from</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php $sortedTodos = \App\Models\Todo::orderBy('sort_order')->get(); @endphp
                    @foreach ($sortedTodos as $todo)
                        @if ($todo->user->contains(auth()->user()))
                        <tr data-todo-id="{{ $todo->id }}">
                            <th>{{$todo->title}}</th>
                            <th>{{$todo->group ? $todo->group->name : 'None'}}</th>
                            <th>{{$todo->commentary}}</th>
                            <th>{{$todo->created_at}}</th>
                            <td>
                                @if ($todo->is_completed)
                                    <div class="badge bg-success">Completed</div>
                                @else
                                    <div class="badge bg-warning">Not Completed</div>
                                @endif
                                <input type="checkbox" class="todo-status-checkbox" data-todo-id="{{ $todo->id }}" {{ $todo->is_completed ? 'checked' : '' }}>
                            </td>
                            <td>
                                <a href="{{ route('todos.edit', ['todo' => $todo->id]) }}" class="group-act-edit group-luke">Edit</a>
                                <button class="group-act-del delete-button" data-todo-id="{{ $todo->id }}">Delete</button>
                                <a href="{{ route('todo.share', ['todo' => $todo->id]) }}" class="group-act-edit group-luke">Share</a>
                            </td>
                            <th>{{$todo->shared_from}}</th>
                        </tr>
                        @endif

                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
    </div>
    <img src="{{ asset('images/sec1/many/1.png') }}" alt="" class="sec1-circle1 sec1-absolute">
    <img src="{{ asset('images/sec1/many/2.png') }}" alt="" class="sec1-circle2 sec1-absolute">
    <img src="{{ asset('images/sec1/inside/1.png') }}" alt="" class="sec1-circle3 sec1-absolute">
    <img src="{{ asset('images/sec1/inside/2.png') }}" alt="" class="sec1-circle4 sec1-absolute">
    <img src="{{ asset('images/sec1/inside/3.png') }}" alt="" class="sec1-circle5 sec1-absolute">
    <img src="{{ asset('images/sec1/outside/1.png') }}" alt="" class="sec1-circle6 sec1-absolute">
    <img src="{{ asset('images/sec1/outside/2.png') }}" alt="" class="sec1-circle7 sec1-absolute">
    <img src="{{ asset('images/sec1/outside/3.png') }}" alt="" class="sec1-circle8 sec1-absolute">
    <div>

    </div>

    </section>

    <img src="{{ asset('images/todo/1.png') }}" alt="" class="bg-img bg-img1">
    <img src="{{ asset('images/todo/2.png') }}" alt="" class="bg-img bg-img2">
    <img src="{{ asset('images/todo/3.png') }}" alt="" class="bg-img bg-img3">
    <img src="{{ asset('images/todo/4.png') }}" alt="" class="bg-img bg-img4">

    <div class="sec13"></div>
@endsection
