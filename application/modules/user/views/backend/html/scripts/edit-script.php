<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script src="<?=  adminAssets("plugins/iCheck/icheck.min.js")?>"></script>
<script>

    //iCheck
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });


</script>


<script>


<?php

    $token = $this->mUserBrowser->setToken("S0XsNOi");

    ?>



    var customize_subscription = false;

    $('#customize_subscription').on('ifChecked', function(event){

        $('.form-group.customize_subscription input').attr('disabled',false);
        $('.form-group.customize_subscription select').attr('disabled',false);
        customize_subscription = true;

    });


    $('#customize_subscription').on('ifUnchecked', function(event){

        $('.form-group.customize_subscription input').attr('disabled',true);
        $('.form-group.customize_subscription select').attr('disabled',true);
        customize_subscription = false;

    });

    $("#form2 #typeAuth").val(<?=$user->grp_access_id?>).trigger('change');

    $(".profle .btnSave").on('click', function () {

        var selector = $(this);

        var password = $("#form1 #password").val();
        var confirm = $("#form1 #confirm").val();
        var name = $("#form1 #name").val();
        var username = $("#form1 #username").val();
        var email = $("#form1 #email").val();
        var phone = $("#form1 #phone").val();



        var typeAuth = $("#form2 #typeAuth").val();

        var dataSet = {
            "id": "<?=$user->id_user?>"/*,"old":old*/,
            "name": name,
            "password": password,
            "username": username,
            "email": email,
            "phone": phone,
            "typeAuth": typeAuth,
            "confirm": confirm,
            "image": <?=$uploader_variable?>,
            "token": "<?=$token?>"
        };

        var user_settings = {};
    <?php if(!ModulesChecker::isEnabled("pack")): ?>
    <?php foreach ($user_settings as $field): ?>
    <?php if($field['_display'] == 1): ?>
        user_settings.<?=$field['field_name']?> = $("#<?=$field['config_key']?>").val();
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

        if(customize_subscription)
            dataSet.user_settings =  user_settings;


        $.ajax({
            url: "<?=  site_url("ajax/user/edit")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                selector.attr("disabled", false);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                selector.attr("disabled", false);
                console.log(data);
                if (data.success === 1) {

                    document.location.href = "<?=admin_url("user/users")?>";

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

        return false;

    });

    $('.form-group .form-control.select2').select2();



</script>

