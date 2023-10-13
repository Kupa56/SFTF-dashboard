
<script>

<?php

    $stoken = RequestInput::get("recover");
    if (!Text::tokenIsValid($stoken)) {
        $stoken = "";
    }

    $token = $this->mUserBrowser->setToken("S69BMNSJB8JB");

    ?>

    $("form #savePasswordBtn").on('click',function(){


        let selector = $(this);

        var password = $("form #password").val();
        var confirm = $("form #confirm").val();

        $.ajax({
            url:"<?=  site_url("ajax/user/resetpassword")?>",
            data:{
                "password": password,
                "confirm": confirm,
                "token": "<?=$token?>",
                "stoken": "<?=$stoken?>"
            },
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

                    $(".message-error").addClass("hidden");
                    $(".message-success").removeClass("hidden");

                    document.location.href = "<?=site_url("user/login")?>";

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