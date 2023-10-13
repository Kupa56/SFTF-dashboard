<script src="<?=AdminTemplateManager::assets("business_manager", "js/framework7.min.js")?>"></script>
<script src="<?=AdminTemplateManager::assets("business_manager", "js/app_obf.js")?>"></script>

<script async-component="business">

    $("#cancel").on('click',function () {
        document.location.href = $(this).attr('href');
        return false;
    });

    $("#edit").on('click',function () {
        edit_booking();
        return false;
    });

    (function () {
        // inject ourself into the window.alert and window.confirm globals
        alert = function (msg) {
            app.dialog.alert(msg,"<?=_lang("Alert!")?>");
        };

    }());

    function edit_booking() {
        var options = [];

    <?php if($reservation['status_id'] == 0): ?>

        options.push( {
            text: '<?= _lang("Confirm") ?>',
            color: '#000000',
            onClick: function (dialog, e) {
                updateOrderStatus(this,1,"");
            }
        });

        options.push( {
            text: '<?= _lang("Decline") ?>',
            color: '#000000',
            onClick: function (dialog, e) {
                updateOrderStatus(this,-1,"");
            }
        });

    <?php else: ?>

        options.push( {
            text: '<?= _lang("Confirm") ?>',
            color: '#000000',
            onClick: function (dialog, e) {
                updateOrderStatus(this,1,"");
            }
        });

        options.push( {
            text: '<?= _lang("Decline") ?>',
            color: '#000000',
            onClick: function (dialog, e) {
                updateOrderStatus(this,-1,"");
            }
        });

        options.push( {
            text: '<?= _lang("Mark as pending") ?>',
            color: '#000000',
            onClick: function (dialog, e) {
                updateOrderStatus(this,0,"");
            }
        });

    <?php  endif; ?>

        options.push( {
            text: '<?=_lang("Cancel")?>',
            color: '#000000',
            onClick: function (dialog, e) {
                dialog.close();
            }
        });


        app.dialog.create({
            title: '<?=Translate::sprintf("Edit status %s",array("#" . str_pad($reservation['id'], 6, 0, STR_PAD_LEFT)))?> ' ,
            text: '<?=_lang("Please select an option")?>',
            buttons: options,
            verticalButtons: true,

        }).open();
    }



    function updateOrderStatus(action, orderstatus, message) {

        let selector = $(action);

        $.ajax({
            url: "<?=  site_url("ajax/booking/change_booking_status")?>",
            data: {
                'booking_id': <?=$reservation['id']?>,
                'status': orderstatus,
                'message': message,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {

                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();

                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br/>";
                    }
                    if (errorMsg !== "") {
                        NSAlertManager.simple_alert.request = errorMsg;
                    }
                }
            }
        });
    }



</script>