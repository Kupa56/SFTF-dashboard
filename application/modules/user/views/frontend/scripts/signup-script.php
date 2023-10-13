<script>

    var lang = -1 ;
<?php
    $token = $this->mUserBrowser->setToken("S0XsOi");
    ?>

    $("#default-language").select2();
    $("#default-language").on('change', function () {

        lang = $(this).val();

    });

    $("form #signUpBtn").on('click', function () {

        var selector = $(this);

        var email = $("form #email").val();
        var password = $("form #password").val();
        var username = $("form #username").val();
        var name = $("form #name").val();
        var telephone = $("form #phone").val();
        
        var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        var recaptcha_response = $("#form #g-recaptcha-response").val();

        $.ajax({
            url: "<?=  site_url("ajax/user/signUp")?>",
            data: {
                "email": email,
                "password": password,
                "username": username,
                "recaptcha_response": recaptcha_response,
                "name": name,
                "telephone": telephone,
                "lang": lang ,
                "timezone": timezone,
            <?php if(ModulesChecker::isEnabled("pack")): ?>
                /* "pack_id": $("#select_pack").val(),*/
            <?php endif; ?>
                "token": "<?=$token?>"
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                console.log(request);

                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;

                    $(".msgSuccess").removeClass("hidden");

                    if (!data.url)
                        document.location.href = "<?=admin_url("")?>";
                    else
                        document.location.href = data.url;

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



</script>
