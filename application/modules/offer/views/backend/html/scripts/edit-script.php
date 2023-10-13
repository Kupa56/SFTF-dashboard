<!-- page script -->
<script src="<?= adminAssets("plugins/datepicker/bootstrap-datepicker.js") ?>"></script>
<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script>


    $('#button_template').select2();
    $('#cf_id').select2();

    $("#open-oml").on("click", function () {
        $("#modal-order-multi-language").modal("show");
        $(".order-button").val($("#custom-button-text").val());
    });


<?php if($offer['order_enabled'] == 1): ?>
    var order_enabled = 1;
<?php else: ?>
    var order_enabled = 0;
<?php endif; ?>


<?php if($offer['is_deal'] == 1): ?>
    $("#make_as_deal").prop("checked", true);
    $('.deal-data').removeClass("hidden");
<?php else: ?>
    $("#make_as_deal").prop("checked", false);
    $('.deal-data').addClass("hidden");
<?php endif; ?>



    if (order_enabled === 1) {
        $("#enable_order").prop("checked", true);
        $(".order-customization").removeClass("hidden");
    } else {
        $(".order-customization").addClass("hidden");
    }

    $("#enable_order").on('change', function () {

        if ($(this).is(":checked")) {
            order_enabled = 1;
        } else {
            order_enabled = 0;
        }

        if (order_enabled === 1) {
            $(".order-customization").removeClass("hidden");
        } else {
            $(".order-customization").addClass("hidden");
        }

        return false;
    });


<?php if($offer['is_deal'] == 1): ?>
    var is_deal = 1;
<?php else: ?>
    var is_deal = 0;
<?php endif; ?>


    $("#make_as_deal").on('change', function () {

        if ($(this).is(":checked")) {
            is_deal = 1;
        } else {
            is_deal = 0;
        }


        if (is_deal === 1) {
            $('.deal-data').removeClass("hidden");
        } else {
            $('.deal-data').addClass("hidden");
        }

        return false;
    });


    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });

<?php  $token = $this->mUserBrowser->setToken("SU74aQ55"); ?>

    $("#btnSave").on('click', function () {


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

        if (parseInt($("#value_type").val()) === 1) {
            percent = 0;
        } else {
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
            "offer_id": <?=$offer['id_offer']?>,
            "is_deal": is_deal,
            "order_enabled": order_enabled,
            "order_cf_id": $("#cf_id").val(),
            "button_template": $("#button_template").val(),

            "offer_coupon_config_type": $("#offer_coupon_config_type").val(),
            "offer_coupon_config_limit": $("#offer_coupon_config_limit").val(),
            "offer_coupon_code": $("#offer_coupon_code").val(),
        };


        if (order_enabled === 1) {

            let order_button = {};

            $(".order-button").each(function (index) {

                let lang = $(this).attr("lang-data");
                order_button[lang] = $(this).val();

            }).promise().done(function () {

                order_button["default"] = $("#custom-button-text").val();
                dataSet0[order_button] = order_button;
                send_data(dataSet0);

            });

        } else
            send_data(dataSet0);


        return false;

    });

    function send_data(dataSet0) {

        let selector = $("#btnSave");

        $.ajax({
            url: "<?=  site_url("ajax/offer/edit")?>",
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


    $('a.linkAccess').on('click', function () {
        var url = ($(this).attr('href'));
        var selector = $(this);
        pop(url, selector);
        //console.log("url :"+url);
    });

    function pop(url, selector) {

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled", false);
                $('#switcher').modal('hide');
                $('#modal-default').modal('hide');
            },
            success: function (data, textStatus, jqXHR) {

                $('#switcher').modal('hide');
                $('#modal-default').modal('hide');

                selector.attr("disabled", false);
                if (data.success === 1) {
                    document.location.reload()
                } else if (data.success === 0) {
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
    }


    $('#selectCurrency').select2();
    $('#selectStore').select2();
    $('#value_type').select2();

<?php if($offer['value_type'] == 'price'): ?>

    $('#value_type').val(1).trigger('change');

<?php if(isset($offer['currency']['code'])): ?>
    $('#selectCurrency').val("<?=$offer['currency']['code']?>").trigger('change');
<?php else: ?>
    $('#selectCurrency').val("<?=$offer['currency']?>").trigger('change');
<?php endif; ?>

    $(".pricing .form-price").removeClass('hidden');
    $(".pricing .form-percent").addClass('hidden');
    $("#percentInput").val('');

<?php else: ?>

    $('#value_type').val(2).trigger('change');
    $(".pricing .form-price").addClass('hidden');
    $(".pricing .form-percent").removeClass('hidden');
    $("#priceInput").val('');

<?php endif; ?>

    $('#selectStore').val(<?=$offer['store_id']?>).trigger('change');

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

            if ($('#enable_order').is(":checked")) {

                $('#enable_order').prop('checked', false);

                order_enabled = 0;

                if (order_enabled === 1) {
                    $(".order-customization").removeClass("hidden");
                } else {
                    $(".order-customization").addClass("hidden");
                }

            }

            $('#enable_order').attr('disabled', true);

        } else {

            $(".pricing .form-price").addClass('hidden');
            $(".pricing .form-percent").addClass('hidden');
            $("#priceInput").val('');
            $("#percentInput").val('');

            if ($('#enable_order').is(":checked")) {

                $('#enable_order').prop('checked', false);

                order_enabled = 0;

                if (order_enabled === 1) {
                    $(".order-customization").removeClass("hidden");
                } else {
                    $(".order-customization").addClass("hidden");
                }

            }

            $('#enable_order').attr('disabled', true);

        }

        return true;
    });


</script>


<?php if (GroupAccess::isGranted('offer', MANAGE_OFFERS)): ?>
    <script>

        $("#featured_item1").change(function () {

            var featured = 0;

            if (this.checked)
                featured = 1;
            else
                featured = 0;

            //   alert(featured);

            $.ajax({
                url: "<?=  site_url("ajax/offer/markAsFeatured")?>",
                data: {
                    "id": "<?=$offer['id_offer']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

                    } else if (data.success === 0) {
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
            return true;
        });


        $("#featured_item0").change(function () {

            var featured = 0;


            $.ajax({
                url: "<?=  site_url("ajax/offer/markAsFeatured")?>",
                data: {
                    "id": "<?=$offer['id_offer']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

                    } else if (data.success === 0) {
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
            return true;
        });

    </script>

<?php endif; ?>


