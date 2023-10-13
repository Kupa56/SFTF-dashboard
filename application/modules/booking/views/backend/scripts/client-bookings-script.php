<script>


    $('.list_general .cancel').on('click',function () {

        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/booking/cancelBookingClient")?>",
            data: {
                'booking_id': parseInt(selector.attr('data-id')),
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


        return false;
    });



    $('.list_general .send-message').on('click',function () {

        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/booking/sendMessageBookingClient")?>",
            data: {
                'booking_id': parseInt(selector.attr('data-id')),
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
                    document.location.href = data.result;

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


        return false;
    });


</script>