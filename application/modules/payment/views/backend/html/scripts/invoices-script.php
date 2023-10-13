
<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>

<script>


    $("#filter").select2();
    $("#filter").on('change',function () {
        var status = $(this).val();
        document.location.href = "<?=admin_url('payment/invoices')?>?status="+status;
    });


    $('a.linkAccess').on('click',function(){

        $('#modal-default').modal('show');
        var url = ($(this).attr('href'));
        $("#_delete").on('click',function () {
            //calling the ajax function
            $(this).attr("disabled",true);
            pop(url);
            return true;
        });

    });


    function getURLParameter(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    }

    function pop(url) {

        $.ajax({
            type:'get',
            url:url,
            dataType: 'json',
            beforeSend: function (xhr) {
                $(".linkAccess").attr("disabled",true);
            },error: function (request, status, error) {
                alert(request.responseText);
                $(".linkAccess").attr("disabled",false);
            },
            success: function (data, textStatus, jqXHR) {

                $('#modal-default').modal('hide');

                $(".linkAccess").attr("disabled",false);
                if(data.success===1){
                    document.location.reload();
                }else if(data.success===0){
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }

        });

        return false;
    }

</script>


