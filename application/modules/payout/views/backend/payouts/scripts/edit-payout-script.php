<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    $('#select_owner').select2({

        ajax: {
            url: "<?=site_url("ajax/user/getOwners")?>",
            dataType: "json",
            data: function (params) {

                var query = {
                    q: params.term,
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            },
            results: function (data, page) {
                console.log(data);

                return {results: data};
            }
        }
    });


    $("#status").val('<?=$payout['status']?>').trigger('change');
    $("#method").val('<?=$payout['method']?>').trigger('change');


    $("#btnEdit").on('click', function () {

        let selector = $("#btnEdit");

        var dataSet0 = {
            "note":  $("#form #editable-textarea").val(),
            "amount": parseFloat($("#form #amount").val()),
            "user_id": $("#form #select_owner").val(),
            "method": $("#form #method").val(),
            "status": $("#form #status").val(),
            "id": <?=$id?>,
        };

        //alert(Object.values(dataSet0));

        $.ajax({
            url: "<?=  site_url("ajax/payout/edit_payout")?>",
            data: dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {
                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = data.url;
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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

</script>


