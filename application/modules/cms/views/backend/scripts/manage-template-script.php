<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>
<script src="<?= adminAssets("plugins/uploader/js/jquery.iframe-transport.js") ?>"></script>
<script src="<?= adminAssets("plugins/uploader/js/jquery.ui.widget.js") ?>"></script>
<script src="<?= adminAssets("plugins/uploader/js/jquery.fileupload.js") ?>"></script>
<script src="<?= adminAssets("plugins/jQueryUI/jquery-ui.js") ?>"></script>
<script>

    //Set active template
    active('Template');

    //Set up uploader fields
    setup_files_uploader();

    $(".sub-navigation li a").on('click',function () {
        let href = $(this).attr('href');
        var fixed = href.replace(/#+/g, '');
        active(fixed);
        return false;
    });

    $('.template-selector li').on("click",function () {
        let templateId = $(this).attr("data-id");
        $("#DEFAULT_TEMPLATE").val(templateId);
        $("#FRONTEND_TEMPLATE_NAME").val(templateId);
        $('.template-selector li').removeClass("active");
        $(this).addClass("active");
    });

    $('.webapp-template-block .form-group .select2').select2();
    $('.webapp-template-block .form-group .colorpicker1').colorpicker();
    $("#btnSaveWebappConfig").on('click', function () {
        let selector = $(this);
        let errors = {};
        let dataSet = {};
        $( ".webapp-template-block .form-control, .webapp-template-block .form-check:checked" ).each(function( index ) {
            var $this = $(this);

            if ($this.is("textarea")) {
                let val  = $this.val();
                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else  if(val === ""){
                    dataSet[$this.attr('name')] =  "";
                }else{
                    if($this.is("[required]"))
                        errors[$this.attr('name')] = "empty field!";
                }
            }else{
                let val  = $this.val();
                if(val !== ""){
                    dataSet[$this.attr('name')] =  val;
                }else  if(val === ""){
                    dataSet[$this.attr('name')] =  "";
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
                $('.webapp-template-block .errors').removeClass("hidden");
            }
        } );
        return false;
    });

    function active(tab) {
        $('.sub-navigation-body').addClass("hidden");
        $('.sub-navigation-body#'+tab).removeClass("hidden");
        $('.sub-navigation li').removeClass("active");
        $('.sub-navigation li a[href=#'+tab+']').parent().addClass("active");
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

    function checkURL(url) {
        return(url.match(/\.(jpeg|jpg|gif|png|ico)$/) != null);
    }

    function setup_files_uploader(){

        $('.webapp-template-block .form-group').css("position","relative");

        //setup fields
        $( ".webapp-template-block input[type=text]").each(function( index ) {
            let $value = $(this).val();
            let $attr = $(this).attr("name");
            if(checkURL($value)){
                $(this).css('padding-right','49px');
                $(this).after('<button data-name="'+$attr+'" ' +
                    'class="upload-file-btn" style="position:absolute;right:4px;top:29px"><i class="mdi mdi-upload"></i>' +
                    '</button>').after('<input style="display:none" class="file_upload file_'+$attr+'" type="file"/>');

            }
        }).promise().done( function(){

            $('.upload-file-btn').on('click',function () {
                let $selector = $(this);
                let $attr = $(this).attr("data-name");
                let $file_input = $('.file_'+$attr);
                $file_input.trigger('click');
                $file_input.fileupload({
                    url: "<?=("cms/ajax/file_uploader")?>",
                    sequentialUploads: true,
                    limitMultiFileUploadSize: 6,
                    loadImageFileTypes: /^image\/(gif|jpeg|png|jpg|ico)$/,
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


    }




</script>