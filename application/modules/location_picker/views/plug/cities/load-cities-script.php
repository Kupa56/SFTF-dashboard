<script>

    $('#loadLoc468').on('click',function () {

        let $selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=site_url("location_picker/ajax/loadLocations")?>",
            dataType: 'json',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = $selector;
            }, error: function (request, status, error) {
                NSTemplateUIAnimation.button.default = $selector;
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {
                NSTemplateUIAnimation.button.success = $selector;
                console.log("locations loaded");
                document.location.reload();
            }

        });

        return false;
    });




</script>