<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script src="<?=  adminAssets("plugins/iCheck/icheck.min.js")?>"></script>
<script>

    //iCheck
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });


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

</script>

<?php if(ModulesChecker::isEnabled("pack")): ?>
    <script>

        $('#typeAuth').select2();
        $('#confirm_pack').select2();
        $('#select_pack').select2();



        var pack_id = 0;
        var pack_duration = 0;

        $('#select_pack').on('select2:select', function (e) {

            var data = e.params.data;

            $('#modal-default-pack').modal('show');

            $("#_select").on('click', function () {

                pack_id = data.id;
                pack_duration = $("#confirm_pack").val();

                $('#modal-default-pack').modal('hide');

                return false;
            });
            return true;

        });


        function refreshPackSubscription(selector,user_id) {

            $.ajax({
                type: 'post',
                url: "<?=site_url("pack/ajax/changeOwnerPack")?>",
                data: {
                    'pack_id': pack_id,
                    'pack_duration': pack_duration,
                    'user_id': user_id
                },
                dataType: 'json',
                beforeSend: function (xhr) {
                    selector.attr("disabled", true);
                }, error: function (request, status, error) {
                    NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                    selector.attr("disabled", false);
                    $('#modal-default-pack').modal('hide');

                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    console.log(data);
                    $('#modal-default-pack').modal('hide');
                    selector.attr("disabled", false);
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


        }


    </script>
<?php endif; ?>


<script>


    $('#typeAuth').select2();
    $("#btnCreate").on('click', function () {

        var selector = $(this);
        var name = $("#form #name").val();
        var username = $("#form #username").val();
        var password = $("#form #password").val();
        var confirm = $("#form #confirm").val();
        var email = $("#form #email").val();
        var phone = $("#form #phone").val();

        var typeAuth = $("#form2 #typeAuth").val();

        var dataSet = {
            "name": name,
            "username": username,
            "password": password,
            "phone": phone,
            "email": email,
            "typeAuth": typeAuth,
            "confirm": confirm,
            "image": <?=$uploader_variable?>,
        };


        var user_settings = {};
    <?php if(!ModulesChecker::isEnabled("pack")): ?>
        <?php foreach ($user_subscribe_fields as $field): ?>
            <?php if($field['_display'] == 1): ?>
                    user_settings.<?=$field['field_name']?> = $("#<?=$field['config_key']?>").val();
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

        if(customize_subscription)
            dataSet.user_settings =  user_settings;


        $.ajax({
            url: "<?=  site_url("ajax/user/create")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                selector.attr("disabled", true);

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {

                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                NSTemplateUIAnimation.button.default = selector;

                console.log(request.responseText);

            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.default = selector;

                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;

                <?php if(ModulesChecker::isEnabled("pack")): ?>

                        if(pack_id > 0 && pack_duration > 0){
                            refreshPackSubscription(selector,data.result.id_user);
                        }else{
                            document.location.href = "<?=admin_url('user/users')?>";
                        }

                <?php else: ?>
                        document.location.href = "<?=admin_url('user/users')?>";
                <?php endif; ?>

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
