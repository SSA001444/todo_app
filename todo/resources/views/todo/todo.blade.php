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
        <h2>Add Todo</h2>

        <form class="row g-3 justify-content-center" method="POST" action="{{ route('todos.store') }}">
            @csrf
            <div class="col-3">
                <input type="text" class="form-control" name="title" placeholder="Title">
            </div>
            <div class="col-3">
                <input type="text" class="form-control" name="commentary" placeholder="Commentary">
            </div>
            <div class="col-2">
                <select name="group_id" id="group_id" class="form-control">
                    <option value="">Select group</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" name="group_id">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Submit</button>
            </div>
        </form>
    </div>

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
                        is_checked: isChecked
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

    <div class="text-center">
        <h2>All Todos</h2>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <table class="table table-bordered" id="sortable-table">
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
                        @if ($todo->user_id == auth()->id())
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
                                <a href="{{ route('todos.edit', ['todo' => $todo->id]) }}" class="btn btn-info">Edit</a>
                                <button class="btn btn-danger delete-button" data-todo-id="{{ $todo->id }}">Delete</button>
                                <a href="{{ route('todo.share', ['todo' => $todo->id]) }}" class="btn btn-info">Share</a>
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
@endsection
