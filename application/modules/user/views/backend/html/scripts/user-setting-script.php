<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    $('#OTP_METHOD').on('change',function (){
        $('.form-group.container1').addClass('hidden');
        $('.form-group.container1-'+$(this).val()).removeClass('hidden');
    });

    $('.DEFAULT_USER_GRPAC, .EMAIL_VERIFICATION, .USER_REGISTRATION, .DEFAULT_USER_MOBILE_GRPAC').select2();

    $('#DEFAULT_USER_GRPAC').val(<?=$config['DEFAULT_USER_GRPAC']?>).trigger('change');
    $('#DEFAULT_USER_MOBILE_GRPAC').val(<?=$config['DEFAULT_USER_MOBILE_GRPAC']?>).trigger('change');

    $(".content .btnSave").on('click', function () {

        var selector = $(this);

        var dataSet = {
            "DEFAULT_USER_GRPAC": $("#DEFAULT_USER_GRPAC").val(),
            "DEFAULT_USER_MOBILE_GRPAC": $("#DEFAULT_USER_MOBILE_GRPAC").val(),
            "EMAIL_VERIFICATION": $("#EMAIL_VERIFICATION").val(),
            "MESSAGE_WELCOME": $("#MESSAGE_WELCOME").val(),
            "USER_REGISTRATION": $("#USER_REGISTRATION").val(),

            "OTP_ENABLED": $("#OTP_ENABLED").val(),
            "OTP_METHOD": $("#OTP_METHOD").val(),

        <?php foreach ($user_subscribe_fields as $field): ?>
            "<?=$field['config_key']?>": $("#<?=$field['config_key']?>").val(),
        <?php endforeach; ?>
        };

        $.ajax({
            url: "<?=  site_url("ajax/setting/saveAppConfig")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                selector.attr("disabled", false);
                console.log(request.responseText);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);
                selector.attr("disabled", false);
                if (data.success === 1) {
                    handleSaveOtherFields();
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


    function handleSaveOtherFields(){

        let selector = $(this);
        let errors = {};
        let dataSet = {};

        $( ".otp-config-block .form-control" ).each(function( index ) {

            var $this = $(this);
            if ($this.is("textarea")) {

                let val = $this.val();

                if(val === "")
                    val = $this.text();

                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else{
                    if($this.is("[required]"))
                        errors[$this.attr('name')] = "empty field!";
                }

            }else{
                let val  = $this.val();
                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else{
                    if($this.is("[required]"))
                        errors[$this.attr('name')] = "empty field!";
                }
            }
        }).promise().done( function(){
            console.log(dataSet);
            console.log(errors);
            if (Object.keys(errors).length === 0){
                saveConfigData(dataSet,selector);
            }else{
                $('.otp-config-block .errors').removeClass("hidden");
            }
        } );
    }



    function saveConfigData(dataSet,selector) {

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

                console.log(data);

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





    //setup fields
    $( "input[data-file]").each(function( index ) {
        let $value = $(this).val();
        let $attr = $(this).attr("name");
        $(this).css('padding-right','49px');
        $(this).after('<button data-file-type="'+$(this).attr("data-file")+'" data-name="'+$attr+'" ' +
            'class="upload-file-btn" style="position:absolute;right:4px;top:29px"><i class="mdi mdi-upload"></i>' +
            '</button>').after('<input style="display:none" class="file_upload file_'+$attr+'" type="file"/>');
    }).promise().done( function(){

        let type = $(this).attr('data-file-type');

        $('.upload-file-btn').on('click',function () {
            let $selector = $(this);
            let $attr = $(this).attr("data-name");
            let $file_input = $('.file_'+$attr);
            $file_input.trigger('click');
            $file_input.fileupload({
                url: "<?=("cms/ajax/file_uploader")?>",
                sequentialUploads: true,
                limitMultiFileUploadSize: 6,
                loadImageFileTypes: new RegExp("/^application\/"+type+"$/"),
                singleFileUploads: true,
                formData: {
                    'token': "1zAqwOjs",
                    'attr': $attr,
                },
                dataType: 'json',
                done: function (e, data) {

                    NSTemplateUIAnimation.buttonWithIcon.default = $selector;

                    var results = data._response.result;
                    let attr = results.attr;
                    if(results.success === 1){
                        let url = results.result;
                        $('input[name='+attr+']').val(url);
                    }else if(results.success === 0){
                        console.log(results.errors);
                    }

                },
                fail: function (e, data) {
                    console.log(data.jqXHR);
                },
                progressall: function (e, data) {

                },
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    console.log(progress);
                },
                start: function (e) {
                    NSTemplateUIAnimation.buttonWithIcon.loading = $selector;
                }
            });



            return false;
        });


    } );


</script>



