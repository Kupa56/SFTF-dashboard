<?php


$this->load->view("payment/client_view/header-client");


?>


<!-- The Styling File -->
<style>

    #card-errors{
        color: red;
    }

    #pay-now{
        display: block;
        margin-top: 10px;
        padding: 11px;
        border-radius: 6px;
        width: 100%;
    }

    #button{
        display: block;
        margin-top: 10px;
        padding: 11px;
        border-radius: 6px;
        width: 100%;
    }

</style>

<!-- Content Wrapper. Contains page content -->
<section class="main">
    <div class="my-custom-container">
        <div class="row payment">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <form id="paymentForm">
                    <div class="form-submit">
                        <button class="btn btn-primary" onclick="submitPayment()" id="pay-now"><?=Translate::sprintf("Pay now %s",array(Currency::parseCurrencyFormat($details_subtotal,$currency)))?></button>
                        <a class="btn btn-default" href="<?=$callback_error_url.'#error-paystack-charge00'?>" id="button"><?=_lang("Cancel")?></a>
                    </div>
                </form>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>

<!-- The needed JS files -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener("submit", payWithPaystack, false);
    function submitPayment(e) {
        e.preventDefault();
        payWithPaystack();
    }

    payWithPaystack();

    function payWithPaystack() {
        let handler = PaystackPop.setup({
            currency: '<?=$currency?>',
            key: '<?=ConfigManager::getValue('PAYSTACK_KEY_ID')?>', // Replace with your public key
            email: '<?=SessionManager::getData('email')?>',
            amount: <?=$details_subtotal?> * 100,
            ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
            // label: "Optional string that replaces customer email"
            onClose: function(){
                document.location.href = '<?=$callback_error_url.'#error-paystack-charge01'?>'
            },
            callback: function(response){
               document.location.href = '<?=site_url('payment/paystack/verify')?>?reference='+ response.reference;
            }
        });
        handler.openIframe();
    }
</script>

</body>
</html>

