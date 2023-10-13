<script src="<?= adminAssets("plugins/datepicker/bootstrap-datepicker.js") ?>"></script>
<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>


    $("#button_template").select2();

    $("#open-oml").on("click",function () {
        $("#modal-order-multi-language").modal("show");
        $(".order-button").val($("#custom-button-text").val());
    });

    var order_enabled = 0;

    $("#enable_order").on('change',function () {

        if($(this).is(":checked")){
            order_enabled = 1;
        }else {
            order_enabled = 0;
        }

        if(order_enabled===1){
            $(".order-customization").removeClass("hidden");
        }else {
            $(".order-customization").addClass("hidden");
        }

        return false;
    });


    var is_deal = 0;

    $("#make_as_deal").on('change',function () {

        if($(this).is(":checked")){
            is_deal = 1;
        }else {
            is_deal = 0;
        }


        if(is_deal===1){
            $('.deal-data').removeClass("hidden");
        }else {
            $('.deal-data').addClass("hidden");
        }

        return false;
    });

    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });


<?php
    $token = $this->mUserBrowser->setToken("SU74aQ55");
    ?>

    $("#btnCreate").on('click', function () {

        if( parseInt($("#value_type").val()) === 1 &&  parseInt($("#priceInput").val()) < 0){
            NSAlertManager.simple_alert.request = "<?=Translate::sprint("Price value should be greater than zero")?>";
            return ;
        }

        var selector = $(this);
        var description = $("#form #editable-textarea").val();
        var price = parseFloat($("#form #priceInput").val());
        var percent = parseFloat($("#form #percentInput").val());
        var date_b = $("#form #date_b").val();
        var date_e = $("#form #date_e").val();
        var name = $("#form #name").val();
        var store_id = $("#form #selectStore").val();


        if( parseInt($("#value_type").val()) === 1){
            percent = 0;
        }else{
            price = 0;
        }

        var dataSet0 = {
            "token": "<?=$token?>",
            "store_id": store_id,
            "images": <?=$uploader_variable?>,
            "name": name,
            "description": description,
            "price": price,
            "percent": percent,
            "date_start": date_b,
            "date_end": date_e,
            "is_deal": is_deal,
            "order_enabled": order_enabled,
            "order_cf_id": $("#cf_id").val(),
            "button_template": $("#button_template").val(),
            "offer_coupon_config_type": $("#offer_coupon_config_type").val(),
            "offer_coupon_config_limit": $("#offer_coupon_config_limit").val(),
            "offer_coupon_code": $("#offer_coupon_code").val(),
        };


        if(order_enabled===1){

            let order_button = {};

            $( ".order-button" ).each(function( index ) {

                let lang = $(this).attr("lang-data");
                order_button[lang] = $(this).val();

            }).promise().done(function () {

                order_button["default"] = $("#custom-button-text").val();
                dataSet0[order_button] = order_button;
                send_data(dataSet0);

            });

        }else
            send_data(dataSet0);


        return false;

    });


    function send_data(dataSet0) {

        let selector = $("#btnCreate");

        $.ajax({
            url: "<?=  site_url("ajax/offer/add")?>",
            data: dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);

                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("offer/my_offers")?>";
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

    }


</script>
<script>

    $('#selectCurrency').select2();
    $('#selectStore').select2();
    $('#value_type').select2();
    $('#cf_id').select2();

    $('#value_type').on('change', function () {

        var value = parseInt($(this).val());

        if (value === 1) {

            $(".pricing .form-price").removeClass('hidden');
            $(".pricing .form-percent").addClass('hidden');
            $("#percentInput").val('');

            $('#enable_order').attr('disabled', false);

        } else if (value === 2) {
            $(".pricing .form-price").addClass('hidden');
            $(".pricing .form-percent").removeClass('hidden');
            $("#priceInput").val('');

            if($('#enable_order').is(":checked")){

                $('#enable_order').prop('checked', false);

                order_enabled = 0;

                if(order_enabled===1){
                    $(".order-customization").removeClass("hidden");
                }else {
                    $(".order-customization").addClass("hidden");
                }

            }

            $('#enable_order').attr('disabled', true);

        } else {

            $(".pricing .form-price").addClass('hidden');
            $(".pricing .form-percent").addClass('hidden');
            $("#priceInput").val('');
            $("#percentInput").val('');

            if($('#enable_order').is(":checked")){

                $('#enable_order').prop('checked', false);

                order_enabled = 0;

                if(order_enabled===1){
                    $(".order-customization").removeClass("hidden");
                }else {
                    $(".order-customization").addClass("hidden");
                }

            }

            $('#enable_order').attr('disabled', true);

        }

        return true;
    });



    //    $("#tags").select2({
    //        tags: true,
    //        placeholder: "<?//=Translate::sprint("Add tags")?>//",
    //        tokenSeparators: [',', ' ']
    //    })

</script>


