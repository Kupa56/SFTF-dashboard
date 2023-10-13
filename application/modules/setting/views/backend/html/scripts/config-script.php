
<?php

AdminTemplateManager::addScriptLibs(adminAssets("plugins/select2/select2.full.min.js"));
//AdminTemplateManager::addScriptLibs(adminAssets("plugins/colorpicker/bootstrap-colorpicker.js"));


?>
<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>
<script>


    function saveConfigData(dataSet,selector) {

        $.ajax({
            url: "<?=  site_url("ajax/setting/saveAppConfig")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                console.log(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.default = selector;

                console.log(data);

                if (data.success === 1) {
                    document.location.reload();
                } else if (data.success === 0) {
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