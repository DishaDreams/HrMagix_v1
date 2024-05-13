
<?php echo e(Form::open(['url' => 'trainer', 'method' => 'post'])); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('branch', __('Branch'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::select('branch', $branches, null, ['class' => 'form-control select2', 'required' => 'required'])); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('firstname', __('First Name'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('firstname', null, ['class' => 'form-control', 'required' => 'required'])); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('lastname', __('Last Name'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('lastname', null, ['class' => 'form-control', 'required' => 'required'])); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('contact', __('Contact'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('contact', null, ['class' => 'form-control', 'required' => 'required'])); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('email', __('Email'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::email('email', null, ['class' => 'form-control', 'required' => 'required'])); ?>

            </div>
        </div>
        <div class="form-group col-lg-12">
            <?php echo e(Form::label('expertise', __('Expertise'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::textarea('expertise', null, ['class' => 'form-control', 'placeholder' => __('Expertise'),'rows'=>'3'])); ?>

        </div>
        <div class="form-group col-lg-12">
            <?php echo e(Form::label('address', __('Address'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => __('Address'),'rows'=>'3'])); ?>

        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn btn-primary">
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/trainer/create.blade.php ENDPATH**/ ?>