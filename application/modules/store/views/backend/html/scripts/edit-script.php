<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script>

    $('.selectCat').select2();


    <?php if($store['user_id'] == $this->mUserBrowser->getData("id_user")) : ?>

    $("#btnCreate").on('click', function () {

        var selector = $(this);

        var id = $("#form #id").val();
        var name = $("#form #name").val();
        var detail = $("#editable-textarea").val();
        var tel = $("#form #tel").val();
        var cat = $("#form #cat").val();
        var website = $("#form #web").val();
        var address = $("#form #<?=$location_fields_id['address']?>").val();
        var lat = $("#form #<?=$location_fields_id['lat']?>").val();
        var lng = $("#form #<?=$location_fields_id['lng']?>").val();
        var canChat = $("input[name='canChat']:checked").val();
        var book = $("input[name='book']:checked").val();
        var video_url = $("#form #video_url").val();
        var affiliate_link = $('#affiliate').val();

        var city = $("#form #<?=$location_fields_id['city']?>").val();
        var country = $("#form #<?=$location_fields_id['country']?>").val();

        var dataSet = {
            'id': id,
            "name": name,
            "address": address,
            "detail": detail,
            "website": website,
            "tel": tel,
            "cat": cat,
            "lat": lat,
            "lng": lng,

            "city": city,
            "country": country,

            "canChat": canChat,
            "book": book,
            "affiliate_link": affiliate_link,
            "video_url": video_url,
            "images": JSON.stringify(<?=$uploader_variable?>),
            <?php if(ModulesChecker::isRegistred("gallery") and isset($gallery_variable)){ ?>
            "gallery": JSON.stringify(<?=$gallery_variable?>)
            <?php } ?>
        };


        if ('undefined' !== typeof times) {
            dataSet.times = times;
        }

        $.ajax({
            url: "<?=  site_url("ajax/store/edit")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);

                NSTemplateUIAnimation.button.default = selector;

                console.log(request.responseText);
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
    <?php endif; ?>


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
<?php if (GroupAccess::isGranted('store', MANAGE_STORES)): ?>
    <script>


        $("#featured_item1").change(function () {

            var featured = 0;

            if (this.checked)
                featured = 1;
            else
                featured = 0;

            //   alert(featured);

            $.ajax({
                url: "<?=  site_url("ajax/store/markAsFeatured")?>",
                data: {
                    "id": "<?=$store['id_store']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

                    } else if (data.success === 0) {
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
            return true;
        });


        $("#featured_item0").change(function () {

            var featured = 0;


            $.ajax({
                url: "<?=  site_url("ajax/store/markAsFeatured")?>",
                data: {
                    "id": "<?=$store['id_store']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

                    } else if (data.success === 0) {
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
            return true;
        });

    </script>
<?php endif; ?>
