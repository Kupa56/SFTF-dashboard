<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    $("div #_deleteRev").on('click', function () {

        var selector = $(this);

        var id = $(this).attr("data");

        $.ajax({
            url: "<?=  site_url("ajax/store/deleteReview")?>",
            data: {"id": id, "type": "review"},
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

    $('.select2#selectStore').select2({
        ajax: {
            url: '<?=site_url('ajax/store/getStoresAjax')?>',
            dataType: 'json',
            delay: 250,
            type: 'GET',
            data: function (params) {

                console.log(params);

                var query = {
                    search: params.term,
                    page: params.page || 1
                };
                // Query parameters will be ?search=[term]&page=[page]
                return query;
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                console.log(data);

                params.page = params.page || 1;

                return {
                    results: data.results
                }

            },
            cache: true
        }
    });

    $('.select2#selectStore').on('change',function () {
        let id = $(this).val();
        if(id>0){
            document.location.href = "<?=admin_url("store/reviews?id=")?>"+id;
        }

        return false;
    });

    var newState = new Option("<?=$store['name']?>",0, true, true);
    // Append it to the select
    $(".select2#selectStore option[value=0]").after(newState);
    $(".select2#selectStore").trigger('change');

</script>