<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>


    $('.selectCat').select2();


    $("#btnCreate").on('click', function () {

        let selector = $(this);

        var name = $("#form #name").val();

        var detail = $("#editable-textarea").val();
        var tel = $("#form #tel").val();
        var cat = $("#form #cat").val();
        var website = $("#form #web").val();

        var address = $("#form #<?=$location_fields_id['address']?>").val();
        var lat = $("#form #<?=$location_fields_id['lat']?>").val();
        var lng = $("#form #<?=$location_fields_id['lng']?>").val();
        var city = $("#form #<?=$location_fields_id['city']?>").val();
        var country = $("#form #<?=$location_fields_id['country']?>").val();

        var canChat = $("input[name='canChat']:checked").val();
        var book = $("input[name='book']:checked").val();
        var video_url = $("#form #video_url").val();
        var affiliate_link = $('#affiliate').val();


        var dataSet  = {
            "name": name,
            "address": address,
            "detail": detail,
            "tel": tel,
            "website": website,
            "cat": cat,
            "lat": lat,
            "lng": lng,

            "city": city,
            "country": country,

            "canChat": canChat,
            "video_url": video_url,
            "book": book,
            "affiliate_link": affiliate_link,
            "images": JSON.stringify(<?=$uploader_variable?>),
        <?php if(ModulesChecker::isRegistred("gallery")){ ?>
            "gallery": JSON.stringify(<?=$gallery_variable?>)
        <?php } ?>
        };

        if ('undefined' !== typeof times) {
            dataSet.times = times;
        }


        $.ajax({
            url: "<?=  site_url("ajax/store/createStore")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);

                NSTemplateUIAnimation.button.default = selector;

                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {


                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("store/my_stores")?>";

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



    let bookingChecker = $("input[name='book']");

    if(bookingChecker.is(':checked')){
        $('#affiliate').attr('disabled',true);
    }else{
        $('#affiliate').attr('disabled',false);
    }

    bookingChecker.on('click',function (){
        if($(this).is(':checked')){
            $('#affiliate').attr('disabled',true);
        }else{
            $('#affiliate').attr('disabled',false);
        }
    });


</script>


