@extends('auth.layouts')

@section('content')

    <div class="alert-container">
        <div>
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
                    <button id="editTicketBtn" class="btn btn-primary">{{ __('messages.edit_ticket') }}</button>
                    <button id="toggleStatusBtn" class="btn btn-secondary">{{ $ticket->status == 'open' ? __('messages.close_ticket') : __('messages.open_ticket') }}</button>
                @endif
            </div>

            <div class="tasks-container">
                <h3 class="tasks-h3">{{ __('messages.tasks') }} <button id="addTaskBtn" class="btn btn-primary add-task-button">{{ __('messages.add_task') }}</button></h3>
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
                <h3>{{ __('messages.comments') }}</h3>
                @foreach($comments as $comment)
                    <div class="comment-block">
                        @if($comment->trashed())
                            <div class="comment-text">{{ __('messages.deleted_comment', ['deleted_by' => Crypt::decryptString($comment->deleted_by), 'deleted_by_role' => $comment->deleted_by_role, 'deletion_reason' => $comment->deletion_reason]) }}</div>
                        @else
                            <div class="comment-author">{{ Crypt::decryptString($comment->user->username) }}</div>
                            @if($comment->photo)
                                <img src="{{ asset($comment->photo) }}" alt="Comment Photo" class="comment-photo">
                            @endif
                            <div class="comment-actions">
                                @if((($comment->user_id == Auth::id() || Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') && $ticket->team_id == Auth::user()->team_id) && $ticket->status == 'open' )
                                    <button class="edit-comment-btn" data-id="{{ $comment->id }}">{{ __('messages.edit') }}</button>
                                    <button class="delete-comment-btn" data-id="{{ $comment->id }}">{{ __('messages.delete') }}</button>
                                @endif
                            </div>
                            <div class="comment-text">{!! nl2br(e($comment->text)) !!}</div>
                            <div class="comment-date">{{ __('messages.by') }} {{ Crypt::decryptString($comment->user->username) }} {{ __('messages.at') }} {{ $comment->created_at }}</div>
                        @endif
                    </div>
                @endforeach

                @if($ticket->status === 'open')
                <form action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data" class="comment-form">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                    <textarea name="comment" class="comment-input" placeholder="{{ __('messages.add_comment') }}" required></textarea>
                    <input type="file" name="photo" accept="image/*" class="comment-photo-input">
                    <button type="submit" class="btn btn-primary">{{ __('messages.add_comment') }}</button>
                </form>
                @endif
            </div>
        </div>
        <!-- Edit Ticket Modal -->
        <div id="editTicketModal" class="modal-task">
            <div class="modal-content-task">
                <span class="close-task" id="close_edit_ticket">&times;</span>
                <h2>{{ __('messages.edit_ticket') }}</h2>
                <form id="editTicketForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <span></span>
                    <input name="title" class="task-input" id="edit-ticket-title" required>
                    <textarea name="description" class="task-input" id="edit-ticket-description" required></textarea>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update_ticket') }}</button>
                </form>
            </div>
        </div>
        <!-- Modal for adding tasks -->
        <div id="addTaskModal" class="modal-task">
            <div class="modal-content-task">
                <span class="close-task" id="add_task">&times;</span>
                <h2>{{ __('messages.add_task') }}</h2>
                <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                    <label for="task">{{ __('messages.task') }}:</label>
                    <input type="text" name="task" class="task-input" required>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add_task') }}</button>
                </form>
            </div>
        </div>
        <!-- Edit Comment Modal -->
        <div id="editCommentModal" class="modal-task">
            <div class="modal-content-task">
                <span class="close-task" id="edit_comment">&times;</span>
                <h2>{{ __('messages.edit_comment') }}</h2>
                <form id="editCommentForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <input name="comment" class="task-input" id="edit-comment-text" required>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update_comment') }}</button>
                </form>
            </div>
        </div>
        <!-- Delete Comment Modal -->
        <div id="deleteCommentModal" class="modal-task">
            <div class="modal-content-task">
                <span class="close-task" id="delete_comment">&times;</span>
                <h2>{{ __('messages.delete_comment') }}</h2>
                <form id="deleteCommentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input name="reason" id="delete-reason" class="task-input" placeholder="{{ __('messages.reason_for_deletion') }}" required>
                    <button type="submit" class="btn btn-primary">{{ __('messages.delete_comment') }}</button>
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
            var toggleStatusBtn = $('#toggleStatusBtn');
            var addCommentForm = $('.comment-form');

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
                const commentText = $(this).closest('.comment-text').find('div').text();
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

            function setTaskCheckboxesStatus(disabled) {
                $('.tasks-container input[type="checkbox"]').prop('disabled', disabled);
            }

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

            function toggleCommentActions(show) {
                if (show) {
                    $('.edit-comment-btn, .delete-comment-btn').show();
                    addCommentForm.show();
                    taskBtn.show();
                } else {
                    $('.edit-comment-btn, .delete-comment-btn').hide();
                    addCommentForm.hide();
                    taskBtn.hide();
                }
            }

            toggleStatusBtn.on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/tickets/' + {{ $ticket->id }} + '/toggle-status',
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const newStatus = response.status;
                            $('.ticket-status')
                                .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1))
                                .removeClass('open closed')
                                .addClass(newStatus);
                            $('#toggleStatusBtn').text(newStatus === 'open' ? '{{ __('messages.close_ticket') }}' : '{{ __('messages.open_ticket') }}');
                            setTaskCheckboxesStatus(newStatus === 'closed');
                            toggleCommentActions(newStatus === 'open');
                            location.reload();
                        }
                    },
                    error: function(response) {
                        console.log('Error toggling ticket status');
                        if (response.responseJSON && response.responseJSON.errors) {
                            console.log(response.responseJSON.errors);
                        }
                    }
                });
            });
            setTaskCheckboxesStatus('{{ $ticket->status }}' === 'closed');
            toggleCommentActions('{{ $ticket->status }}' === 'open');
        });
    </script>

@endsection
