
<script>

<?php
    $token = $this->mUserBrowser->setToken("S69BMNSJB8JB");
    ?>

    $("form #sendPasswordBtn").on('click',function(){

        let selector = $(this);
        let login = $("form #login").val();

        $.ajax({
            url:"<?=  site_url("ajax/user/forgetpassword")?>",
            data:{"login":login,"token":"<?=$token?>"},
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            },error: function (request, status, error) {

                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if(data.success===1){

                    NSTemplateUIAnimation.button.success = selector;

                    $(".msgSuccess").removeClass("hidden");

                }else if(data.success===0){

                    NSTemplateUIAnimation.button.default = selector;

                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"<br>";
                    }

                    if(errorMsg!==""){
                        $(".message-error .messages").html(errorMsg);
                        $(".message-error").removeClass("hidden");
                    }
                }
            }
        });

        return false;
    });



</script>