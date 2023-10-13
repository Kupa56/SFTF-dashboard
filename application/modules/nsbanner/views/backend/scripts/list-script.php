<script>


    $(".banners .delete").on('click',function () {

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("nsbanner/ajax/delete")?>",
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

                    if(data.success === 1){
                        modal("success",data,function (success) {
                            document.location.reload();
                        });
                    }else{

                        var errorMsg = "";
                        for (var key in data.errors) {
                            errorMsg = errorMsg + data.errors[key] + "\n";
                        }
                        if (errorMsg !== "") {
                            alert(errorMsg);
                        }

                        modal("error",data);
                    }


                }
            });
        };

        return false;
    });



</script>