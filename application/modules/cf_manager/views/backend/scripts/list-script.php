<script>


    $("#cf_list .remove").on('click',function () {

        let id = parseInt($(this).attr('data-id'));

        alert(id);

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("cf_manager/ajax/remove")?>",
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
                            document.location.href="<?=admin_url("cf_manager/cf_list")?>";
                        });
                    }else{

                        var errorMsg = "";
                        for (var key in data.errors) {
                            errorMsg = errorMsg + data.errors[key] + "<br/>";
                        }
                        if (errorMsg !== "") {
                            NSAlertManager.simple_alert.request = errorMsg;
                        }

                        modal("error",data);
                    }


                }
            });
        };

        return false;
    });



</script>