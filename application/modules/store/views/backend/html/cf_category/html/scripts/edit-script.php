<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>

<!-- page script -->
<script>

    $("#cf_id").select2();

    $("#btnEdit").on('click',function(){

        let selector = $(this);

        $.ajax({
            url:"<?=site_url("ajax/store/cf_categories_edit")?>",
            data:{
                'cat_id':<?=$category["id_category"]?>,
                'button_template':$('button_template').val(),
                "cf_id":$("#cf_id").val()
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            },error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href="<?=admin_url("store/cf_categories")?>";
                }else if(data.success===0){
                    NSTemplateUIAnimation.button.default = selector;
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        NSAlertManager.simple_alert.request = errorMsg;
                    }
                }
            }
        });

        return false;

    });


</script>
<script>
    $('.colorpicker1').colorpicker();
</script>