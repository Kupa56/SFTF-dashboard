<script src="<?= adminAssets("plugins/jQueryUI/jquery-ui.js") ?>"></script>
<script>

    $( ".dd" ).sortable({
        start: function(e, ui) {

        },
        stop: function() {
            re_order();
        }
    });

    function re_order(){

        let data = "";

        $( ".dd .line" ).each(function( index ) {
            let id = parseInt($(this).attr('data-id'));

            if(data !== ""){
                data = data+","+id
            }else{
                data = id
            }

        }).promise().done(function () {
            re_order_exe(data);
        });

    }



    function re_order_exe(list) {

        console.log(list);

        $.ajax({
            url: "<?=  site_url("ajax/category/re_order")?>",
            data: {
                "list":list
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {


            }, error: function (request, status, error) {

                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {

                } else if (data.success === 0) {

                }
            }
        });


    }


    $(".delete").on('click',function () {

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("category/ajax/delete")?>",
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