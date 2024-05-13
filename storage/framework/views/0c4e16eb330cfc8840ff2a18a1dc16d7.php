<div class="col-form-label">

    <div class="modal-body">
        <h6 class="mb-2"><?php echo e(__('Schedule Detail')); ?></h6>
        <dl class="row mb-0 align-items-center">
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Job')); ?></dt>
            <dd class="col-sm-9 text-sm"><?php echo e(!empty($interviewSchedule->applications) ? !empty($interviewSchedule->applications->jobs) ? $interviewSchedule->applications->jobs->title : '-' : '-'); ?></dd>
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Interview On')); ?></dt>
            <dd class="col-sm-9 text-sm"> <?php echo e(\Auth::user()->dateFormat($interviewSchedule->date).' '. \Auth::user()->timeFormat($interviewSchedule->time)); ?></dd>
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Assign Employee')); ?></dt>
            <dd class="col-sm-9 text-sm"><?php echo e(!empty($interviewSchedule->users)?$interviewSchedule->users->name:'-'); ?></dd>

        </dl>

    </div>
    <div class="modal-body">
        <h6 class="mb-2"><?php echo e(__('Candidate Detail')); ?></h6>
        <dl class="row mb-0 align-items-center">
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Name')); ?></dt>
            <dd class="col-sm-9 text-sm"><?php echo e(($interviewSchedule->applications)?$interviewSchedule->applications->name:'-'); ?></dd>
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Email')); ?></dt>
            <dd class="col-sm-9 text-sm"> <?php echo e(($interviewSchedule->applications)?$interviewSchedule->applications->email:'-'); ?></dd>
            <dt class="col-sm-3 h6 text-sm"><?php echo e(__('Phone')); ?></dt>
            <dd class="col-sm-9 text-sm"><?php echo e(($interviewSchedule->applications)?$interviewSchedule->applications->phone:'-'); ?></dd>
        </dl>
    </div>
    <div class="modal-body">
        <h6 class="mb-2"><?php echo e(__('Candidate Status')); ?></h6>
        <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="custom-control custom-radio">
                <input type="radio" id="stage_<?php echo e($stage->id); ?>" name="stage" data-scheduleid="<?php echo e($interviewSchedule->candidate); ?>" value="<?php echo e($stage->id); ?>" class="form-check-input stages" <?php echo e(!empty($interviewSchedule->applications)?!empty($interviewSchedule->applications->stage==$stage->id)?'checked':'':''); ?>>
                <label class="custom-control-label" for="stage_<?php echo e($stage->id); ?>"><?php echo e($stage->title); ?></label>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="modal-footer">
        <a href="#" data-url="<?php echo e(route('job.on.board.create', $interviewSchedule->candidate)); ?>"  data-ajax-popup="true"  class="btn btn-primary" >  <?php echo e(__('Add to Job OnBoard')); ?></a>
    </div>
</div>
<?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/interviewSchedule/show.blade.php ENDPATH**/ ?>