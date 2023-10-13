<script>

<?php

    $token = $this->mUserBrowser->setToken("S69BMNSJB8JB");

    ?>

    $("#btnLogin").on('click', function () {

        let selector = $(this);

        let login = $("form #login").val();
        let password = $("form #password").val();

        $.ajax({
            url: "<?=  site_url("ajax/user/signIn")?>",
            data: {
                "login": login,
                "password": password,
                "token": "<?=$token?>"
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

                $(".msgError").addClass("hidden");
                $(".msgSuccess").addClass("hidden");

            }, error: function (request, status, error) {

                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                console.log(request);

                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;

                    $(".msgSuccess").removeClass("hidden");
                    if(data.url === undefined){
                        document.location.href = "<?=admin_url("")?>";
                    }else if(data.url !== undefined && data.url !== ""){
                        document.location.href = data.url;
                    }

                } else if (data.success === 0) {

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


    $("#default-language").select2();
    $("#default-language").on('change',function () {

        var code = $(this).val();
        var url = "<?=site_url("user/login")."?lang="?>"+code;
        document.location.href = url;

        return true;
    });


</script>
