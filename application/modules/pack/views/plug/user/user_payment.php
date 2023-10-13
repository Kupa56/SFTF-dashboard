<?php

//get linked pack -> check if it's paid -> then
$linked_pack = $this->mPack->getPack(  intval(SessionManager::getData("pack_id"))  );
?>

<?php if(isset($linked_pack->price) && $linked_pack->price > 0): ?>

<div class="col-md-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><strong><?= Translate::sprint("Subscription & Automatic Payments") ?></strong>
            </h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body margin">
            <div class="form-group">
                <label>
                    <input type="checkbox" id="auto_renew" <?=SessionManager::getData("auto_renew")==1?"checked":""?>>&nbsp;&nbsp;<?=_lang("Auto renew")?>
                </label>



            <?php if(SessionManager::getData("auto_renew")==1): ?>
                    <br>
                <p class="text-blue">

                <?php

                    $balance = $this->mWalletModel->getBalance(SessionManager::getData('id_user'));

                    echo Translate::sprintf("The next payment on %s will be processed automatic by using your current balance: %s",array(
                            "<u>".date("Y-m-d H:i:s",strtotime(SessionManager::getData("will_expired")))."</u>",
                            "<b>".Currency::parseCurrencyFormat($balance,DEFAULT_CURRENCY)."</b>"
                        ));

                    ?>

                </p>
            <?php endif; ?>
            </div>
        </div>

    </div>
    <!-- /.box-body -->
</div>



<?php
    $script = "<script>


$('#auto_renew').on('click',function () {
    
        var auto_renew = 0;

        if($(this).is(':checked'))
            auto_renew = 1;

        $.ajax({
            url:\"".site_url("payment/ajax/update_auto_renew")."\",
            data:{
                auto_renew:auto_renew
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

            },error: function (request, status, error) {
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                if (data.success === 1) {
                    document.location.reload();
                } else if (data.success === 0) {

                    var errorMsg = \"\";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + \"\\n\";
                    }
                    if (errorMsg !== \"\") {
                        alert(errorMsg);
                    }
                }
            }
        });

    });

</script>";
    AdminTemplateManager::addScript($script);

    ?>



<?php endif; ?>

<div class="col-md-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><strong><?= Translate::sprint("Billing") ?></strong>
            </h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body margin">


        <?php

                $billingInfo = $this->mPaymentModel->getBillingInfo(
                    intval($this->mUserBrowser->getData('id_user'))
                );

            ?>

            <div>
                <label><?= Translate::sprint("Last Invoice") ?>:</label><br>
            <?php
                if ($billingInfo['invoice'] != NULL) {
                    $status = Translate::sprint("Unpaid");

                    if ($billingInfo['invoice']->status == 1) {
                        $status = Translate::sprint("Paid");
                    }

                    echo  Translate::sprint("InvoiceID") . ': <u>#' .  str_pad($billingInfo['invoice']->id, 6, 0, STR_PAD_LEFT) . "</u> (" . $status . ")";


                    if ($billingInfo['invoice']->status == 1)
                        echo " - " . Translate::sprint("Date") . ":" . $billingInfo['invoice']->updated_at;
                }else{
                    echo Translate::sprint("No invoice");
                }
                ?>
            </div>

            <br>


            <div>
                <label><?= Translate::sprint("Last Transaction") ?>:</label><br>
            <?php

                if ($billingInfo['transaction'] != NULL) {

                    $transaction = $billingInfo['transaction']->transaction_id;
                    $method = $billingInfo['invoice']->method;

                    echo $transaction." - (".$method.") " ;


                }else{
                    echo Translate::sprint("No transaction");
                }

                ?>
            </div>

        </div>

    </div>
    <!-- /.box-body -->
</div>


