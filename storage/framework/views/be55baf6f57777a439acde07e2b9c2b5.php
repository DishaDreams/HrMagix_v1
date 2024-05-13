<?php
    $plan = Utility::getChatGPTSettings();
?>

<?php echo e(Form::open(['url' => 'ticket', 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

<div class="modal-body">

    <?php if($plan->enable_chatgpt == 'on'): ?>
        <div class="text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="<?php echo e(route('generate', ['ticket'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top"
                title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                <i class="fas fa-robot"></i><?php echo e(__(' Generate With AI')); ?>

            </a>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('title', __('Subject'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Ticket Subject')])); ?>

        </div>
        <?php if(\Auth::user()->type != 'employee'): ?>
            <div class="form-group col-md-6">
                <?php echo e(Form::label('employee_id', __('Ticket for Employee'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::select('employee_id', $employees, null, ['class' => 'form-control select2 employee_id', 'placeholder' => __('Select Employee')])); ?>

            </div>
        <?php else: ?>
            <?php echo Form::hidden('employee_id', !empty($employees) ? $employees->id : 0, ['id' => 'employee_id']); ?>

        <?php endif; ?>

        <div class="form-group col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('priority', __('Priority'), ['class' => 'col-form-label'])); ?>

                <select name="priority" class="form-control">
                    <option value="low"><?php echo e(__('Low')); ?></option>
                    <option value="medium"><?php echo e(__('Medium')); ?></option>
                    <option value="high"><?php echo e(__('High')); ?></option>
                    <option value="critical"><?php echo e(__('critical')); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('end_date', __('End Date'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::date('end_date', date('Y-m-d'), ['class' => 'form-control current_date', 'autocomplete' => 'off'])); ?>

            </div>
        </div>

        <div class="form-group col-md-12">
            <?php echo Form::label('description', __('Description'), ['class' => 'col-form-label']); ?>

            <textarea class="form-control summernote-simple-2" name="description" id="exampleFormControlTextarea1" rows="7"></textarea>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label class="form-label"><?php echo e(__('Attachments')); ?></label>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-lg-12 col-md-12">
                        <div class="choose-file form-group">
                            <label for="file" class="form-label">
                                <input type="file" name="attachment" id="attachment" class="form-control <?php echo e($errors->has('attachment') ? ' is-invalid' : ''); ?>" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" data-filename="attachment_selection">
                                <div class="invalid-feedback">
                                    <?php echo e($errors->first('attachment')); ?>

                                </div>
                            </label>
                            <p class="attachment_selection"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label class="form-label"></label>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-lg-12 col-md-12">
                        <img src="" id="blah" width="60%" />
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo e(Form::label('status', __('Status'), ['class' => 'col-form-label'])); ?>

            <select name="status" class="form-control " id="status">
                <option value="close"><?php echo e(__('Close')); ?></option>
                <option value="open"><?php echo e(__('Open')); ?></option>
                <option value="onhold"><?php echo e(__('On Hold')); ?></option>
            </select>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn  btn-primary">

</div>
<?php echo e(Form::close()); ?>


<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);
    });
</script>
<?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/ticket/create.blade.php ENDPATH**/ ?>