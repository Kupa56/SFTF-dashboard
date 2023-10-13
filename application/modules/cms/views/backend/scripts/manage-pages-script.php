<script>



    $('body').delegate('#list .remove','click',function(){

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("cms/ajax/removePage")?>",
                data: {
                    "id":id
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                    modal("beforeSend",xhr);

                }, error: function (request, status, error) {

                    modal("error",request);
                    console.log(request);

                },
                success: function (data, textStatus, jqXHR) {

                    modal("success",data,function (success) {
                        document.location.reload();
                    });

                }
            });
        };

        return false;
    });



</script>