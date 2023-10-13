<script>

    $('.uploader-block .form-group .select2').select2();
    $('.uploader-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveStoreConfig").on('click', function () {

        var selector = $(this);
        let dataSet = {};

        $( ".uploader-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();


        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });


    function checkURL0(url) {
        return(url.match(/\.(jpeg|jpg|gif|png|ico)$/) != null);
    }

    $('.uploader-block .form-group').css("position","relative");

    //setup fields
    $( ".uploader-block input[data-file]").each(function( index ) {
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

