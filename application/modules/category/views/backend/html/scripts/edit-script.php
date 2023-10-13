<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>

<!-- page script -->
<script>

    $("#cf_id").select2();

    $("#btnEdit").on('click',function(){

        let selector = $(this);
        var cat = $("#addCat").val();

        $.ajax({
            url:"<?=site_url("ajax/category/editCategory")?>",
            data:{
                'cat':cat,
                'id':"<?=$category['id_category']?>",
                "image":<?=$uploader_variable?>,
                "icon":<?=$uploader_variable2?>,
                "color":$("#color").val()
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            },error: function (request, status, error) {
                alert(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href="<?=admin_url("category/categories")?>";
                }else if(data.success===0){
                    NSTemplateUIAnimation.button.default = selector;
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
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