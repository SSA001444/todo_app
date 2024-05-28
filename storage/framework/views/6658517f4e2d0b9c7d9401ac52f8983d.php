<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <?php if(session()->has('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session()->get('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="alert alert-danger">
                        <?php echo e($error); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>

    <section class="section-1">
        <div class="section-1-item">
            <div class="section-1-logo">
                <img alt="Logo" class="section-1-img" src="<?php echo e(asset('images/todo/header/to_do_3.png')); ?>">
            </div>
        </div>
        <div class="container-section-1">
            <div class="section-1-block">
                <form method="POST" action="<?php echo e(route('todos.store')); ?>">
                    <?php echo csrf_field(); ?>
                <div class="lop">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="input-sec1-tit" name="title" placeholder="Title">
                        </div>
                        <div class="col-6">
                            <label class="sec1-label">
                                <select class="sec1-select" name="group_id" id="group_id">
                                    <option value disabled selected>Select group</option>
                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($group->id); ?>" name="group_id"><?php echo e($group->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        url: "<?php echo e(route('todos.reorder')); ?>",
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
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
                    url: "<?php echo e(route('todos.update-status')); ?>",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
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

                    <?php $sortedTodos = \App\Models\Todo::orderBy('sort_order')->get(); ?>
                    <?php $__currentLoopData = $sortedTodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($todo->user->contains(auth()->user())): ?>
                        <tr data-todo-id="<?php echo e($todo->id); ?>">
                            <th><?php echo e($todo->title); ?></th>
                            <th><?php echo e($todo->group ? $todo->group->name : 'None'); ?></th>
                            <th><?php echo e($todo->commentary); ?></th>
                            <th><?php echo e($todo->created_at); ?></th>
                            <td>
                                <?php if($todo->is_completed): ?>
                                    <div class="badge bg-success">Completed</div>
                                <?php else: ?>
                                    <div class="badge bg-warning">Not Completed</div>
                                <?php endif; ?>
                                <input type="checkbox" class="todo-status-checkbox" data-todo-id="<?php echo e($todo->id); ?>" <?php echo e($todo->is_completed ? 'checked' : ''); ?>>
                            </td>
                            <td>
                                <a href="<?php echo e(route('todos.edit', ['todo' => $todo->id])); ?>" class="group-act-edit group-luke">Edit</a>
                                <button class="group-act-del delete-button" data-todo-id="<?php echo e($todo->id); ?>">Delete</button>
                                <a href="<?php echo e(route('todo.share', ['todo' => $todo->id])); ?>" class="group-act-edit group-luke">Share</a>
                            </td>
                            <th><?php echo e($todo->shared_from); ?></th>
                        </tr>
                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            </div>
    </div>
    <img src="<?php echo e(asset('images/sec1/many/1.png')); ?>" alt="" class="sec1-circle1 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/many/2.png')); ?>" alt="" class="sec1-circle2 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/inside/1.png')); ?>" alt="" class="sec1-circle3 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/inside/2.png')); ?>" alt="" class="sec1-circle4 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/inside/3.png')); ?>" alt="" class="sec1-circle5 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/outside/1.png')); ?>" alt="" class="sec1-circle6 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/outside/2.png')); ?>" alt="" class="sec1-circle7 sec1-absolute">
    <img src="<?php echo e(asset('images/sec1/outside/3.png')); ?>" alt="" class="sec1-circle8 sec1-absolute">
    <div>

    </div>

    </section>

    <img src="<?php echo e(asset('images/todo/1.png')); ?>" alt="" class="bg-img bg-img1">
    <img src="<?php echo e(asset('images/todo/2.png')); ?>" alt="" class="bg-img bg-img2">
    <img src="<?php echo e(asset('images/todo/3.png')); ?>" alt="" class="bg-img bg-img3">
    <img src="<?php echo e(asset('images/todo/4.png')); ?>" alt="" class="bg-img bg-img4">

    <div class="sec13"></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/todo/todo.blade.php ENDPATH**/ ?>