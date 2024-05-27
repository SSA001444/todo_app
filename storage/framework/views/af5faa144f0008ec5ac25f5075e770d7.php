<!DOCTYPE html>
<html lang="en">
<head>
    <title>Papers Planes</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('images/bird.png')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

        .navbar-laravel
        {
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
        }
        .navbar-brand , .nav-link, .my-form, .login-form
        {
            font-family: Raleway, sans-serif;
        }
        .my-form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .my-form .row
        {
            margin-left: 0;
            margin-right: 0;
        }
        .log_reg_form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .log_reg_form .row
        {
            margin-left: 0;
            margin-right: 0;
        }

    </style>
</head>
<body>

<script>
    $(document).ready(function() {
        $('.header-img-php').click(function() {
            $('#profile-photo-input').click();
        });
        $('#profile-photo-input').change(function() {
            var formData = new FormData();
            formData.append('profile_photo', $(this)[0].files[0]);
            formData.append('_token', "<?php echo e(csrf_token()); ?>");

            $.ajax({
                type: 'POST',
                url: '/profile/update-photo',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Profile photo updated successfully');
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error updating profile photo:', error.responseJSON || error.responseText);
                }
            });
        });
    });
</script>

<header>
    <div class="container">
        <?php if(auth()->guard()->guest()): ?>
            <div class="header-butt-2">
                <a class="header-logreg" href="<?php echo e(route('login')); ?>">Login</a>
                <a class="header-logreg" href="<?php echo e(route('register')); ?>">Register</a>
            </div>
        <?php else: ?>
            <div class="header-item">
                <div class="header-logo">
                    <div class="header-php-div">
                        <label for="profile-photo-input" class="header-php">
                            <?php
                                $userPhoto = auth()->user()->profile_photo;
                                $defaultPhoto = asset('images/auth/login/2.png');
                                $photoPath = $userPhoto && file_exists(public_path($userPhoto)) ? asset($userPhoto) : asset($defaultPhoto);
                            ?>
                            <img src="<?php echo e($photoPath); ?>" alt="Profile Photo" class="header-img-php">
                            <div class="overlay"></div>
                            <input type="file" name="profile_photo" id="profile-photo-input" style="display: none;">
                            <img src="<?php echo e(asset('images/todo/header/12312.png')); ?>" alt="Profile Photo" class="header-hover-img">
                        </label>
                    </div>
                    <div class="user-info">
                        <span class="username"><?php echo e(auth()->user()->username); ?></span>
                        <span class="role"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
                    </div>
                    <a class="header-logout" href="<?php echo e(route('logout')); ?>">Logout</a>
                </div>
                <div class="header-butt">
                    <form class="header-form">
                        <label>
                            <select class="header-select" name="" id="">
                                <option value="">EN</option>
                                <option value="">LV</option>
                            </select>
                        </label>
                    </form>
                    <?php if(auth()->user()->role == 'admin' || auth()->user()->role == 'moderator'): ?>
                        <a class="header-logout" href="<?php echo e(route('admin.index')); ?>">Admin Panel</a>
                    <?php endif; ?>
                        <a class="header-logout" href="<?php echo e(route('todos.index')); ?>">Todos</a>
                    <a class="header-logout" href="<?php echo e(route('groups.index')); ?>">Groups</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>
<div class="header-line">
    <hr class="header-hr">
</div>

<?php echo $__env->yieldContent('content'); ?>

</body>
</html>
<?php /**PATH F:\TicketSystem\resources\views/auth/layouts.blade.php ENDPATH**/ ?>