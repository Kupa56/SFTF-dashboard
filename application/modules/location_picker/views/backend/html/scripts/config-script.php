<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>
<script>

    $('#op_maps_picker').select2();
    $('#op_maps_picker').on('change',function () {

        let id = parseInt($(this).val());

        if(id === 1 ){

            $('._config .maps-api').addClass('hidden');
            $('._config .here-maps-form').removeClass('hidden');

        }else if(id === 2 ){

            $('._config .maps-api').addClass('hidden');
            $('._config .google-maps-form').removeClass('hidden');
        }

    });

    $("._config .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/location_picker/saveConfig")?>",
            dataType: 'json',
            data:{
                'LOCATION_PICKER_HERE_MAPS_APP_ID': $('._config #LOCATION_PICKER_HERE_MAPS_APP_ID').val(),
                'LOCATION_PICKER_HERE_MAPS_APP_CODE': $('._config #LOCATION_PICKER_HERE_MAPS_APP_CODE').val(),
                'LOCATION_PICKER_OP_PICKER': $('._config #op_maps_picker').val(),
                'MAPS_API_KEY': $('._config #MAPS_API_KEY').val(),
                'GOOGLE_PLACES_API_KEY': $('._config #GOOGLE_PLACES_API_KEY').val(),
            },
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
               console.log(request);

                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.success = selector;

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
    });


</script>