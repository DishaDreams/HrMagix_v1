
<?php echo e(Form::model($payee, ['route' => ['payees.update', $payee->id], 'method' => 'PUT'])); ?>

<div class="modal-body">
    <div class="row">
        <div class="form-group">
            <?php echo e(Form::label('payee_name', __('Payee Name'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('payee_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Payee Name')])); ?>

        </div>
        <div class="form-group">
            <?php echo e(Form::label('contact_number', __('Contact Number'), ['class' => 'form-label'])); ?>

            <?php echo e(Form::text('contact_number', null, ['class' => 'form-control','placeholder' => __('Enter Contact Number')])); ?>

        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn  btn-primary">
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/payees/edit.blade.php ENDPATH**/ ?>