@extends('auth.layouts')

@section('content')
    @if($ticket->team_id == Auth::user()->team_id)
    <div class="ticket-container">
        <div class="ticket-details">
            <h2 class="ticket-title">{{ $ticket->title }}</h2>
            <p class="ticket-description">{{ $ticket->description }}</p>
            <div class="ticket-meta">
                <span class="ticket-status {{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
                @foreach($ticket->tags as $tag)
                    <span class="badge badge-primary">{{ $tag->name }}</span>
                @endforeach
            </div>
            @if(($ticket->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') && $ticket->team_id == Auth::user()->team_id)
                <button id="editTicketBtn" class="btn btn-primary">Edit Ticket</button>
            @endif
        </div>

        <div class="tasks-container">
            <h3 class="tasks-h3">Tasks <button id="addTaskBtn" class="btn btn-primary add-task-button">Add Task</button></h3>
            <ul>
                @foreach($tasks as $task)
                    <li>
                        <input type="checkbox" id="task-{{ $task->id }}" {{ $task->completed ? 'checked' : '' }}>
                        {{ $task->name }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="comments-container">
            <h3>Comments</h3>
            @foreach($comments as $comment)
                <div class="comment-block">
                    @if($comment->trashed())
                        <div class="comment-text">This comment was deleted by {{ $comment->deleted_by }} ({{ $comment->deleted_by_role }}) due to: {{ $comment->deletion_reason }}</div>
                    @else
                        <div class="comment-author">{{ $comment->user->username }}</div>
                        @if($comment->photo)
                            <img src="{{ asset($comment->photo) }}" alt="Comment Photo" class="comment-photo">
                        @endif
                        <div class="comment-actions">
                            @if(($comment->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') && $ticket->team_id == Auth::user()->team_id )
                                <button class="edit-comment-btn" data-id="{{ $comment->id }}">Edit</button>
                                <button class="delete-comment-btn" data-id="{{ $comment->id }}">Delete</button>
                            @endif
                        </div>
                        <div class="comment-text">{!! nl2br(e($comment->text)) !!}</div>
                        <div class="comment-date">By {{ $comment->user->username }} at {{ $comment->created_at }}</div>
                    @endif
                </div>
            @endforeach

            <form action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data" class="comment-form">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <textarea name="comment" class="comment-input" placeholder="Add a comment" required></textarea>
                <input type="file" name="photo" accept="image/*" class="comment-photo-input">
                <button type="submit" class="btn btn-primary">Add Comment</button>
            </form>
        </div>
    </div>
    <!-- Edit Ticket Modal -->
    <div id="editTicketModal" class="modal-task">
        <div class="modal-content-task">
            <span class="close-task" id="close_edit_ticket">&times;</span>
            <h2>Edit Ticket</h2>
            <form id="editTicketForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <span></span>
                <input name="title" class="task-input" id="edit-ticket-title" required>
                <textarea name="description" class="task-input" id="edit-ticket-description" required></textarea>
                <button type="submit" class="btn btn-primary">Update Ticket</button>
            </form>
        </div>
    </div>
    <!-- Modal for adding tasks -->
    <div id="addTaskModal" class="modal-task">
        <div class="modal-content-task">
            <span class="close-task" id="add_task">&times;</span>
            <h2>Add Task</h2>
            <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <label for="task">Task:</label>
                <input type="text" name="task" class="task-input" required>
                <button type="submit" class="btn btn-primary">Add Task</button>
            </form>
        </div>
    </div>
    <!-- Edit Comment Modal -->
    <div id="editCommentModal" class="modal-task">
        <div class="modal-content-task">
            <span class="close-task" id="edit_comment">&times;</span>
            <h2>Edit Comment</h2>
            <form id="editCommentForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <input name="comment" class="task-input" id="edit-comment-text" required>
                <button type="submit" class="btn btn-primary">Update Comment</button>
            </form>
        </div>
    </div>
    <!-- Delete Comment Modal -->
    <div id="deleteCommentModal" class="modal-task">
        <div class="modal-content-task">
            <span class="close-task" id="delete_comment">&times;</span>
            <h2>Delete Comment</h2>
            <form id="deleteCommentForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <input name="reason" id="delete-reason" class="task-input" placeholder="Reason for deletion" required>
                <button type="submit" class="btn btn-primary">Delete Comment</button>
            </form>
        </div>
    </div>
    @endif
    <script>
        $(document).ready(function() {
            var taskModal = $('#addTaskModal');
            var taskBtn = $('#addTaskBtn');
            var taskSpan = $('#add_task');
            var editModal = $('#editCommentModal');
            var deleteModal = $('#deleteCommentModal');
            var editSpan = $('#edit_comment');
            var deleteSpan = $('#delete_comment');
            var editTicketModal = $('#editTicketModal');
            var editTicketBtn = $('#editTicketBtn');
            var closeEditTicketSpan = $('#close_edit_ticket');

            taskBtn.on('click', function() {
                taskModal.show();
            });

            taskSpan.on('click', function() {
                taskModal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(taskModal)) {
                    taskModal.hide();
                }
            });

            editTicketBtn.on('click', function() {
                const ticketId = {{ $ticket->id }};
                const ticketTitle = '{{ $ticket->title }}';
                const ticketDescription = '{{ $ticket->description }}';

                $('#edit-ticket-title').val(ticketTitle);
                $('#edit-ticket-description').val(ticketDescription);
                $('#editTicketForm').attr('action', `/tickets/${ticketId}`);
                editTicketModal.show();
            });

            closeEditTicketSpan.on('click', function() {
                editTicketModal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(editTicketModal)) {
                    editTicketModal.hide();
                }
            });

            $('.edit-comment-btn').on('click', function() {
                const commentId = $(this).data('id');
                const commentText = $(this).closest('.comment').find('p').text();
                $('#edit-comment-text').val(commentText);
                $('#editCommentForm').attr('action', `/tickets/comments/${commentId}`);
                editModal.show();
            });

            $('.delete-comment-btn').on('click', function() {
                const commentId = $(this).data('id');
                $('#deleteCommentForm').attr('action', `/tickets/comments/${commentId}`);
                deleteModal.show();
            });

            editSpan.on('click', function() {
                editModal.hide();
            });

            deleteSpan.on('click', function() {
                deleteModal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(editModal)) {
                    editModal.hide();
                }
                if ($(event.target).is(deleteModal)) {
                    deleteModal.hide();
                }
            });

            $('.tasks-container input[type="checkbox"]').on('change', function() {
                var taskId = $(this).attr('id').split('-')[1];
                var completed = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/tasks/' + taskId,
                    type: 'PATCH',
                    data: {
                        completed: completed,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Task updated successfully');
                    },
                    error: function(response) {
                        console.log('Error updating task');
                    }
                });
            });
        });
    </script>

@endsection