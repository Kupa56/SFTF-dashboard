<script src="<?=  adminAssets("plugins/datatables/jquery.dataTables.min.js")?>"></script>
<script>

    $("#_direction").val('<?=(isset($config['dir']))?$config['dir']:"ltr"?>').trigger("change");

    $('#list_languages').DataTable({
        "language": {
            "url": "<?=  adminAssets("plugins/datatables/langs/".DEFAULT_LANG.".lang")?>"
        }
    });


    var edited_fields = [];


    $( "#list_languages .lang-input" ).on('keyup',function () {

        var _key = $(this).attr('data-key');
        var _value = $(this).val();


        for (var key in edited_fields){
            if(edited_fields[key].key === _key){
                edited_fields[key].value = _value;
                return false;
            }
        }

        edited_fields.push({
            key: _key,
            value: _value,
        })

    });

    $("#save").on('click',function () {

        var selector = $(this);


        $.ajax({
            url: "<?=  site_url("ajax/nstranslator/save")?>",
            data: {
                "values": JSON.stringify(edited_fields),
                "code": "<?=$lang?>",
                "config_version": $("#_version").val(),
                "config_direction": $("#_direction").val(),
                "config_name": $("#_name").val(),
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);


                if (data.success === 1) {
                    document.location.href = "<?=admin_url("nstranslator/languages")?>";
                    NSTemplateUIAnimation.button.success = selector;
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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


    $("#add_new_key").on('click',function () {

        var selector = $(this);

        var _key = $("#_key").val();
        var _value = $("#_value").val();
        var _code = $("#_code").val();

        $.ajax({
            url: "<?=  site_url("ajax/nstranslator/add_new_key")?>",
            data: {
                "key": _key,
                "value": _value,
                "code": _code
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);
                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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
