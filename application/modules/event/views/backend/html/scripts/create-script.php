<script src="<?= adminAssets("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/datepicker/bootstrap-datepicker.js") ?>"></script>

<script>

    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });
</script>

<script>

    var store_id = 0;
    $("#compose-textarea").wysihtml5();

    $("#btnCreate").on('click', function () {

        let selector = $(this);

        var name = $("#form #name").val();
        var desc = $("#editable-textarea").val();
        var tel = $("#form #tel").val();
        var website = $("#form #web").val();
        var date_b = $("#form #date_b").val();
        var date_e = $("#form #date_e").val();


        var address = $("#form #<?=$location_fields_id['address']?>").val();
        var lat = $("#form #<?=$location_fields_id['lat']?>").val();
        var lng = $("#form #<?=$location_fields_id['lng']?>").val();


        $.ajax({
            url: "<?=  site_url("ajax/event/create")?>",
            data: {
                "store_id": store_id,
                "name": name,
                "address": address,
                "desc": desc,
                "tel": tel,
                "website": website,
                "lat": lat,
                "lng": lng,
                "date_b": date_b,
                "date_e": date_e,
                "images": JSON.stringify(<?=$uploader_variable?>)
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
                    document.location.href = "<?=admin_url("event/my_events")?>";
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

<script>


    $('.selectStore').select2();
    $('.selectStore').on('select2:select', function (e) {
        // Do something
        var data = e.params.data;
        var id = data.id;
        store_id = id;
        if (id > 0) {

            var adr = $(".selectStore option[value=" + id + "]").attr("adr");
            var lat = $(".selectStore option[value=" + id + "]").attr("lat");
            var lng = $(".selectStore option[value=" + id + "]").attr("lng");

            $("#address").val(adr);
            $("#lat").val(lat);
            $("#lng").val(lng);

        }

    });
</script>


