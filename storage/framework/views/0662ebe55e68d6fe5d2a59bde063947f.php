<?php
    $setting = App\Models\Utility::settings();
    $plan = Utility::getChatGPTSettings();
?>
<?php echo e(Form::open(['url' => 'interview-schedule', 'method' => 'post'])); ?>

<div class="modal-body">

    <?php if($plan->enable_chatgpt == 'on'): ?>
    <div class="text-end">
        <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true" data-url="<?php echo e(route('generate', ['interview-schedule'])); ?>"
            data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Generate')); ?>"
            data-title="<?php echo e(__('Generate Content With AI')); ?>">
            <i class="fas fa-robot"></i><?php echo e(__(' Generate With AI')); ?>

        </a>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('candidate', __('Interview To'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::select('candidate', $candidates, null, ['class' => 'form-control select2', 'required' => 'required'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('employee', __('Interviewer'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::select('employee', $employees, null, ['class' => 'form-control select2', 'required' => 'required'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('date', __('Interview Date'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::text('date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off', 'id' => 'currentDate'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('time', __('Interview Time'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::time('time', null, ['class' => 'form-control ', 'id' => 'currentTime'])); ?>

        </div>
        <div class="form-group ">
            <?php echo e(Form::label('comment', __('Comment'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '3'])); ?>

        </div>
        <?php if(isset($setting['is_enabled']) && $setting['is_enabled'] =='on'): ?>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('synchronize_type', __('Synchroniz in Google Calendar ?'), ['class' => 'form-label'])); ?>

            <div class=" form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow"
                    value="google_calender">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn  btn-primary">

</div>
<?php echo e(Form::close()); ?>


<?php if($candidate != 0): ?>
    <script>
        $('select#candidate').val(<?php echo e($candidate); ?>).trigger('change');
    </script>
<?php endif; ?>

<script>
    const getTwoDigits = (value) => value < 10 ? `0${value}` : value;

    const formatDate = (date) => {
        const day = getTwoDigits(date.getDate());
        const month = getTwoDigits(date.getMonth() + 1); // add 1 since getMonth returns 0-11 for the months
        const year = date.getFullYear();

        return `${year}-${month}-${day}`;
    }

    const formatTime = (date) => {
        const hours = getTwoDigits(date.getHours());
        const mins = getTwoDigits(date.getMinutes());

        return `${hours}:${mins}`;
    }

    const date = new Date();
    document.getElementById('currentDate').value = formatDate(date);
    document.getElementById('currentTime').value = formatTime(date);
</script><?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/interviewSchedule/create.blade.php ENDPATH**/ ?>