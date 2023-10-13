<script>


    $('.mailer-block .form-group .select2').select2();

    $(".content .btnSaveMailerConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        $( ".mailer-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveMailConfig(dataSet,selector);
        } );

        return false;
    });


    $("#SMTP_SERVER_ENABLED").on('change',function () {

        let val = $(this).val();

        if(val === "true"){
            $( ".mailer-block .form-control" ).attr('disabled',false);
        }else{
            $( ".mailer-block .form-control" ).attr('disabled',true);
        }

        $(this).attr('disabled',false);

    });


    $("select#MAILER_ESP_MODULE_ENABLED").on('change',function (){

        let val = $(this).val();
        $('.mailer-block .mailer-group').addClass('hidden');
        $('.mailer-block .mailer-group-'+val).removeClass('hidden');

    });




    function saveMailConfig(dataSet,selector) {

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

