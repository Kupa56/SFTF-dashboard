<script>

    $("#add_new_language").on('click',function () {

        var selector = $(this);

        var _name = $("#_language_name").val();
        var _code = $("#_language_code").val();
        var _direction = $("#_direction").val();

        $.ajax({
            url: "<?=  site_url("ajax/nstranslator/add_new_language")?>",
            data: {
                "name": _name,
                "code": _code,
                "direction": _direction
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

               selector.attr("disabled", true);

            }, error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled", false);
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);
                //console.log(data);
                selector.attr("disabled", false);

                if (data.success === 1) {
                    document.location.href = "<?=admin_url("nstranslator/languages")?>";
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


        return false;
    });
</script>
