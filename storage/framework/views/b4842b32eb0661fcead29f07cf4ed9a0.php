<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Job Stage')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Job Stage')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Job Stage')): ?>
        <a href="#" data-url="<?php echo e(route('job-stage.create')); ?>" data-ajax-popup="true"
            data-title="<?php echo e(__('Create New Job Stage')); ?>" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="<?php echo e(__('Create')); ?>">
            <i class="ti ti-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-3">
            <?php echo $__env->make('layouts.hrm_setup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-9">
            

            <div class="theme-detail-card">
                <ul class="list-unstyled list-group sortable">
                    <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between"
                            data-id="<?php echo e($stage->id); ?>">
                            <h6 class="mb-0">
                                <i class="me-3" data-feather="move"></i>
                                <span><?php echo e($stage->title); ?></span>
                            </h6>
                            <div class="d-flex justify-content-end">
                                <div class="action-btn bg-info ms-2">
                                    <a class="mx-3 btn btn-sm  align-items-center" href="#"
                                        data-url="<?php echo e(route('job-stage.edit', $stage->id)); ?>" data-ajax-popup="true"
                                        data-size="md" data-bs-toggle="tooltip" title=""
                                        data-title="<?php echo e(__('Edit Job Stage')); ?>"
                                        data-bs-original-title="<?php echo e(__('Edit')); ?>"><i
                                            class="ti ti-pencil  text-white"></i></a>
                                </div>
                                <div class="action-btn bg-danger ms-2">
                                    <?php echo Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['job-stage.destroy', $stage->id],
                                        'id' => 'delete-form-' . $stage->id,
                                    ]); ?>

                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                        aria-label="Delete"><i class="ti ti-trash text-white "></i></a>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <p class="mt-3"><b>Note: You can easily order change of card blocks using drag & drop.</b></p>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('js/jquery-ui.min.js')); ?>"></script>
    <?php if(\Auth::user()->type == 'company'): ?>
        <script>
            $(function() {
                $(".sortable").sortable();
                $(".sortable").disableSelection();
                $(".sortable").sortable({
                    stop: function() {
                        var order = [];
                        $(this).find('li').each(function(index, data) {
                            order[index] = $(data).attr('data-id');
                        });

                        $.ajax({
                            url: "<?php echo e(route('job.stage.order')); ?>",
                            data: {
                                order: order,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            success: function(data) {},
                            error: function(data) {
                                data = data.responseJSON;
                                toastr('Error', data.error, 'error')
                            }
                        })
                    }
                });
            });
        </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/jobStage/index.blade.php ENDPATH**/ ?>