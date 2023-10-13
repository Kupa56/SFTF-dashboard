<script>


    $("#add-balance-modal #select_amount").select2();

    $("#add-balance-modal #select_amount").on('change',function () {

        let val = parseInt($(this).val());

        if(val === -1){
            $('#add-balance-modal .custom_amount').removeClass("hidden");
            $('#add-balance-modal .custom_amount #amount').val("");
        }else{
            $('#add-balance-modal .custom_amount').addClass("hidden");
            $('#add-balance-modal .custom_amount #amount').val($(this).val());
        }

        return false;
    });


    $("#add-balance-modal #add_balance").on('click',function () {

        let selector = $(this);

        $.ajax({
            url:"<?=site_url("payment/ajax/add_balance")?>",
            type:'POST',
            data:{
                'amount': $('#add-balance-modal .custom_amount #amount').val()
            },
            dataType: 'json',
            beforeSend: function (xhr) {
               NSTemplateUIAnimation.button.loading = selector;
            },error: function (request, status, error) {
                NSTemplateUIAnimation.button.default = selector;
                alert(request.responseText);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){

                    NSTemplateUIAnimation.button.success = selector;
                    $("#add-balance-modal").modal("hide");
                    document.location.href = data.result;

                }else if(data.success===0){
                    NSTemplateUIAnimation.button.default = selector;
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
    });

    $('a.linkAccess').on('click',function(){

        $('#modal-default').modal('show');
        //$('#myModal').modal('show');
        //('#myModal').modal('hide');

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

