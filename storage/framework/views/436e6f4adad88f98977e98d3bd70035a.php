<?php $__env->startPush('scripts'); ?>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script type="text/javascript">
        <?php if(
            $plan->price > 0.0 &&
                $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                !empty($admin_payment_setting['stripe_key']) &&
                !empty($admin_payment_setting['stripe_secret'])): ?>
            var stripe = Stripe('<?php echo e($admin_payment_setting['stripe_key']); ?>');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {
                style: style
            });

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        toastrs('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        <?php endif; ?>

        $(document).ready(function() {
            $(document).on('click', '.apply-coupon', function() {

                var ele = $(this);

                var coupon = ele.closest('.row').find('.coupon').val();

                $.ajax({
                    url: '<?php echo e(route('apply.coupon')); ?>',
                    datType: 'json',
                    data: {
                        plan_id: '<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>',
                        coupon: coupon
                    },
                    success: function(data) {

                        if (ele.closest($('#payfast-form')).length == 1) {
                            get_payfast_status(data.price, coupon);
                        }

                        if (data != '') {
                            $('.final-price').text(data.final_price);
                            $('').val(coupon);

                            if (data.is_success) {
                                show_toastr('Success', data.message, 'success');
                            } else {
                                show_toastr('Error', data.message, 'error');
                            }
                        } else {
                            show_toastr('Error', "<?php echo e(__('Coupon code required.')); ?>",
                                'error');
                        }
                    }
                })
            });
        });
    </script>

    <?php if(
        !empty($admin_payment_setting['is_paystack_enabled']) &&
            isset($admin_payment_setting['is_paystack_enabled']) &&
            $admin_payment_setting['is_paystack_enabled'] == 'on'): ?>
        <script>
            $(document).on("click", ".pay_with_paystack", function() {

                $('#paystack-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var paystack_callback = "<?php echo e(url('/plan/paystack')); ?>";
                        var order_id = '<?php echo e(time()); ?>';
                        var coupon_id = res.coupon;
                        var handler = PaystackPop.setup({
                            key: '<?php echo e($admin_payment_setting['paystack_public_key']); ?>',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                window.location.href = paystack_callback + '/' + response
                                    .reference + '/' + '<?php echo e(encrypt($plan->id)); ?>' +
                                    '?coupon_id=' +
                                    coupon_id
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {
                        show_toastr('success', res.msg, 'success');

                        setInterval(function() {
                            window.location.href = "<?php echo e(route('plans.index')); ?>";
                        }, 2000);
                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    <?php endif; ?>
    <script>
        //    Flaterwave Payment
    </script>
    <?php if(
        !empty($admin_payment_setting['is_flutterwave_enabled']) &&
            isset($admin_payment_setting['is_flutterwave_enabled']) &&
            $admin_payment_setting['is_flutterwave_enabled'] == 'on'): ?>
        <script>
            $(document).on("click", "#pay_with_flaterwave", function() {

                $('#flaterwave-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;
                        var API_publicKey = '<?php echo e($admin_payment_setting['flutterwave_public_key']); ?>';
                        var nowTim = "<?php echo e(date('d-m-Y-h-i-a')); ?>";
                        var flutter_callback = "<?php echo e(url('/plan/flaterwave')); ?>";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '<?php echo e(Auth::user()->email); ?>',
                            amount: res.total_price,
                            currency: '<?php echo e($admin_payment_setting['currency']); ?>',

                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' + <?php echo e(date('Y-m-d')); ?>,
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {
                                    window.location.href = flutter_callback + '/' + txref + '/' +
                                        '<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>?coupon_id=' +
                                        coupon_id;
                                } else {
                                    // redirect to a failure page.
                                }
                                x.close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {
                        show_toastr('success', res.msg, 'success');

                        setInterval(function() {
                            window.location.href = "<?php echo e(route('plans.index')); ?>";
                        }, 2000);
                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    <?php endif; ?>
    <script>
        // Razorpay Payment
    </script>
    <?php if(
        !empty($admin_payment_setting['is_razorpay_enabled']) &&
            isset($admin_payment_setting['is_razorpay_enabled']) &&
            $admin_payment_setting['is_razorpay_enabled'] == 'on'): ?>
        <script>
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {

                        var razorPay_callback = '<?php echo e(url('/plan/razorpay')); ?>';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "<?php echo e($admin_payment_setting['razorpay_public_key']); ?>", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": '<?php echo e($admin_payment_setting['currency']); ?>',
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + response
                                    .razorpay_payment_id + '/' +
                                    '<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>?coupon_id=' +
                                    coupon_id;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {
                        show_toastr('success', res.msg, 'success');

                        setInterval(function() {
                            window.location.href = "<?php echo e(route('plans.index')); ?>";
                        }, 2000);
                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    <?php endif; ?>

    <?php if(
        $admin_payment_setting['is_payfast_enabled'] == 'on' &&
            !empty($admin_payment_setting['payfast_merchant_id']) &&
            !empty($admin_payment_setting['payfast_merchant_key'])): ?>
        <script>
            $(document).ready(function() {
                get_payfast_status(amount = 0, coupon = null);
            })

            function get_payfast_status(amount, coupon) {
                var plan_id = $('#plan_id').val();

                $.ajax({
                    url: '<?php echo e(route('payfast.payment')); ?>',
                    method: 'POST',
                    data: {
                        'plan_id': plan_id,
                        'coupon_amount': amount,
                        'coupon_code': coupon
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        if (data.success == true) {
                            $('#get-payfast-inputs').append(data.inputs);

                        } else {
                            show_toastr('Error', data.inputs, 'error')
                        }
                    }
                });
            }
        </script>
    <?php endif; ?>

<?php $__env->stopPush(); ?>


<?php
    $dir = asset(Storage::url('uploads/plan'));
    $dir_payment = asset(Storage::url('uploads/payments'));
    $admin_payment_setting = App\Models\Utility::getAdminPaymentSetting();
?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('plans.index')); ?>"><?php echo e(__('Plans')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Order Summary')); ?></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Order Summary')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
    <div class="row">

        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="sticky-top">

                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                                style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                                <div class="card-body">
                                    <span class="price-badge bg-primary"><?php echo e($plan->name); ?></span>

                                    <span
                                        class="mb-4 f-w-600 p-price"><?php echo e($admin_payment_setting['currency_symbol'] ? $admin_payment_setting['currency_symbol'] : '$'); ?><?php echo e($plan->price); ?><small
                                            class="text-sm">/
                                            <?php echo e(\App\Models\Plan::$arrDuration[$plan->duration]); ?></small></span>
                                    <p class="mb-0">
                                        <?php echo e($plan->name); ?> <?php echo e(__('Plan')); ?>

                                    </p>
                                    <p class="mb-0">
                                        <?php echo e($plan->description); ?>

                                    </p>

                                    <ul class="list-unstyled my-4">
                                        <li>
                                            <span class="theme-avtar">
                                                <i class="text-primary ti ti-circle-plus"></i></span>
                                            <?php echo e($plan->max_users < 0 ? __('Unlimited') : $plan->max_users); ?>

                                            <?php echo e(__('Users')); ?>

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
                                </div>
                            </div>

                            <div class="card">
                                <div class="list-group list-group-flush" id="useradd-sidenav">

                                    <?php if($admin_payment_setting['is_manually_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#manually-payment" role="tab" aria-controls="manually"
                                            aria-selected="true"><?php echo e(__('Manually')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($admin_payment_setting['is_banktransfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details'])): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#banktransfer-payment" role="tab" aria-controls="banktransfer"
                                            aria-selected="true"><?php echo e(__('Bank Transfer')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(
                                        $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                                            !empty($admin_payment_setting['stripe_key']) &&
                                            !empty($admin_payment_setting['stripe_secret'])): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#stripe-payment" role="tab" aria-controls="stripe"
                                            aria-selected="true"><?php echo e(__('Stripe')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(
                                        $admin_payment_setting['is_paypal_enabled'] == 'on' &&
                                            !empty($admin_payment_setting['paypal_client_id']) &&
                                            !empty($admin_payment_setting['paypal_secret_key'])): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paypal-payment" role="tab" aria-controls="paypal"
                                            aria-selected="false"><?php echo e(__('Paypal')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(
                                        $admin_payment_setting['is_paystack_enabled'] == 'on' &&
                                            !empty($admin_payment_setting['paystack_public_key']) &&
                                            !empty($admin_payment_setting['paystack_secret_key'])): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paystack-payment" role="tab" aria-controls="paystack"
                                            aria-selected="false"><?php echo e(__('Paystack')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#flutterwave-payment" role="tab" aria-controls="flutterwave"
                                            aria-selected="false"><?php echo e(__('Flutterwave')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#razorpay-payment" role="tab" aria-controls="razorpay"
                                            aria-selected="false"><?php echo e(__('Razorpay')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paytm-payment" role="tab" aria-controls="paytm"
                                            aria-selected="false"><?php echo e(__('Paytm')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#mercadopago-payment" role="tab" aria-controls="mercadopago"
                                            aria-selected="false"><?php echo e(__('Mercado Pago')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#mollie-payment" role="tab" aria-controls="mollie"
                                            aria-selected="false"><?php echo e(__('Mollie')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#skrill-payment" role="tab" aria-controls="skrill"
                                            aria-selected="false"><?php echo e(__('Skrill')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#coingate-payment" role="tab" aria-controls="coingate"
                                            aria-selected="false"><?php echo e(__('Coingate')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paymentwall-payment" role="tab" aria-controls="paymentwall"
                                            aria-selected="true"><?php echo e(__('Paymentwall')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#toyyibpay-payment" role="tab" aria-controls="toyyibpay"
                                            aria-selected="true"><?php echo e(__('Toyyibpay')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#payfast-payment" role="tab" aria-controls="payfast"
                                            aria-selected="true"><?php echo e(__('Payfast')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#iyzipay-payment" role="tab" aria-controls="iyzipay"
                                            aria-selected="true"><?php echo e(__('Iyzipay')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#sspay-payment" role="tab" aria-controls="sspay"
                                            aria-selected="true"><?php echo e(__('Sspay')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paytab-payment" role="tab" aria-controls="paytab"
                                            aria-selected="true"><?php echo e(__('Paytab')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#benefit-payment" role="tab" aria-controls="benefit"
                                            aria-selected="true"><?php echo e(__('Benefit')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#cashfree-payment" role="tab" aria-controls="cashfree"
                                            aria-selected="true"><?php echo e(__('Cashfree')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#aamarpay-payment" role="tab" aria-controls="aamarpay"
                                            aria-selected="true"><?php echo e(__('Aamarpay')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_paytr_enabled']) && $admin_payment_setting['is_paytr_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#paytr-payment" role="tab" aria-controls="paytr"
                                            aria-selected="true"><?php echo e(__('PayTR')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_yookassa_enabled']) && $admin_payment_setting['is_yookassa_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#yookassa-payment" role="tab" aria-controls="yookassa"
                                            aria-selected="true"><?php echo e(__('YooKassa')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_midtrans_enabled']) && $admin_payment_setting['is_midtrans_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#midtrans-payment" role="tab" aria-controls="midtrans"
                                            aria-selected="true"><?php echo e(__('Midtrans')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                    <?php if(isset($admin_payment_setting['is_xendit_enabled']) && $admin_payment_setting['is_xendit_enabled'] == 'on'): ?>
                                        <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                            href="#xendit-payment" role="tab" aria-controls="xendit"
                                            aria-selected="true"><?php echo e(__('Xendit')); ?>

                                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-9">

                        
                        <?php if($admin_payment_setting['is_manually_enabled'] == 'on'): ?>
                            <div class="" id="manually-payment">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5><?php echo e(__('Manually')); ?></h5>
                                            </div>
                                            <div class="border p-3 rounded stripe-payment-div">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="custom-radio">
                                                            <label
                                                                class="font-16 font-weight-bold"><?php echo e(__('Requesting Manual payment for the planned amount for the subscriptions plan.')); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer text-end">
                                                <?php if(\Auth::user()->type == 'company' && \Auth::user()->plan != $plan->id): ?>
                                                    <?php if($plan->id != 1): ?>
                                                        <?php if(\Auth::user()->requested_plan != $plan->id): ?>
                                                            <a href="<?php echo e(route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)])); ?>"
                                                                class="btn btn-primary"
                                                                data-title="<?php echo e(__('Send Request')); ?>"
                                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                                <span class="btn-inner--icon"><i
                                                                        class=""></i></span><?php echo e(__('Send Request')); ?>

                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo e(route('request.cancel', \Auth::user()->id)); ?>"
                                                                class="btn btn-danger"
                                                                data-title="<?php echo e(__('Cancel Request')); ?>"
                                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                                <span class="btn-inner--icon"><i
                                                                        class=""></i></span><?php echo e(__('Cancel Request')); ?>

                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <?php if($admin_payment_setting['is_banktransfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details'])): ?>
                            <div class="" id="banktransfer-payment">
                                <form role="form" action="<?php echo e(route('banktransfer.post')); ?>" method="post"
                                    enctype="multipart/form-data" class="require-validation" id="payment-form1">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Bank Transfer')); ?></h5>
                                                </div>
                                                <div class="border p-3 rounded banktransfer-payment-div">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="card-name-on"
                                                                    class="col-form-label text-dark"><?php echo e(__('Bank Details :')); ?></label>
                                                                <p class="text-sm">
                                                                    <?php echo isset($admin_payment_setting['bank_details']) ? $admin_payment_setting['bank_details'] : ''; ?>

                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="choose-file">
                                                                <div for="file" class="col-form-label">
                                                                    <div class="col-form-label">
                                                                        <?php echo e(__('Peyment Receipt')); ?></div>
                                                                    <input type="file" class="form-control"
                                                                        name="payment_receipt" id="file"
                                                                        data-filename="upload_file">
                                                                </div>
                                                                <p class="upload_file"></p>
                                                            </div>
                                                        </div>

                                                        <div class="banktransfer-peyment-div">
                                                            <div class="form-group">
                                                                <label
                                                                    for="banktransfer_coupon"><?php echo e(__('Coupon')); ?></label>
                                                            </div>
                                                            <div class="row align-items-center">
                                                                <div class="col-md-11">
                                                                    <div class="form-group">
                                                                        <input type="text" id="banktransfer_coupon"
                                                                            name="coupon" class="form-control coupon"
                                                                            placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group cursor-pointer">
                                                                        <a class="text-muted" data-from="banktransfer"
                                                                            data-bs-toggle="tooltip"
                                                                            title="<?php echo e(__('Apply')); ?>"><i
                                                                                class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 text-right banktransfer-coupon-tr"
                                                                    style="display: none">
                                                                    <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                        class="banktransfer-coupon-price"></b>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for=""
                                                                            class="col-form-label"><?php echo e(__('Plan Price : ')); ?><span
                                                                                class="paypal-final-price"><?php echo e($admin_payment_setting['currency_symbol'] ? $admin_payment_setting['currency_symbol'] : '$'); ?><?php echo e($plan->price); ?></span></label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for=""
                                                                            class="col-form-label"><?php echo e(__('Net Amount : ')); ?><span
                                                                                class="final-price"><?php echo e($admin_payment_setting['currency_symbol'] ? $admin_payment_setting['currency_symbol'] : '$'); ?><?php echo e($plan->price); ?></span></label><br>
                                                                        <small class="text-xs">
                                                                            <?php echo e(__('(After Coupon Apply)')); ?>

                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="error" style="display: none;">
                                                                <div class='alert-danger alert'>
                                                                    <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <input type="submit" value="<?php echo e(__('Pay Now')); ?>"
                                                        class="btn btn-primary ">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(
                            $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                                !empty($admin_payment_setting['stripe_key']) &&
                                !empty($admin_payment_setting['stripe_secret'])): ?>
                            <div class="" id="stripe-payment">
                                <form role="form" action="<?php echo e(route('stripe.post')); ?>" method="post"
                                    class="require-validation" id="payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Stripe')); ?></h5>
                                                </div>
                                                <div class="border p-3 rounded stripe-payment-div">
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="custom-radio">
                                                                <label
                                                                    class="font-16 font-weight-bold"><?php echo e(__('Credit / Debit Card')); ?></label>
                                                            </div>
                                                            <p class="mb-0 pt-1 text-sm">
                                                                <?php echo e(__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')); ?>

                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="card-name-on"
                                                                    class="col-form-label text-dark"><?php echo e(__('Name on card')); ?></label>
                                                                <input type="text" name="name" id="card-name-on"
                                                                    class="form-control required"
                                                                    placeholder="<?php echo e(\Auth::user()->name); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div id="card-element">
                                                                <!-- A Stripe Element will be inserted here. -->
                                                            </div>
                                                            <div id="card-errors" role="alert"></div>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="stripe-peyment-div">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="stripe_coupon"><?php echo e(__('Coupon')); ?></label>
                                                                </div>
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-11">
                                                                        <div class="form-group">
                                                                            <input type="text" id="stripe_coupon"
                                                                                name="coupon" class="form-control coupon"
                                                                                placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="form-group cursor-pointer">
                                                                            <a class="text-muted" data-from="stripe"
                                                                                data-bs-toggle="tooltip"
                                                                                title="<?php echo e(__('Apply')); ?>"><i
                                                                                    class="ti ti-square-check btn-apply apply-coupon cursor-pointer"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 text-right stripe-coupon-tr"
                                                                        style="display: none">
                                                                        <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                            class="stripe-coupon-price"></b>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="error" style="display: none;">
                                                                <div class='alert-danger alert'>
                                                                    <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <input type="submit" value="<?php echo e(__('Pay Now')); ?>"
                                                        class="btn btn-primary ">

                                                </div>
                                                


                                                

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(
                            $admin_payment_setting['is_paypal_enabled'] == 'on' &&
                                !empty($admin_payment_setting['paypal_client_id']) &&
                                !empty($admin_payment_setting['paypal_secret_key'])): ?>
                            <div class="" id="paypal-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.paypal')); ?>" method="post"
                                    class="require-validation" id="payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Paypal')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="paypal-payment-div">
                                                        <div class="form-group">
                                                            <label for="paypal_coupon"
                                                                class="form-control-label"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="paypal_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted apply-paypal-btn-coupon"
                                                                        data-from="paypal" data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paypal-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paypal-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" id="paypal" value="paypal"
                                                        name="payment_processor" class="custom-control-input">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        <?php endif; ?>

                        
                        <?php if(
                            $admin_payment_setting['is_paystack_enabled'] == 'on' &&
                                !empty($admin_payment_setting['paystack_public_key']) &&
                                !empty($admin_payment_setting['paystack_secret_key'])): ?>
                            <div class="" id="paystack-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.paystack')); ?>" method="post"
                                    class="require-validation" id="paystack-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Paystack')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="paystack-payment-div">
                                                        <div class="form-group">
                                                            <label for="paystack_coupon"
                                                                class="form-control-label"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">
                                                                    <input type="text" id="paystack_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="paystack"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="paystack"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paystack-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paystack-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary pay_with_paystack" type="button"
                                                        id="pay_with_paystack">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on'): ?>
                            <div class="" id="flutterwave-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.flaterwave')); ?>" method="post"
                                    class="require-validation" id="flaterwave-payment-form">
                                    <?php echo csrf_field(); ?>

                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Flutterwave')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group"><label for="flaterwave_coupon"
                                                            class="form-control-label"><?php echo e(__('Coupon')); ?></label></div>
                                                    <div class="row align-items-center">
                                                        <div class="col-md-11">
                                                            <div class="form-group">
                                                                <input type="text" id="flaterwave_coupon"
                                                                    name="coupon" class="form-control coupon"
                                                                    data-from="flaterwave"
                                                                    placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            

                                                            <div class="form-group cursor-pointer">
                                                                <a class="text-muted" data-from="flaterwave"
                                                                    data-bs-toggle="tooltip"
                                                                    title="<?php echo e(__('Apply')); ?>"><i
                                                                        class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                </a>
                                                            </div>

                                                        </div>
                                                        <div class="col-12 text-right flaterwave-coupon-tr"
                                                            style="display: none">
                                                            <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                class="flaterwave-coupon-price"></b>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="error" style="display: none;">
                                                                <div class='alert-danger alert'>
                                                                    <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="button"
                                                        id="pay_with_flaterwave">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on'): ?>
                            <div class="" id="razorpay-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.razorpay')); ?>" method="post"
                                    class="require-validation" id="razorpay-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Razorpay')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="razorpay-payment-div">
                                                        <div class="form-group">
                                                            <label for="razorpay_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">

                                                            <div class="col-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="razorpay_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="razorpay"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="razorpay"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right razorpay-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="razorpay-coupon-price"></b>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="button"
                                                        id="pay_with_razorpay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on'): ?>
                            <div class="" id="paytm-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.paytm')); ?>" method="post"
                                    class="require-validation" id="paytm-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Paytm')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="paytm-payment-div">

                                                        <div class="row align-items-center">
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <label for="paytm_coupon"
                                                                        class="form-control-label text-dark mb-4"><?php echo e(__('Mobile Number')); ?></label>
                                                                    <input type="text" id="mobile" name="mobile"
                                                                        class="form-control mobile" data-from="mobile"
                                                                        placeholder="<?php echo e(__('Enter Mobile Number')); ?>"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <label for="paytm_coupon"
                                                                        class="form-control-label text-dark mb-4"><?php echo e(__('Coupon')); ?></label>
                                                                    <input type="text" id="paytm_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="paytm"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 mt-5">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="paytm"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paytm-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paytm-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit" id="pay_with_paytm">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on'): ?>
                            <div class="" id="mercadopago-payment">

                                <form role="form" action="<?php echo e(route('plan.pay.with.mercado')); ?>" method="post"
                                    class="require-validation" id="mercado-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Mercado')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mercado-payment-div">
                                                        <div class="form-group"><label for="mercado_coupon"
                                                                class="form-control-label"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="mercado_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="mercado"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="mercado"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right mercado-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="mercado-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit" id="pay_with_mercado">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on'): ?>
                            <div class="" id="mollie-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.mollie')); ?>" method="post"
                                    class="require-validation" id="mollie-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using  Mollie')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mollie-payment-div">
                                                        <div class="form-group"><label for="mollie_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-11">
                                                                <div class="form-group">
                                                                    <input type="text" id="mollie_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="mollie"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="mollie"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right mollie-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="mollie-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit" id="pay_with_mollie">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on'): ?>
                            <div class="" id="skrill-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.skrill')); ?>" method="post"
                                    class="require-validation" id="skrill-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Skrill ')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="skrill-payment-div">
                                                        <div class="form-group"><label for="skrill_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="skrill_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="skrill"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="skrill"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right skrill-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="skrill-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <?php
                                                            $skrill_data = [
                                                                'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                                                'user_id' => 'user_id',
                                                                'amount' => 'amount',
                                                                'currency' => 'currency',
                                                            ];
                                                            session()->put('skrill_data', $skrill_data);
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit" id="pay_with_skrill">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on'): ?>
                            <div class="" id="coingate-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.coingate')); ?>" method="post"
                                    class="require-validation" id="coingate-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="coingate-payment-div">
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12 col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5><?php echo e(__('Pay Using Coingate')); ?></h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label for="coingate_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="coingate_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="coingate"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="coingate"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right coingate-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="coingate-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="card-footer text-end">
                                                        <input type="hidden" name="plan_id"
                                                            value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                        <button class="btn btn-primary" type="submit"
                                                            id="pay_with_coingate">
                                                            <i class="mdi mdi-cash-multiple mr-1"></i>
                                                            <?php echo e(__('Pay Now')); ?>

                                                            
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on'): ?>
                            <div class="" id="paymentwall-payment">
                                <form role="form" action="<?php echo e(route('paymentwall')); ?>" method="post"
                                    class="require-validation" id="paymentwall-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Paymentwall')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 paymentwall-payment-div">
                                                        <div class="form-group"><label for="payementwall_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="paymentwall_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="paymentwall"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="paymentwall"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paymentwall-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paymentwall-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paymentwall">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on'): ?>
                            <div class="" id="toyyibpay-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.toyyibpay')); ?>" method="post"
                                    class="require-validation" id="toyyibpay-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Toyyibpay')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 toyyibpay-payment-div">
                                                        <div class="form-group"><label for="toyyibpay_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="toyyibpay_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="toyyibpay"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                

                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="toyyibpay"
                                                                        data-from="paymentwall" data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paymentwall-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="toyyibpay-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_toyyibpay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                        
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on'): ?>
                            <div id="payfast-payment" class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('Pay Using Payfast')); ?></h5>
                                </div>

                                
                                <?php if(
                                    $admin_payment_setting['is_payfast_enabled'] == 'on' &&
                                        !empty($admin_payment_setting['payfast_merchant_id']) &&
                                        !empty($admin_payment_setting['payfast_merchant_key']) &&
                                        !empty($admin_payment_setting['payfast_signature']) &&
                                        !empty($admin_payment_setting['payfast_mode'])): ?>
                                    <div
                                        class="tab-pane <?php echo e(($admin_payment_setting['is_payfast_enabled'] == 'on' && !empty($admin_payment_setting['payfast_merchant_id']) && !empty($admin_payment_setting['payfast_merchant_key'])) == 'on' ? 'active' : ''); ?>">
                                        <?php
                                            $pfHost = $admin_payment_setting['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                                        ?>
                                        <form role="form" action=<?php echo e('https://' . $pfHost . '/eng/process'); ?>

                                            method="post" class="require-validation" id="payfast-form">
                                            <div class="card-body">
                                                <div class="py-3 payfast-peyment-div">
                                                    <div class="form-group">
                                                        <label for="payfast_coupon"
                                                            class="form-label"><?php echo e(__('Coupon')); ?></label>
                                                    </div>
                                                    <div class="row align-items-center">
                                                        <div class="col-md-11">
                                                            <div class="form-group">
                                                                <input type="text" id="payfast_coupon" name="coupon"
                                                                    class="form-control coupon"
                                                                    placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group cursor-pointer">
                                                                <a class="text-muted " data-bs-toggle="tooltip"
                                                                    title="<?php echo e(__('Apply')); ?>"><i
                                                                        class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div id="get-payfast-inputs"></div>
                                            
                                            <div class="card-footer text-end">
                                                <input type="hidden" name="plan_id" id="plan_id"
                                                    value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                <input type="submit" value="<?php echo e(__('Pay Now')); ?>"
                                                    id="payfast-get-status" class="btn btn-primary">

                                            </div>
                                            
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on'): ?>
                            <div class="" id="iyzipay-payment">
                                <form role="form" action="<?php echo e(route('iyzipay.payment.init')); ?>" method="post"
                                    class="require-validation" id="iyzipay-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Iyzipay')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 iyzipay-payment-div">
                                                        <div class="form-group"><label for="iyzipay_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="iyzipay_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="iyzipay"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="iyzipay"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right iyzipay-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="iyzipay-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paymentwall">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on'): ?>
                            <div class="" id="sspay-payment">
                                <form role="form" action="<?php echo e(route('plan.sspaypayment')); ?>" method="post"
                                    class="require-validation" id="sspay-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Sspay')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 sspay-payment-div">
                                                        <div class="form-group"><label for="sspay_coupon"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="sspay_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="sspay"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="sspay"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right sspay-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="sspay-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_sspay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on'): ?>
                            <div class="" id="paytab-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.paytab')); ?>" method="post"
                                    class="require-validation" id="paytab-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using PayTab')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 paytab-payment-div">
                                                        <div class="form-group"><label for="paytab"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="paytab_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="paytab"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="paytab"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paytab-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paytab-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paytab">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on'): ?>
                            <div class="" id="benefit-payment">
                                <form role="form" action="<?php echo e(route('benefit.initiate')); ?>" method="post"
                                    class="require-validation" id="benefit-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Benefit')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 benefit-payment-div">
                                                        <div class="form-group"><label for="benefit"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="benefit_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="benefit"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="benefit"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right benefit-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="benefit-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_benefit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on'): ?>
                            <div class="" id="cashfree-payment">
                                <form role="form" action="<?php echo e(route('cashfree.payment')); ?>" method="post"
                                    class="require-validation" id="cashfree-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Cashfree')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 cashfree-payment-div">
                                                        <div class="form-group"><label for="cashfree"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="cashfree_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="cashfree"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="cashfree"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right cashfree-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="cashfree-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_cashfree">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on'): ?>
                            <div class="" id="aamarpay-payment">
                                <form role="form" action="<?php echo e(route('pay.aamarpay.payment')); ?>" method="post"
                                    class="require-validation" id="aamarpay-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Aamarpay')); ?></h5>
                                                </div>
                                                <div class="card-body">

                                                    <div class="py-3 aamarpay-payment-div">
                                                        <div class="form-group"><label for="aamarpay"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="aamarpay_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="aamarpay"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="aamarpay"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right aamarpay-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="aamarpay-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_aamarpay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_paytr_enabled']) && $admin_payment_setting['is_paytr_enabled'] == 'on'): ?>
                            <div class="" id="paytr-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.paytr', [$plan->id])); ?>"
                                    method="post" class="require-validation" id="paytr-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using PayTR')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="py-3 paytr-payment-div">
                                                        <div class="form-group"><label for="paytr"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="paytr_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="paytr"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="aamarpay"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right paytr-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="paytr-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_paytr">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_yookassa_enabled']) && $admin_payment_setting['is_yookassa_enabled'] == 'on'): ?>
                            <div class="" id="yookassa-payment">
                                <form role="form" action="<?php echo e(route('plan.pay.with.yookassa')); ?>" method="post"
                                    class="require-validation" id="yookassa-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using YooKassa')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="py-3 yookassa-payment-div">
                                                        <div class="form-group"><label for="yookassa"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="yookassa_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="yookassa"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="yookassa"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right yookassa-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="yookassa-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_yookassa">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_midtrans_enabled']) && $admin_payment_setting['is_midtrans_enabled'] == 'on'): ?>
                            <div class="" id="midtrans-payment">
                                <form role="form" action="<?php echo e(route('plan.get.midtrans')); ?>" method="post"
                                    class="require-validation" id="midtrans-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Midtrans')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="py-3 midtrans-payment-div">
                                                        <div class="form-group"><label for="midtrans"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="midtrans_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="midtrans"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="midtrans"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right midtrans-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="midtrans-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_midtrans">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(isset($admin_payment_setting['is_xendit_enabled']) && $admin_payment_setting['is_xendit_enabled'] == 'on'): ?>
                            <div class="" id="xendit-payment">
                                <form role="form" action="<?php echo e(route('plan.xendit.payment')); ?>" method="post"
                                    class="require-validation" id="xendit-payment-form">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5><?php echo e(__('Pay Using Xendit')); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="py-3 xendit-payment-div">
                                                        <div class="form-group"><label for="xendit"
                                                                class="form-control-label text-dark"><?php echo e(__('Coupon')); ?></label>
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-md-11">
                                                                <div class="form-group">

                                                                    <input type="text" id="xendit_coupon"
                                                                        name="coupon" class="form-control coupon"
                                                                        data-from="xendit"
                                                                        placeholder="<?php echo e(__('Enter Coupon Code')); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group cursor-pointer">
                                                                    <a class="text-muted" data-from="xendit"
                                                                        data-bs-toggle="tooltip"
                                                                        title="<?php echo e(__('Apply')); ?>"><i
                                                                            class="ti ti-square-check btn-apply apply-coupon"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <div class="col-12 text-right xendit-coupon-tr"
                                                                style="display: none">
                                                                <b><?php echo e(__('Coupon Discount')); ?></b> : <b
                                                                    class="xendit-coupon-price"></b>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="error" style="display: none;">
                                                                    <div class='alert-danger alert'>
                                                                        <?php echo e(__('Please correct the errors and try again.')); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer text-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="<?php echo e(\Illuminate\Support\Facades\Crypt::encrypt($plan->id)); ?>">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="pay_with_xendit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> <?php echo e(__('Pay Now')); ?>

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/hrmagix.com/httpdocs/in/app/resources/views/stripe.blade.php ENDPATH**/ ?>