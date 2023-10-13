<?php
$this->load->view("payment/client_view/header-client");
?>


<?php


    $items = json_decode($invoice->items);

    $mp_id = 0;


?>


<!-- Content Wrapper. Contains page content -->
<section class="main">

    <div class="my-custom-container">
        <div class="row payment">
            <div class="col-sm-12">
            </div>
            <div class="col-sm-6">
                <div class="methods">

                <?php
                    $provided_payment = PaymentsProvider::getPayments($invoice->module);


                    $this->load->library('user_agent');
                    $platform =  $this->agent->platform();

                    $list_payments = array();


                    foreach ($provided_payment as $key => $payment){
                        if(strtolower($platform) != "ios" && $payment['id'] == PaymentsProvider::APPLE_PAY){
                            continue;
                        }else{
                            $list_payments[] = $payment;
                        }
                    }


                    ?>

                <?php foreach ($list_payments as $payment): ?>

                    <?php if( PaymentsProvider::isProvided($invoice->module,$payment['id'])) : ?>
                            <div data="<?=$payment['id']?>" class="<?=$list_payments[0]['id']==$payment['id']?'active':''?> <?=$payment['payment']?> method ">
                                <div class="detail">
                                    <p>
                                        <img src="<?=$payment['image']?>"/>
                                        <strong><?=$payment['payment']?></strong>
                                        <span><?=_lang($payment['description'])?></span>
                                    </p>
                                </div>
                                <div class="clearfix"></div>
                            </div>

                        <?php
                            if($mp_id == 0)
                                $mp_id = $payment['id'];
                            ?>
                    <?php endif; ?>

                <?php endforeach; ?>


                </div>
            </div>
            <div class="col-sm-6">
                <div class="my-invoice">

                    <div class="items">
                    <?php

                        $invoice->amount = 0;
                        foreach ($items as $item){
                            echo '<div class="item">';

                            if($invoice->module != "wallet"){
                                echo "<span>"._lang($item->item_name)."</span> x ".intval($item->qty);
                            }else{
                                echo "<span>".Translate::sprintf($item->item_name,array(
                                        Currency::parseCurrencyFormat(($item->price*$item->qty),$invoice->currency)
                                    ))."</span>";
                            }

                            echo "<b>".Currency::parseCurrencyFormat(($item->price*$item->qty),$invoice->currency)."</b>";
                            echo '<div class="clearfix"></div>';
                            echo '</div>';

                            $invoice->amount = $invoice->amount+($item->price*$item->qty);
                        }

                        ?>

                    </div>


                <?php if(!TaxManager::isDisabled($invoice->module)): if(defined('DEFAULT_TAX') AND DEFAULT_TAX>0 ): ?>

                    <?php
                        $percent = 0;
                        $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                        if($tax!=NULL){
                            $percent = $tax['value'];
                            $tax_value = ( ($tax['value']/100) * $invoice->amount ) ;
                            $invoice->amount = $tax_value + $invoice->amount ;
                        }
                        ?>

                        <div class="items">
                            <div class="item">
                                <span><?=$tax['name']?></span>
                            <?php
                                echo "<b>". Currency::parseCurrencyFormat(  $tax_value  ,$invoice->currency)."</b>";
                                ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                <?php elseif(defined('DEFAULT_TAX') AND DEFAULT_TAX==-2): ?>
                    <?php
                        if(defined('MULTI_TAXES') and count(MULTI_TAXES)>0){
                            $litTaxes  = jsonDecode(MULTI_TAXES,JSON_OBJECT_AS_ARRAY);
                            $newAmount = $invoice->amount;
                            foreach ($litTaxes as $value) {
                                $percent = 0;
                                $mTax = $this->mTaxModel->getTax($value);
                                if($mTax!=NULL){
                                    $percent = $mTax['value'];
                                    $newAmount = ( ($mTax['value']/100)*$invoice->amount ) + $newAmount ;
                                }
                                ?>
                                <div class="items">
                                    <div class="item">
                                        <span><?=$mTax['name']?></span>
                                    <?php
                                        echo "<b>". Currency::parseCurrencyFormat(( ($mTax['value']/100)*$invoice->amount ),$invoice->currency)."</b>";
                                        ?>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                        <?php }

                        } ?>
                <?php endif; endif; ?>

                <?php

                        $extras = jsonDecode($invoice->extras,JSON_OBJECT_AS_ARRAY);

                        if(!is_array($extras))
                            $extras = array();

                        $amount = $invoice->amount;
                    ?>
                <?php foreach ($extras as $key => $value): ?>

                        <div class="items">
                            <div class="item">
                                <span><?=_lang($key)?></span>
                            <?php
                                echo "<b>".Currency::parseCurrencyFormat($value,$invoice->currency)."</b>";
                                ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                    <?php
                            $amount = $amount + doubleval($value);
                        ?>

                <?php endforeach; ?>

                    <div class="items">
                        <div class="item">
                            <strong><?=Translate::sprint("TOTAL")?></strong>
                        <?php
                            echo "<b>".Currency::parseCurrencyFormat($amount,$invoice->currency)."</b>";
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="pay-btn">
                    <a method-data="<?=$mp_id?>" href="<?=site_url('payment/process_payment?invoiceid='.$invoice->id)?>" id="pay-now" class="btn btn-primary btn-flat">
                        <u><?=Translate::sprint("Confirm")?></u>
                    </a>
                </div>

                <div class="cancel-btn">
                    <a href="<?=$cancel_url?>" id="cancel-now" class="btn bg-gray btn-flat">
                        <u><?=Translate::sprint("Cancel")?></u>
                    </a>
                </div>

            </div>
        </div>
    </div>

</section>

<?php

    $this->load->view('payment/client_view/html/scripts/make-payment-script');

?>

</body>
</html>
