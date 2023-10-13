
<!-- Import Trumbowyg plugins... -->
<script src="<?=AdminTemplateManager::assets('cms','trumbowyg/trumbowyg.js')?>"></script>
<script src="<?=AdminTemplateManager::assets('cms','trumbowyg/plugins/upload/trumbowyg.upload.min.js')?>"></script>
<script src="<?=AdminTemplateManager::assets('cms','trumbowyg/plugins/resizimg/jquery-resizable.min.js')?>"></script>
<script src="<?=AdminTemplateManager::assets('cms','trumbowyg/plugins/resizimg/trumbowyg.resizimg.min.js')?>"></script>


<script>


    $(".select2").select2();

    $('#trumbowyg').trumbowyg({
        btnsDef: {
            // Create a new dropdown
            image: {
                dropdown: ['insertImage', 'upload'],
                ico: 'insertImage'
            }
        },
        btns: [
            ['viewHTML'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['image'], // Our fresh created dropdown
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        plugins: {
            upload: {
                serverPath: '<?=site_url("cms/ajax/uploadImage")?>',
                fileFieldName: 'image',
                headers: {
                    'Authorization': 'Client-ID Test'
                },
                urlPropertyName: 'data.link'
            },
            resizimg: {
                minSize: 64,
                step: 16,
            }
        }
    });


</script>

<script>


    function convertToSlug(Text)
    {
        return Text
            .toLowerCase()
            .replace(/ /g,'-')
            .replace(/[^\w-]+/g,'')
            ;
    }


    $( "#form .title" ).keyup(function() {
       let value = convertToSlug($(this).val());
        $( "#form .slug" ).val(value);
    });


    $("#btnADD").on('click',function () {
        exe_add($(this),0);
        return false;
    });

    $("#btnAddPublish").on('click',function () {
        exe_add($(this),1);
        return false;
    });

    function exe_add(selector,status) {

        $.ajax({
            url: "<?=  site_url("ajax/cms/addPage")?>",
            data: {
                "slug": $( "#form .slug" ).val(),
                "title": $( "#form .title" ).val(),
                "template": $( "#form .template" ).val(),
                "content": $('#trumbowyg').trumbowyg('html'),
                "status": status,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("cms/managePages")?>";
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


    }


</script>