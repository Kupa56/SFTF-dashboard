<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>
<script>

    $('#OPENING_TIME_ENABLED').select2();

    $("._config .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/store/saveConfig")?>",
            dataType: 'json',
            data:{
                'OPENING_TIME_ENABLED':  $('#OPENING_TIME_ENABLED').val(),
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