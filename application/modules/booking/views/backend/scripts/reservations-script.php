<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script src="<?= adminAssets("plugins/daterangepicker/moment.js") ?>"></script>
<script src="<?= adminAssets("plugins/daterangepicker/daterangepicker.js") ?>"></script>
<script>


    var range_date_start = "";
    var range_date_end = "";


    $('input[name="datefilter"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('input[name="datefilter"]').on('apply.daterangepicker', function (ev, picker) {

        range_date_start = picker.startDate.format('YYYY-MM-DD');
        range_date_end = picker.endDate.format('YYYY-MM-DD');

        $(this).val(range_date_start + ' - ' + range_date_end);
        //window.alert("You chose: " + range_date_start + ' - ' + range_date_end);
    });

    $('input[name="datefilter"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });



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


    $("div #_filter").on('click', function () {

        var selector = $(this);

        $.ajax({
            url: "<?= site_url("booking/ajax/query")?>",
            data: {
                "url": "<?=current_url()?>",
                "query": {
                    "owner_id": $("#select_owner").val(),
                    "date_start": range_date_start,
                    "date_end": range_date_end,
                    "order_status": $("#select_order_status").val(),
                    "payment_status": $("#select_payment_status").val(),
                    "limit": $("#limit").val(),
                }
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                NSTemplateUIAnimation.button.success = selector;

                if (data.success === 1) {
                    document.location.href = data.url;
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

        return false;
    });


</script>