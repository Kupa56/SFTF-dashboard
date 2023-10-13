<div class="box box-solid form-wallet">
    <div class="box-header">
        <div class="box-title"  style="width : 100%;">
            <strong><?=Translate::sprint("Send Money")?></strong>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">
                <form>
                    <div class="form-group">
                        <label><?=_lang("Enter client email")?></label>
                        <input name="email" class="form-control" type="email" placeholder="<?=_lang("Enter email")?>" />
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Amount")?> (<?=ConfigManager::getValue("DEFAULT_CURRENCY")?>)</label>
                        <input name="amount" class="form-control" type="number" placeholder="<?=_lang("Enter amount")?>" />
                    </div>
                    <div class="form-group">
                        <button id="verifyAndSendBtn" class="btn btn-primary"><?=_lang("Verify & Send")?></button>
                    </div>
                </form>
            </div>

            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"  style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?=Translate::sprint("Balance")?></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body box-title">
                        <strong><?=_lang("Remaining account credits")?></strong>
                        <div class="box-title">
                            <?php
                            $balance = $this->mWalletModel->getBalance(SessionManager::getData('id_user'))
                            ?>
                            <b class="font-size20px <?=$balance==0?"text-red":""?>">
                                <?php
                                echo Currency::parseCurrencyFormat(
                                    $balance,
                                    ConfigManager::getValue("DEFAULT_CURRENCY")
                                )
                                ?>
                            </b>

                            &nbsp;&nbsp;<a href="<?=admin_url("payment/billing")?>"><i class="mdi mdi-plus"></i><?=_lang("Top-up")?></a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<?php

$script = $this->load->view('backend/html/digitalWallet/scripts/form-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
