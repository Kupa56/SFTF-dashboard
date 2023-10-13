<script>

    $(".form-wallet #verifyAndSendBtn").on('click',function () {

        let selector = $(this);

        $.ajax({
            url:"<?=site_url("digital_wallet/ajax/verifyAndCreateWalletTransaction")?>",
            type:'POST',
            data:{
                'amount': $('.form-wallet input[name=amount]').val(),
                'email': $('.form-wallet input[name=email]').val(),
                'SendAsadmin': $('.form-wallet input[name=SendAsadmin]').is(":checked"),
                'token': '<?=$this->mUserBrowser->setToken("WgTh_ABbnl")?>',
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

                console.log(data);
                if(data.success===1){
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();
                }else if(data.success===0){
                    NSTemplateUIAnimation.button.default = selector;
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"<br>";
                    }

                    if(errorMsg!==""){
                        NSAlertManager.simple_alert.request = errorMsg;
                    }
                }
            }

        });



        return false;
    });


</script>