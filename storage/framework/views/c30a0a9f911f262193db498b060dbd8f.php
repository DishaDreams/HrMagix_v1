<?php echo e(Form::open(['url' => 'job-application', 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            <?php echo e(Form::label('job', __('Job'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::select('job', $jobs, null, ['class' => 'form-control select2', 'id' => 'jobs'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('name', __('Name'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('name', null, ['class' => 'form-control name'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('email', __('Email'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('email', null, ['class' => 'form-control'])); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('phone', __('Phone'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('phone', null, ['class' => 'form-control'])); ?>

        </div>
        <div class="form-group col-md-6 gender d-none">
            
            <?php echo Form::label('dob', __('Date of Birth'), ['class' => 'form-label']); ?>

            <?php echo Form::date('dob', old('dob'), ['class' => 'form-control', 'autocomplete' => 'off']); ?>

        </div>
        <div class="form-group col-md-6 gender d-none">
            <?php echo Form::label('gender', __('Gender'), ['class' => 'form-label']); ?>

            <div class="d-flex radio-check">
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_male" value="Male" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_male"><?php echo e(__('Male')); ?></label>
                </div>
                <div class="form-check form-check-inline form-group">
                    <input type="radio" id="g_female" value="Female" name="gender" class="form-check-input">
                    <label class="form-check-label" for="g_female"><?php echo e(__('Female')); ?></label>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12 address d-none">
            <?php echo e(Form::label('address', __('Address'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::textarea('address', null, ['class' => 'form-control' ,'placeholder'=>'Enter address','rows'=>'3'])); ?>

        </div>
        <div class="form-group col-md-6 address d-none">
            <?php echo e(Form::label('city', __('City'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('city', null, ['class' => 'form-control' ,'placeholder'=>'Enter city'])); ?>

        </div>
        <div class="form-group col-md-6 address d-none">
            <?php echo e(Form::label('state', __('State'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('state', null, ['class' => 'form-control' ,'placeholder'=>'Enter state'])); ?>

        </div>
        <div class="form-group col-md-6 address d-none">
            <?php echo e(Form::label('country', __('Country'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('country', null, ['class' => 'form-control' ,'placeholder'=>'Enter country'])); ?>

        </div>
        <div class="form-group col-md-6 address d-none">
            <?php echo e(Form::label('zip_code', __('Zip Code'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('zip_code', null, ['class' => 'form-control' ,'placeholder'=>'Enter zip code'])); ?>

        </div>

        <div class="form-group col-md-6 resume d-none">
            
            <?php echo e(Form::label('profile', __('Profile'), ['class' => 'form-label'])); ?>

            <div class="choose-files ">
                <label for="profile" >
                    <div class=" bg-primary image_update">
                        <i class="ti ti-upload px-1"></i><?php echo e(__('Choose file here')); ?>

                    </div>
                    <input type="file" class="custom-input-file d-none" name="profile" id="profile" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                    <img id="blah" src="" width="100" />
                </label>
            </div>
        </div>

        <div class="form-group col-md-6 resume d-none">
            <?php echo e(Form::label('resume', __('CV / Resume'), ['class' => 'form-label'])); ?>

            <div class="choose-files ">
                <label for="resume" >
                    <div class=" bg-primary image_update">
                        <i class="ti ti-upload px-1"></i><?php echo e(__('Choose file here')); ?>

                    </div>
                    <input type="file" name="resume" id="resume" class="custom-input-file d-none" data-filename="resume_create"  onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">
                    <img id="blah1"  width="100"src=""/>
                </label>
            </div>
        </div>

        <div class="form-group col-md-12 resume d-none">
            
            <?php echo e(Form::label('cover_letter', __('Cover Letter'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::textarea('cover_letter', null, ['class' => 'form-control', 'rows' => '3'])); ?>

        </div>
        <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group col-md-12  resume question_<?php echo e($question->id); ?> d-none">
                
                <?php echo e(Form::label($question->question, $question->question, ['class' => 'form-label'])); ?>

                <input type="text" class="form-control" name="question[<?php echo e($question->question); ?>]"
                    <?php echo e($question->is_required == 'yes' ? 'required' : ''); ?>>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn  btn-primary">
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/jobApplication/create.blade.php ENDPATH**/ ?>