<?php $__env->startSection('content'); ?>
    <div class="container">
        <?php if($teamName): ?>
            <h1 class="team-status-header">You are a member of the team: <?php echo e($teamName); ?></h1>
        <?php else: ?>
            <div class="no-team-container">
                <h1 class="no-team-header">You need to create your own team or wait to be invited</h1>
                <button id="createTeamBtn" class="create-team-btn">Create Team</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="createTeamModal" class="modal-team">
        <div class="modal-content-team">
            <span class="close-team">&times;</span>
            <h2>Create a New Team</h2>
            <form id="createTeamForm" action="<?php echo e(route('teams.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <label for="teamName">Team Name</label>
                <input type="text" id="teamName" name="teamName" class="input-team-name" required>
                <button type="submit" class="submit-team-btn">Create</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var modal = $('#createTeamModal');
            var btn = $('#createTeamBtn');
            var span = $('.close-team');

            btn.on('click', function() {
                modal.show();
            });

            span.on('click', function() {
                modal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/main/team-status.blade.php ENDPATH**/ ?>