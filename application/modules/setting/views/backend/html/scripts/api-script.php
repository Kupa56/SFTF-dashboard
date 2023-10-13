<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>
<script>

    $(".api_verification .enableApiBtn").on('click',function () {

        let $key = $(this).attr('data-id-key');
        let $pid_value = $(".api_verification ._"+$key).val();
        let $item_value = $(".api_verification ._item_"+$key).val();

        let selector = $(this);
        $.ajax({
            url: "<?=  site_url("ajax/setting/sverify")?>",
            data: {
                key: $key,
                pid: $pid_value,
                item: $item_value
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                selector.attr("disabled", false);
                console.log(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.default = selector;
                selector.attr("disabled", false);

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();
                } else if (data.success === 0) {

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br/>";
                    }
                    if (errorMsg !== "") {
                        NSAlertManager.simple_alert.request = errorMsg;
                    } else if (data.error) {
                        NSAlertManager.simple_alert.request = data.error;
                    }
                }
            }
        });

        return false;
    });



</script>