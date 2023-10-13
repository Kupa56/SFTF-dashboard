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


                <form action="<?=$_SESSION['callback_success_url']?>" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>

            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>
<!-- The needed JS files -->
<!-- JQUERY File -->
<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId=<?=$id?>"></script>


<?php

$logo = ImageManagerUtils::getValidImages(APP_LOGO);
$imageUrl = adminAssets("images/logo.png");
if(!empty($logo)){
    $imageUrl = $logo[0]["560_560"]["url"];
}

?>
<script>



</script>


</body>
</html>

