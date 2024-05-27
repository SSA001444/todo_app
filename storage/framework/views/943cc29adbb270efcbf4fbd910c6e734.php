<?php $__env->startSection('content'); ?>
    <div class="container groups">
        <div class="groups-center">
            <div class="groups-item-fullwidth">
                <h1 class="group-title">Admin Panel</h1>

                <div class="alert-container">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($error); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
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
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($user->id); ?></td>
                                        <td><?php echo e($user->username); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td>
                                            <?php if($user->id !== Auth::user()->id): ?>
                                                <?php if(Auth::user()->role === 'admin'): ?>
                                                    <form action="<?php echo e(route('admin.users.updateRole', $user)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="select-wrapper">
                                                            <select name="role" class="group-select" onchange="this.form.submit()">
                                                                <option value="user" <?php echo e($user->role == 'user' ? 'selected' : ''); ?>>User</option>
                                                                <option value="moderator" <?php echo e($user->role == 'moderator' ? 'selected' : ''); ?>>Moderator</option>
                                                            </select>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <?php echo e(ucfirst($user->role)); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo e(ucfirst($user->role)); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user->id !== Auth::user()->id): ?>
                                                <?php if(Auth::user()->role === 'admin' || (Auth::user()->role === 'moderator' && $user->role !== 'moderator' && $user->role !== 'admin')): ?>
                                                    <form action="<?php echo e(route('admin.users.remove', $user)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="but-group">Remove</button>
                                                    </form>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                            <h2 class="group-title2">Invite New User</h2>
                            <form action="<?php echo e(route('admin.users.invite')); ?>" method="POST" class="group-form">
                                <?php echo csrf_field(); ?>
                                <input type="text" name="identifier" class="group-select" placeholder="User Email or Username" required>
                                <button type="submit" class="but-group">Send Invitation</button>
                            </form>
                </div>
            </div>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/admin/admin.blade.php ENDPATH**/ ?>