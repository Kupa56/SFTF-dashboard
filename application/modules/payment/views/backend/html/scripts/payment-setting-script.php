<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    let enabled_payments = [];


    $( ".payment_methods .payment_method").on('click',function () {

        enabled_payments = [];

        $( ".payment_methods .payment_method").each(function( index ) {

            if($(this).is(":checked")){
                enabled_payments.push($(this).val());
            }

        }).promise().done( function(){
            enabled_payments.push(0);
            console.log(enabled_payments);
        } );

    });



    $('.PAYMENT_CURRENCY').select2();
    $('.PAYPAL_CONFIG_DEV_MODE').select2();
    $('.STRIPE_CONFIG_DEV_MODE').select2();
    $('.RAZORPAY_CONFIG_DEV_MODE').select2();

    $(".content #btnSave").on('click', function () {

        var selector = $(this);

        var dataSet = {

            "METHOD_PAYMENTS_ENABLED_LIST": enabled_payments,
            "PAYPAL_CONFIG_CLIENT_ID": $("#PAYPAL_CONFIG_CLIENT_ID").val(),
            "PAYPAL_CONFIG_SECRET_ID": $("#PAYPAL_CONFIG_SECRET_ID").val(),
            "PAYPAL_CONFIG_DEV_MODE": $("#PAYPAL_CONFIG_DEV_MODE").val(),

            "STRIPE_PUBLISHABLE_KEY": $("#STRIPE_PUBLISHABLE_KEY").val(),
            "STRIPE_SECRET_KEY": $("#STRIPE_SECRET_KEY").val(),
            "STRIPE_CONFIG_DEV_MODE": $("#STRIPE_CONFIG_DEV_MODE").val(),

            "RAZORPAY_CONFIG_DEV_MODE": $("#RAZORPAY_CONFIG_DEV_MODE").val(),
            "RAZORPAY_KEY_ID": $("#RAZORPAY_KEY_ID").val(),
            "RAZORPAY_SECRET_KEY": $("#RAZORPAY_SECRET_KEY").val(),

            "FLUTTERWAVE_CONFIG_DEV_MODE": $("#FLUTTERWAVE_CONFIG_DEV_MODE").val(),
            "FLUTTERWAVE_KEY_ID": $("#FLUTTERWAVE_KEY_ID").val(),
            "FLUTTERWAVE_SECRET_KEY": $("#FLUTTERWAVE_SECRET_KEY").val(),

            "HYPERPAY_CONFIG_DEV_MODE": $("#HYPERPAY_CONFIG_DEV_MODE").val(),
            "HYPERPAY_KEY_ID": $("#HYPERPAY_KEY_ID").val(),
            "HYPERPAY_SECRET_KEY": $("#HYPERPAY_SECRET_KEY").val(),


            "PAYTM_CONFIG_DEV_MODE": $("#PAYTM_CONFIG_DEV_MODE").val(),
            "PAYTM_KEY_ID": $("#PAYTM_KEY_ID").val(),
            "PAYTM_SECRET_KEY": $("#PAYTM_SECRET_KEY").val(),

            "PAYSTACK_CONFIG_DEV_MODE": $("#PAYSTACK_CONFIG_DEV_MODE").val(),
            "PAYSTACK_KEY_ID": $("#PAYSTACK_KEY_ID").val(),
            "PAYSTACK_SECRET_KEY": $("#PAYSTACK_SECRET_KEY").val(),

            "MY_COOLPAY_CONFIG_DEV_MODE": $("#MY_COOLPAY_CONFIG_DEV_MODE").val(),
            "MY_COOLPAY_KEY_ID": $("#MY_COOLPAY_KEY_ID").val(),
            "MY_COOLPAY_SECRET_KEY": $("#MY_COOLPAY_SECRET_KEY").val(),

            "MERCADO_PAGO_CONFIG_DEV_MODE": $("#MERCADO_PAGO_CONFIG_DEV_MODE").val(),
            "MERCADO_PAGO_KEY_ID": $("#MERCADO_PAGO_KEY_ID").val(),
            "MERCADO_PAGO_ACCESS_TOKEN": $("#MERCADO_PAGO_ACCESS_TOKEN").val(),
            "MERCADO_PAGO_CLIENT_ID": $("#MERCADO_PAGO_CLIENT_ID").val(),
            "MERCADO_PAGO_CLIENT_SECRET": $("#MERCADO_PAGO_CLIENT_SECRET").val(),

            "TRANSFER_BANK_NAME": $("#TRANSFER_BANK_NAME").val(),
            "TRANSFER_BANK_SWIFT": $("#TRANSFER_BANK_SWIFT").val(),
            "TRANSFER_BANK_IBAN": $("#TRANSFER_BANK_IBAN").val(),
            "TRANSFER_BANK_DETAILS": $("#TRANSFER_BANK_DETAILS").val(),

            "WALLET_TOP_UP_AMOUNTS": $("#WALLET_TOP_UP_AMOUNTS").val(),

            "PAYMENT_CURRENCY": $("#PAYMENT_CURRENCY").val(),
            "token": ""
        };

        $.ajax({
            url: "<?=  site_url("ajax/setting/saveAppConfig")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            }, error: function (request, status, error) {
                alert(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;

                    document.location.reload();
                } else if (data.success === 0) {

                    NSTemplateUIAnimation.button.default = selector;

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "\n";
                    }
                    if (errorMsg !== "") {
                        alert(errorMsg);
                    }
                }
            }
        });


        return false;
    });




</script>
