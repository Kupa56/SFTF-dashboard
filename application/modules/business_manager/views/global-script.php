<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>

    //load_component("store","business_manager/store/create");

    function load_component(module,path) {
        $.ajax({
            url: "<?=  site_url("ajax/business_manager/load_component")?>",
            data: {
                //"times": times,
                "path": path,
                "module": module,

            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {


            }, error: function (request, status, error) {

                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);
                if (data.success === 1) {
                    $("#loaded-components").append(data.html).append(data.script);
                } else if (data.success === 0) {

                }
            }
        });

    }


</script>