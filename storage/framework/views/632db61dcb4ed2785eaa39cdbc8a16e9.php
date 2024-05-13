<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Plan')); ?>

<?php $__env->stopSection(); ?>

<?php
    $admin_payment_setting = App\Models\Utility::getAdminPaymentSetting();
?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Plan')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Plan')): ?>
        <?php if(
            !empty($admin_payment_setting) &&
                ($admin_payment_setting['is_manually_enabled'] == 'on' ||
                    ($admin_payment_setting['is_banktransfer_enabled'] == 'on' &&
                        !empty($admin_payment_setting['bank_details'])) ||
                    ($admin_payment_setting['is_stripe_enabled'] == 'on' &&
                        !empty($admin_payment_setting['stripe_key']) &&
                        !empty($admin_payment_setting['stripe_secret'])) ||
                    ($admin_payment_setting['is_paypal_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paypal_client_id']) &&
                        !empty($admin_payment_setting['paypal_secret_key'])) ||
                    ($admin_payment_setting['is_paystack_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paystack_public_key']) &&
                        !empty($admin_payment_setting['paystack_secret_key'])) ||
                    ($admin_payment_setting['is_flutterwave_enabled'] == 'on' &&
                        !empty($admin_payment_setting['flutterwave_public_key']) &&
                        !empty($admin_payment_setting['flutterwave_secret_key'])) ||
                    ($admin_payment_setting['is_razorpay_enabled'] == 'on' &&
                        !empty($admin_payment_setting['razorpay_public_key']) &&
                        !empty($admin_payment_setting['razorpay_secret_key'])) ||
                    ($admin_payment_setting['is_paytm_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paytm_merchant_id']) &&
                        !empty($admin_payment_setting['paytm_merchant_key'])) ||
                    ($admin_payment_setting['is_mercado_enabled'] == 'on' &&
                        !empty($admin_payment_setting['mercado_access_token'])) ||
                    ($admin_payment_setting['is_mollie_enabled'] == 'on' &&
                        !empty($admin_payment_setting['mollie_api_key']) &&
                        !empty($admin_payment_setting['mollie_profile_id']) &&
                        !empty($admin_payment_setting['mollie_partner_id'])) ||
                    ($admin_payment_setting['is_skrill_enabled'] == 'on' && !empty($admin_payment_setting['skrill_email'])) ||
                    ($admin_payment_setting['is_coingate_enabled'] == 'on' &&
                        !empty($admin_payment_setting['coingate_auth_token'])) ||
                    ($admin_payment_setting['is_paymentwall_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paymentwall_public_key']) &&
                        !empty($admin_payment_setting['paymentwall_secret_key'])) ||
                    ($admin_payment_setting['is_toyyibpay_enabled'] == 'on' &&
                        !empty($admin_payment_setting['toyyibpay_category_code']) &&
                        !empty($admin_payment_setting['toyyibpay_secret_key'])) ||
                    ($admin_payment_setting['is_payfast_enabled'] == 'on' &&
                        !empty($admin_payment_setting['payfast_merchant_id']) &&
                        !empty($admin_payment_setting['payfast_merchant_key']) &&
                        !empty($admin_payment_setting['payfast_signature'])) ||
                    ($admin_payment_setting['is_iyzipay_enabled'] == 'on' &&
                        !empty($admin_payment_setting['iyzipay_public_key']) &&
                        !empty($admin_payment_setting['iyzipay_secret_key'])) ||
                    ($admin_payment_setting['is_sspay_enabled'] == 'on' &&
                        !empty($admin_payment_setting['sspay_category_code']) &&
                        !empty($admin_payment_setting['sspay_secret_key'])) ||
                    ($admin_payment_setting['is_paytab_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paytab_profile_id']) &&
                        !empty($admin_payment_setting['paytab_server_key']) &&
                        !empty($admin_payment_setting['paytab_region'])) ||
                    ($admin_payment_setting['is_benefit_enabled'] == 'on' &&
                        !empty($admin_payment_setting['benefit_api_key']) &&
                        !empty($admin_payment_setting['benefit_secret_key'])) ||
                    ($admin_payment_setting['is_cashfree_enabled'] == 'on' &&
                        !empty($admin_payment_setting['cashfree_api_key']) &&
                        !empty($admin_payment_setting['cashfree_secret_key'])) ||
                    ($admin_payment_setting['is_aamarpay_enabled'] == 'on' &&
                        !empty($admin_payment_setting['aamarpay_store_id']) &&
                        !empty($admin_payment_setting['aamarpay_signature_key']) &&
                        !empty($admin_payment_setting['aamarpay_description'])) ||
                    ($admin_payment_setting['is_paytr_enabled'] == 'on' &&
                        !empty($admin_payment_setting['paytr_merchant_id']) &&
                        !empty($admin_payment_setting['paytr_merchant_key']) &&
                        !empty($admin_payment_setting['paytr_merchant_salt'])) ||
                    ($admin_payment_setting['is_yookassa_enabled'] == 'on' &&
                        !empty($admin_payment_setting['yookassa_shop_id']) &&
                        !empty($admin_payment_setting['yookassa_secret'])) ||
                    ($admin_payment_setting['is_midtrans_enabled'] == 'on' &&
                        !empty($admin_payment_setting['midtrans_secret'])) ||
                    ($admin_payment_setting['is_xendit_enabled'] == 'on' &&
                        !empty($admin_payment_setting['xendit_api']) &&
                        !empty($admin_payment_setting['xendit_token'])))): ?>
            <a href="#" data-url="<?php echo e(route('plans.create')); ?>" data-size="md" data-ajax-popup="true"
                data-title="<?php echo e(__('Create New Plan')); ?>" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                data-bs-original-title="<?php echo e(__('Create')); ?>">
                <i class="ti ti-plus"></i>
            </a>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">

        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-md-4">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                    style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                    <div class="card-body ">
                        <span class="price-badge bg-primary"><?php echo e($plan->name); ?></span>

                        <div class="d-flex flex-row-reverse m-0 p-0">

                            <?php if(\Auth::user()->type == 'super admin' && $plan->price > 0): ?>
                                <div class="action-btn bg-danger ms-2">
                                    <?php echo Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['plans.destroy', $plan->id],
                                        'id' => 'delete-form-' . $plan->id,
                                    ]); ?>

                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                        data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
                                        aria-label="Delete"><i class="ti ti-trash text-white text-white"></i></a>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Plan')): ?>
                                <div class="action-btn bg-primary ms-2">
                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                        data-ajax-popup="true" data-title="<?php echo e(__('Edit Plan')); ?>"
                                        data-url="<?php echo e(route('plans.edit', $plan->id)); ?>" data-size="lg" data-bs-toggle="tooltip"
                                        data-bs-original-title="<?php echo e(__('Edit')); ?>" data-bs-placement="top"><span
                                            class="text-white"><i class="ti ti-pencil"></i></span></a>
                                </div>
                            <?php endif; ?>

                            <?php if(\Auth::user()->type == 'super admin' && $plan->price > 0): ?>
                                <div class="ms-2 mt-1 justify-content-center active-tag">
                                    <div class="form-check form-switch custom-switch-v1 float-end">
                                        <input type="checkbox" name="plan_disable"
                                            class="form-check-input input-primary is_disable" value="1"
                                            data-id='<?php echo e($plan->id); ?>' data-name="<?php echo e(__('plan')); ?>"
                                            <?php echo e($plan->is_disable == 1 ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="plan_disable"></label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if(\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id): ?>
                                <span class="d-flex align-items-center ms-2">
                                    <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                    <span class="ms-2"><?php echo e(__('Active')); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>

                        <span
                            class="mb-4 f-w-600 p-price"><?php echo e(!empty($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$'); ?><?php echo e($plan->price); ?><small
                                class="text-sm">/ <?php echo e($plan->duration); ?></small></span><br>

                        <?php if($plan->price > 0): ?>
                            <span><?php echo e(__('Free Trial Days :')); ?>

                                <b><?php echo e(!empty($plan->trial_days) ? $plan->trial_days : 0); ?></b>
                            </span>
                        <?php endif; ?>
                        <p class="mb-0">
                            <?php echo e($plan->description); ?>

                        </p>

                        <ul class="list-unstyled my-4">
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                <?php echo e($plan->max_users == -1 ? __('Unlimited') : $plan->max_users); ?> <?php echo e(__('Users')); ?>

                            </li>
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                <?php echo e($plan->max_employees == -1 ? __('Unlimited') : $plan->max_employees); ?>

                                <?php echo e(__('Employees')); ?>

                            </li>
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                <?php echo e($plan->storage_limit == -1 ? __('Lifetime') : $plan->storage_limit); ?>

                                <?php echo e(__('MB Storage')); ?>

                            </li>
                            <li>
                                <span class="theme-avtar">
                                    <i class="text-primary ti ti-circle-plus"></i></span>
                                <?php echo e($plan->enable_chatgpt == 'on' ? __('Enable Chat GPT') : __('Disable Chat GPT')); ?>

                            </li>
                        </ul>
                        <div class="row d-flex justify-content-between">
                            <?php if(
                                (!empty($admin_payment_setting) &&
                                    ($admin_payment_setting['is_manually_enabled'] == 'on' ||
                                        $admin_payment_setting['is_banktransfer_enabled'] == 'on' ||
                                        $admin_payment_setting['is_stripe_enabled'] == 'on' ||
                                        $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                                        $admin_payment_setting['is_paystack_enabled'] == 'on' ||
                                        $admin_payment_setting['is_flutterwave_enabled'] == 'on' ||
                                        $admin_payment_setting['is_razorpay_enabled'] == 'on' ||
                                        $admin_payment_setting['is_mercado_enabled'] == 'on' ||
                                        $admin_payment_setting['is_paytm_enabled'] == 'on' ||
                                        $admin_payment_setting['is_mollie_enabled'] == 'on' ||
                                        $admin_payment_setting['is_skrill_enabled'] == 'on' ||
                                        $admin_payment_setting['is_iyzipay_enabled'] == 'on' ||
                                        $admin_payment_setting['is_sspay_enabled'] == 'on' ||
                                        $admin_payment_setting['is_paytab_enabled'] == 'on' ||
                                        $admin_payment_setting['is_benefit_enabled'] == 'on' ||
                                        $admin_payment_setting['is_cashfree_enabled'] == 'on' ||
                                        $admin_payment_setting['is_aamarpay_enabled'] == 'on' ||
                                        $admin_payment_setting['is_paytr_enabled'] == 'on' ||
                                        $admin_payment_setting['is_yookassa_enabled'] == 'on' ||
                                        $admin_payment_setting['is_midtrans_enabled'] == 'on' ||
                                        $admin_payment_setting['is_xendit_enabled'] == 'on' ||
                                        $admin_payment_setting['is_payfast_enabled'] == 'on' ||
                                        $admin_payment_setting['is_toyyibpay_enabled'] == 'on' ||
                                        $admin_payment_setting['is_coingate_enabled'] == 'on')) ||
                                    (!empty($admin_payment_setting) && $admin_payment_setting['is_paymentwall_enabled'] == 'on')): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Buy Plan')): ?>
                                    <?php if($plan->id != \Auth::user()->plan && \Auth::user()->type != 'super admin'): ?>
                                        <?php if(!$plan->price == 0): ?>
                                            <?php if($plan->price > 0 && \Auth::user()->trial_plan == 0 && \Auth::user()->plan != $plan->id && $plan->trial == 1): ?>
                                                <div class="col-6">
                                                    <a href="<?php echo e(route('plans.trial', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))); ?>"
                                                        class="btn btn-lg btn-primary btn-icon m-1"
                                                        data-title="<?php echo e(__('Start Free Trial')); ?>" data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        data-bs-original-title="<?php echo e(__('Start Free Trial')); ?>"
                                                        title="<?php echo e(__('Start Free Trial')); ?>"><?php echo e(__('Start Free Trial')); ?>

                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($plan->price > 0): ?>
                                                <div class="col-4">
                                                    <div class="d-grid text-center">
                                                        <a href="<?php echo e(route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))); ?>"
                                                            class="btn btn-lg btn-primary btn-icon m-1" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            data-bs-original-title="<?php echo e(__('Subscribe')); ?>"
                                                            title="<?php echo e(__('Subscribe')); ?>"><?php echo e(__('Subscribe')); ?></a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(\Auth::user()->type == 'company' && \Auth::user()->plan != $plan->id): ?>
                                <?php if($plan->id != 1): ?>
                                    <div class="col-2">
                                        <?php if(\Auth::user()->requested_plan != $plan->id): ?>
                                            <a href="<?php echo e(route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)])); ?>"
                                                class="btn btn-lg btn-primary btn-icon m-1"
                                                data-title="<?php echo e(__('Send Request')); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-original-title="<?php echo e(__('Send Request')); ?>"
                                                title="<?php echo e(__('Send Request')); ?>">
                                                <span class="btn-inner--icon"><i class="ti ti-arrow-forward-up"></i></span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('request.cancel', \Auth::user()->id)); ?>"
                                                class="btn btn-lg btn-danger btn-icon m-1"
                                                data-title="<?php echo e(__('Cancel Request')); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-original-title="<?php echo e(__('Cancel Request')); ?>"
                                                title="<?php echo e(__('Cancel Request')); ?>">
                                                <span class="btn-inner--icon"><i class="ti ti-shield-x"></i></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(\Auth::user()->type == 'company' && \Auth::user()->trial_expire_date): ?>
                                <?php if(\Auth::user()->type == 'company' && \Auth::user()->trial_plan == $plan->id): ?>
                                    <p class="display-total-time text-dark mb-0">
                                        <?php echo e(__('Plan Trial Expired : ')); ?>

                                        <?php echo e(!empty(\Auth::user()->trial_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->trial_expire_date) : 'lifetime'); ?>

                                    </p>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if(\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id): ?>
                                    <p class="display-total-time text-dark mb-0">
                                        <?php echo e(__('Plan Expired : ')); ?>

                                        <?php echo e(!empty(\Auth::user()->plan_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) : 'lifetime'); ?>

                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).on('change', '#trial', function() {
            if ($(this).is(':checked')) {
                $('.plan_div').removeClass('d-none');
                $('#trial').attr("required", true);
                $('#trial_days').attr("required", true);

            } else {
                $('.plan_div').addClass('d-none');
                $('#trial').removeAttr("required");
                $('#trial_days').removeAttr("required");
            }
        });
    </script>

    <script>
        $(document).on("click", ".is_disable", function() {

            var id = $(this).attr('data-id');
            var is_disable = ($(this).is(':checked')) ? $(this).val() : 0;

            $.ajax({
                url: '<?php echo e(route('plan.disable')); ?>',
                type: 'POST',
                data: {
                    "is_disable": is_disable,
                    "id": id,
                    "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function(data) {
                    if (data.success) {
                        show_toastr('success', data.success);
                    } else {
                        show_toastr('error', data.error);

                    }

                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/plan/index.blade.php ENDPATH**/ ?>