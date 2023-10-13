<?php if (GroupAccess::isGranted('pack',DELETE_PACK)): ?>
    <script>

        $("div .remove").on('click', function () {

            var selector = $(this);

            let id = parseInt($(this).attr('data-id'));

            NSAlertManager.alert.request = function (modal) {
                $.ajax({
                    url: "<?=site_url("ajax/pack/delete")?>",
                    data: {
                        "id": id
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        modal("beforeSend", xhr);
                        selector.attr("disabled", true);
                    }, error: function (request, status, error) {
                        modal("error", request);
                        console.log(request);
                        selector.attr("disabled", false);
                    },
                    success: function (data, textStatus, jqXHR) {

                        selector.attr("disabled", false);
                        if (data.success === 1) {
                            document.location.reload();
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
            };

            return false;
        });


    </script>

<?php endif; ?>


