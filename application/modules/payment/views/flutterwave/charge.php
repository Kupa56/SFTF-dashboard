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

</style>

<!-- Content Wrapper. Contains page content -->
<section class="main">

    <div class="my-custom-container">
        <div class="row payment">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">


                <button class="btn btn-primary" id="pay-now"><?=Translate::sprintf("Pay now %s",array(Currency::parseCurrencyFormat($details_subtotal,DEFAULT_CURRENCY)))?></button>


            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>
<!-- The needed JS files -->
<!-- JQUERY File -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>


<?php

$logo = ImageManagerUtils::getValidImages(APP_LOGO);
$imageUrl = adminAssets("images/logo.png");
if(!empty($logo)){
    $imageUrl = $logo[0]["560_560"]["url"];
}

?>
<script>

    makePayment();

    $("#pay-now").on('click',function () {
        makePayment();
        return false;
    });

    function makePayment() {
        FlutterwaveCheckout({
            public_key: "<?=FLUTTERWAVE_KEY_ID?>",
            tx_ref: <?=rand(1000,99999)?>,
            amount: <?=$details_subtotal?>,
            currency: "<?=DEFAULT_CURRENCY?>",
            payment_options: " ",
            customer: {
                email: "<?=SessionManager::getData("email")?>",
                phone_number: "",
                name: "<?=SessionManager::getData("email")?>",
            },
            redirect_url: // specified redirect URL
                "<?=site_url("payment/flutterwave/verify")?>",

            callback: function (data) {
                console.log(data);
            },
            onclose: function() {
                // close modal
            },
            customizations: {
                title: "<?=APP_NAME?>",
                description: "Payment for items in cart",
                logo: "<?= $imageUrl ?>",
            },
        });
    }


</script>


</body>
</html>

