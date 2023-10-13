<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>


    $("#DEFAULT_TAX").on("change",function () {

        var id_taxes = $(this).val();
        console.log(" taxes selected id  : "+id_taxes);
        if(id_taxes === -2)
        {
            console.log("multi_taxes ");
            $('#multi_taxes').modal('show');
        }
    });

    $('.DEFAULT_TAX').select2();

    $(".content #btnSaveDefaultTax").on('click', function () {

        var selector = $(this);

        var dataSet = {
            "id": $("#DEFAULT_TAX").val(),
            "token": ""
        };


        $.ajax({
            url: "<?=  site_url("ajax/payment/setDefault")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled", false);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
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


        return false;
    });

    $("#submit_multi_taxes").on('click', function () {

        var selector = $(this);
        var list_multi_taxes = [];
        $("input:checked").each(function() {
            list_multi_taxes.push(  $(this).val() );
        });
        $.ajax({
            url: "<?=  site_url("ajax/payment/setMultiTaxes")?>",
            data: {ids : list_multi_taxes},
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled", false);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
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


        return false;

    });

    $(".content #btnAddNewTax").on('click', function () {


        var selector = $(this);

        var dataSet = {
            "name": $(".taxes #tax_name").val(),
            "value": $(".taxes #tax_value").val(),
            "token": ""
        };



        $.ajax({
            url: "<?=  site_url("ajax/payment/addTax")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                selector.attr("disabled", true);
            }, error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled", false);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
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


        return false;
    });

    //$('#DEFAULT_TAX').val(<?=DEFAULT_TAX?>).trigger('change');

</script>
