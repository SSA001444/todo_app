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
        var socket = new WebSocket("ws://192.168.1.100:8000");

        $(document).ready(function () {
            $(document).on('click', '.delete-button', function () {
                var todoId = $(this).data('todo-id');

                if (confirm('Are you sure you want to delete this todo?')) {
                    window.location.href = '/todos/delete/' + todoId;
                }
            });
        });
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
            $('#sortable-table').on('change', '.subtask-status-checkbox', function () {
                var subtaskId = $(this).data('subtask-id');
                var isChecked = $(this).prop('checked');

                $.ajax({
                    type: "POST",
                    url: "{{ route('subtask.update-status') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        subtask_id: subtaskId,
                        is_checked: isChecked,
                    },
                    success: function (data) {

                    },
                    error: function (error) {
                        console.log("Error updating status: " + error);
                    }
                });
            });
        });
        $(document).ready(function() {
            socket.onopen = function () {
                console.log("Connection success");
            };
            socket.onclose = function(event) {
                if (event.wasClean) {
                    console.log("Connection close");
                } else {
                    console.log("Connection kill");
                }
                alert("Code: " + event.code + " reason: " +event.reason);
            };

            socket.onmessage = function(event) {
                var json = JSON.parse(event.data);
                var recipientId = json.user_id;
                var currentUserId = {{ auth()->id() }};
                var todos = document.getElementById('table-tbody');

                if (recipientId == currentUserId) {
                    alert("You receive new todo: " + json.title + " from: " + json.name);
                }
                if (json.new) {
                    $.get('load-todos', function (data) {

                        var created_at;
                        var foundTodo = data.find(function (todo) {
                            created_at = todo.created_at;
                            return todo.id === json.id;
                        });

                        if (foundTodo) {
                            var todoRow = document.createElement('tr');
                            todoRow.dataset.todoId = json.id;
                            todoRow.setAttribute('class', 'ui-sortable-handle' );
                            todoRow.setAttribute('id', 'todos-table' );
                            todoRow.dataset.todo = JSON.stringify(foundTodo);
                            todoRow.innerHTML = `
                        <th id="title">${json.title}</th>
                        <th id="group">${json.group_id !== null ? json.group_id : 'None'}</th>
                        <th id="commentary">${json.commentary !== null ? json.commentary : ''}</th>
                        <th>${created_at}</th>
                        <td>
                            ${json.is_completed ? '<div class="badge bg-success">Completed</div>' : '<div class="badge bg-warning">Not Completed</div>'}
                            <input type="checkbox" class="todo-status-checkbox" data-todo-id="${json.id}" ${json.is_completed ? 'checked' : ''}>
                        </td>
                        <td>
                            <a href="/todos/${json.id}/edit" class="btn btn-info">Edit</a>
                            <button class="btn btn-danger delete-button" data-todo-id="${json.id}">Delete</button>
                            <a href="/share-todo/${json.id}" class="btn btn-info">Share</a>
                        </td>
                        <th></th>
                    `;
                            todos.appendChild(todoRow);

                        }
                    });
                }

                if (json.new === false) {
                    var groupName;
                    $.get('load-groups', function (data) {
                        var group = data.find(function (group) {
                                groupName = group.name;
                        });
                    });

                    $.get('load-todos', function (data) {
                        var foundTodo = data.find(function (todo) {
                            return todo.id === json.id;
                        });

                        if (foundTodo.group_id == null) {
                            groupName = "None";
                        }

                        if (foundTodo) {
                            var titleElement = document.querySelector("tr[data-todo-id='" + json.id + "'] th#title");
                            var groupElement = document.querySelector("tr[data-todo-id='" + json.id + "'] th#group");
                            var commentaryElement = document.querySelector("tr[data-todo-id='" + json.id + "'] th#commentary");
                            var completedElement = document.querySelector("tr[data-todo-id='" + json.id + "'] input#check-completed");
                            var statusBadge = document.querySelector("tr[data-todo-id='" + json.id + "'] div.badge");

                            titleElement.textContent = json.title;
                            groupElement.textContent = groupName;
                            commentaryElement.textContent = json.commentary;
                            if (json.is_completed) {
                                completedElement.checked = true;
                                statusBadge.textContent = "Completed"
                                statusBadge.classList.remove('bg-success', 'bg-warning');
                                statusBadge.classList.add('bg-success');

                            } else {
                                completedElement.checked = false;
                                statusBadge.textContent = "Not Completed";
                                statusBadge.classList.remove('bg-success', 'bg-warning');
                                statusBadge.classList.add('bg-warning');
                            }
                        }
                    });
                }

                if (json.delete) {
                    var todoRow = document.querySelector(`tr[data-todo-id='${json.id}']`);
                    if (todoRow) {
                        todoRow.remove();
                    }
                }
                if (json.updateStatus) {
                    var completedElement = document.querySelector("tr[data-todo-id='" + json.id + "'] input#check-completed");
                    var statusBadge = document.querySelector("tr[data-todo-id='" + json.id + "'] div.badge");
                    if (json.is_completed) {
                        completedElement.checked = true;
                        statusBadge.textContent = "Completed"
                        statusBadge.classList.remove('bg-success', 'bg-warning');
                        statusBadge.classList.add('bg-success');

                    } else {
                        completedElement.checked = false;
                        statusBadge.textContent = "Not Completed";
                        statusBadge.classList.remove('bg-success', 'bg-warning');
                        statusBadge.classList.add('bg-warning');
                    }
                }
            };
            socket.onerror = function(error) {
                alert("Error: " + error.message);
            };
        });

        $(document).ready(function() {
            $(".toggle-subtasks-button").click(function () {
                var button = $(this);
                var subtasksList = button.siblings(".subtasks-list");
                var todoId = button.closest("tr").data("todo-id");
                if (subtasksList.is(":visible")) {
                    subtasksList.hide();
                } else {
                    $.get("/load-subtasks/" + todoId, function (data) {

                        var subtask = data.find(function (subtask) {
                            return subtask.todo_id = todoId;
                        });

                        if(subtask) {
                            subtasksList.empty();

                            $.each(data, function (index, subtask) {

                                var subtaskDiv = $('<div>');

                                var titleDiv = $('<div>').text(subtask.title);

                                var checkbox = $('<input>')
                                    .attr('type', 'checkbox')
                                    .attr('id', 'subtask-completed')
                                    .addClass('subtask-status-checkbox')
                                    .attr('data-subtask-id', subtask.id)
                                    .prop('checked', subtask.is_completed);

                                subtaskDiv.append(titleDiv);
                                subtaskDiv.append(checkbox);

                                subtasksList.append(subtaskDiv);
                            });
                            subtasksList.show();
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error loading subtasks: " + errorThrown);
                    });
                }
            });
            $(".submit-subtask-button").click(function() {
                var button = $(this);
                var subtaskForm = button.closest(".subtask-form");
                var todoId = button.closest("tr").data("todo-id");
                var subtaskTitle = subtaskForm.find(".subtask-title").val();

                $.ajax({
                    url: "/add-subtask",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        todo_id: todoId,
                        title: subtaskTitle
                    },
                    success: function(data) {

                    },
                    error: function(error) {
                        console.error("Error: " + error);
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
                    <tbody id="table-tbody">

                    @php $sortedTodos = \App\Models\Todo::orderBy('sort_order')->get(); @endphp
                    @foreach ($sortedTodos as $todo)
                        @if ($todo->user->contains(auth()->user()))
                        <tr data-todo-id="{{ $todo->id }}" id="todos-table" data-todo="{{ $todo }}">
                            <th id="title">{{$todo->title}}</th>
                            <th id="group">{{$todo->group ? $todo->group->name : 'None'}}</th>
                            <th id="commentary">{{$todo->commentary}}</th>
                            <th>{{$todo->created_at}}</th>
                            <td>
                                @if ($todo->is_completed)
                                    <div id="badgeComp" class="badge bg-success">Completed</div>
                                @else
                                    <div id="badgeNotComp" class="badge bg-warning">Not Completed</div>
                                @endif
                                <input type="checkbox" id="check-completed" class="todo-status-checkbox" data-todo-id="{{ $todo->id }}" {{ $todo->is_completed ? 'checked' : '' }}>
                            </td>
                            <td>
                                <a href="{{ route('todos.edit', ['todo' => $todo->id]) }}" class="btn btn-info">Edit</a>
                                <button class="btn btn-danger delete-button" data-todo-id="{{ $todo->id }}">Delete</button>
                                <a href="{{ route('todo.share', ['todo' => $todo->id]) }}" class="btn btn-info">Share</a>
                            </td>
                            <th>{{$todo->shared_from}}</th>
                            <td>
                                <button class="toggle-subtasks-button">Show subtasks</button>
                                <div class="subtasks-list">

                                </div>
                            </td>
                            <td>
                                <form class="subtask-form">
                                    <button class="submit-subtask-button">Add subtask</button>
                                    <input type="text" class="subtask-title" placeholder="Title">
                                </form>
                            </td>
                        @endif

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
