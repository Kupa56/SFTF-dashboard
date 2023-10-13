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

</script>

<?php if (GroupAccess::isGranted('user', DELETE_USERS)): ?>

    <script>


        $("div .remove").on('click', function () {

            let id = parseInt($(this).attr('data-id'));

            NSAlertManager.alert.request = function (modal) {
                $.ajax({
                    url: "<?=site_url("ajax/user/delete")?>",
                    data: {
                        "id": id
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        modal("beforeSend", xhr);
                    }, error: function (request, status, error) {
                        modal("error", request);
                        console.log(request);
                    },
                    success: function (data, textStatus, jqXHR) {

                        modal("success", data, function (success) {
                            document.location.reload();
                        });

                    }
                });
            };

            return false;
        });


    </script>
<?php endif; ?>
