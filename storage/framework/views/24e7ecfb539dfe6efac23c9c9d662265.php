<?php $__env->startSection('content'); ?>
<div class="input-center">
    <div class="input-item">
        <div class="input-item-logo">
            <img class="input-logo" src="<?php echo e(asset('images/auth/login/login.png')); ?>" alt="">
        </div>
        <form action="<?php echo e(route('store')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div id="error-container" class="alert alert-danger" style="display: none;">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="input-lox">
                <img class="input-img" src="<?php echo e(asset('images/auth/login/1.png')); ?>" alt="">
            </div>
            <div class="input-input">
                <input type="text" class="input <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="username"  placeholder="Enter your username" value="<?php echo e(old('username')); ?>">
                <?php if($errors->has('username')): ?>
                    <span class="invalid-feedback" role="alert"><?php echo e($errors->first('username')); ?></span>
                <?php endif; ?>
            </div>

            <div class="input-input">
                <input type="email" class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email"  placeholder="Enter your email" value="<?php echo e(old('email')); ?>">
                <?php if($errors->has('email')): ?>
                    <span class="invalid-feedback" role="alert"><?php echo e($errors->first('email')); ?></span>
                <?php endif; ?>
            </div>

            <div class="input-input">
                <input type="password" class="input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="Enter your password">
                <?php if($errors->has('password')): ?>
                    <span class="invalid-feedback" role="alert"><?php echo e($errors->first('password')); ?></span>
                <?php endif; ?>
            </div>

            <div class="input-input">
                <input type="password" class="input" name="password_confirmation" placeholder="Enter your password again">
            </div>

            <div class="input-item-but">
                <button type="submit" class="add-input">Register</button>
            </div>

            <div class="input-item-text">
                <p class="input-title">Registration</p>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/auth/register.blade.php ENDPATH**/ ?>