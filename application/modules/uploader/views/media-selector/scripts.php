<script>

    $("body").delegate('.item', 'click', function(event) {
        let id = $(this).attr('data-id');
        let s100 = $(".item-"+id+" .100_100").val();
        let s200 = $(".item-"+id+" .200_200").val();
        let s500 = $(".item-"+id+" .500_500").val();
        let sfull = $(".item-"+id+" .full").val();

        $('.preview').removeClass('hidden');

        $('.preview img').attr("src",sfull);
        $('.preview .s100_100').val(s100);
        $('.preview .s200_200').val(s200);
        $('.preview .s500_500').val(s500);
        $('.preview .sfull').val(sfull);

    });

    var NSTemplateUIAnimation0 = {


        button: {

            set loading(selector){
                var text  = selector.text().trim();
                selector.attr("disabled",true);
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.html("<i class=\"btn-saving-cart fa fa-check\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
                selector.addClass('bg-green');
                selector.attr("disabled",true);
            },
            set default(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.attr("disabled",false);
            },

            // selector.html('<i class="btn-saving-cart fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Mail Sent")?>&nbsp;&nbsp;');
        },

        buttonWithIcon: {

            set loading(selector){

                window.NSTemplateUIAnimationContent  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;<?=_lang("Loading")?>");

            },

            set success(selector) {

                selector.html( window.NSTemplateUIAnimationContent);
                selector.attr('disabled',true);

            },
            set default(selector) {
                selector.html( window.NSTemplateUIAnimationContent);
            },

        },


    };



    load_media($('#pre-loading').attr('data-page'));

    $('#pre-loading').on('click',function () {

        let page = $('#pre-loading').attr('data-page');
        load_media(page);

        return false;
    });

    function load_media(page){

        let selector = $("#pre-loading");

        $.ajax({
            url: "<?=  site_url("ajax/uploader/loadMedia")?>",
            data: {
                "page": page,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation0.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);

                NSTemplateUIAnimation0.button.default = selector;

                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {

                    NSTemplateUIAnimation0.button.default = selector;

                    $(".images-container").append(data.html);

                    if(data.pagination.nextpage > 0){
                        $("#pre-loading").attr('data-page',data.pagination.nextpage);
                    }else{
                        $("#pre-loading").remove();
                    }

                } else if (data.success === 0) {

                    NSTemplateUIAnimation0.button.default = selector;

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


    function file_uploaded0(results) {
        $('.image-previews').remove();
        document.location.reload();
    }



</script>