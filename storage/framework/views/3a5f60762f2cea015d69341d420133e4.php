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



    <section class="groups">
        <div class="gropus">
            <div class="groups-item2">
                <div class="section-1-item">
                    <div class="section-1-logo">
                        <img alt="Logo" class="section-1-img" src="<?php echo e(asset('images/todo/header/to_do_3.png')); ?>">
                    </div>
                </div>
            </div>

            <div class="groups-sec">
                <div class="groups-item">
                    <div class="groups-center">
                        <h2 class="group-title">Add group</h2>
                         <form action="<?php echo e(route('groups.store')); ?>" method="POST" class="group-form" >
                             <?php echo csrf_field(); ?>
                             <input type="text" name="name" class="group-select" PLACEHOLDER="Title group">
                             <button class="but-group" type="submit">Submit</button>
                         </form>

                        <script>
                            $(document).ready(function(){
                                $('#sortable-table tbody').sortable({
                                    axis: 'y',
                                    update: function (event, ui){
                                        var groupId = $("#sortable-table tbody tr").map(function () {
                                            return $(this).data("group-id");
                                        }).get();

                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo e(route("groups.reorder")); ?>",
                                            data:
                                                {
                                                    groupId: groupId
                                                },
                                            success: function(data){
                                                console.log("Order updated successfully");
                                            },
                                            error: function(error){
                                                console.log("Error updating order: " +error);
                                            }
                                        });
                                    }
                                });
                                $("#sortable-table tbody").disableSelection();
                            });
                        </script>

                        <h2 class="group-title2">All Groups</h2>

                        <div class="table-wrapper">
                                <table class="fl-table" id="sortable-table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Created at</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php $counter=1 ?>
                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($counter); ?></td>
                                            <td><?php echo e($group->name); ?></td>
                                            <td><?php echo e($group->created_at); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('groups.edit', ['group' => $group->id])); ?>" class="group-act-edit group-luke">Edit</a>
                                                <a href="<?php echo e(route('groups.destroy', ['group' => $group->id] )); ?>" class="group-act-del">Delete</a>

                                            </td>
                                        </tr>
                                        <?php $counter++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\TicketSystem\resources\views/todo/createGroup.blade.php ENDPATH**/ ?>