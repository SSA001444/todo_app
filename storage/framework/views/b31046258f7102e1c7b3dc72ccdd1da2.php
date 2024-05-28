<?php $__env->startSection('content'); ?>
    <div class="messenger-container">
        <div class="sidebar">
            <div class="sidebar-header">
                Contacts
            </div>
            <div class="contacts">
                <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="contact-item">
                        <div class="contact-avatar">
                            <img src="<?php echo e($contact->profile_photo ? asset($contact->profile_photo) : 'https://via.placeholder.com/40'); ?>" alt="<?php echo e($contact->username); ?>" class="contact-avatar-img">
                        </div>
                        <div class="contact-name">
                            <a href="<?php echo e(route('messenger.dialog', ['userId' => $contact->id])); ?>"><?php echo e($contact->username); ?></a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                <?php if(isset($selectedUser)): ?>
                    Chat with <?php echo e($selectedUser->username); ?>

                <?php else: ?>
                    Select a contact to start chatting
                <?php endif; ?>
            </div>
            <div class="messages">
                <?php if(isset($messages)): ?>
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="message <?php echo e($message->user_id == Auth::id() ? 'sent' : 'received'); ?>">
                            <strong><?php echo e($message->user->username); ?>:</strong> <?php echo e($message->message); ?>

                            <?php if($message->edited): ?>
                                <div class="edited-label">
                                    edited <?php echo e($message->updated_at->addHours(3)->format('H:i')); ?>

                                </div>
                            <?php endif; ?>
                            <div class="sent-label">
                                sent <?php echo e($message->created_at->addHours(3)->format('H:i')); ?>

                            </div>
                            <?php if($message->user_id == Auth::id()): ?>
                                <div class="message-actions">
                                    <form action="<?php echo e(route('messenger.delete', ['messageId' => $message->id])); ?>" method="POST" class="inline-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-icon"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    <button type="button" class="btn-icon" data-message-id="<?php echo e($message->id); ?>" data-message-text="<?php echo e($message->message); ?>"><i class="fas fa-edit"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p>No messages yet.</p>
                <?php endif; ?>
            </div>
            <?php if(isset($selectedUser)): ?>
                <div class="message-input-container">
                    <form action="<?php echo e(route('messenger.send', ['userId' => $selectedUser->id])); ?>" method="POST" class="d-flex w-100">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="message" class="message-input" placeholder="Type a message">
                        <button type="submit" class="send-button">Send</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="editMessageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editMessageForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-group">
                    <label for="editMessageText">Message</label>
                    <input type="text" id="editMessageText" name="message" class="message-input">
                </div>
                <button type="submit" class="send-button">Save changes</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var modal = $('#editMessageModal');
            var span = $('.close');
            span.on('click', function() {
                modal.hide();
            });

            $('.btn-icon[data-message-id]').on('click', function() {
                var messageId = $(this).data('message-id');
                var messageText = $(this).data('message-text');
                $('#editMessageText').val(messageText);
                $('#editMessageForm').attr('action', '/messenger/' + messageId);
                modal.show();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/messenger/messenger.blade.php ENDPATH**/ ?>