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

    <div class="my-custom-container" id="xxx">
        <div class="row payment">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">

                <button class="button" id="paytmWithPaytm">Pay with Paytm</button>

            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>
<!-- The needed JS files -->


<?php

$logo = ImageManagerUtils::getValidImages(APP_LOGO);
$imageUrl = adminAssets("images/logo.png");
if(!empty($logo)){
    $imageUrl = $logo[0]["560_560"]["url"];
}

?>

<!-- JQUERY File -->
<script type="application/javascript"  src="https://<?=(PAYTM_CONFIG_DEV_MODE == TRUE)?"securegw-stage":"securegw"?>.paytm.in/merchantpgpui/checkoutjs/merchants/<?=PAYTM_KEY_ID?>.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>


<script>


    $("#paytmWithPaytm").on('click',function () {
        onScriptLoad();
    });

    function onScriptLoad(){
        var config = {
            "flow": "DEFAULT",
            "hidePaymodeLabel": true,
            "merchant":{
                "name":"<?=APP_NAME?>",
                "logo":"<?=$imageUrl?>",
                "mid":"<?=PAYTM_KEY_ID?>",
                "redirect":true,
                "callbackUrl":"",
                "hidePaytmBranding":false
            },
            "payMode":{
                "labels":{

                },
                "filter":[

                ],
                "order":[
                    "LOGIN",
                    "CARD",
                    "NB",
                    "UPI"
                ]
            },
            "style":{
                "headerBackgroundColor":"#8dd8ff",
                "headerColor":"#<?=DASHBOARD_COLOR?>"
            },
            "data": {
                "orderId": "ORDERID_"+<?=$order_id?>,
                "token": "<?=$txnToken?>",
                "tokenType": "TXN_TOKEN",
                "amount": <?=$amount?>
            },
            "handler": {
                "notifyMerchant": function(eventName,data){
                    console.log("notifyMerchant handler function called");
                    console.log("eventName => ",eventName);
                    console.log("data => ",data);
                }
            }
        };

        if(window.Paytm && window.Paytm.CheckoutJS){
            window.Paytm.CheckoutJS.onLoad(function excecuteAfterCompleteLoad() {
                // initialze configuration using init method
                console.log("onLoaded");

                window.Paytm.CheckoutJS.init(config).then(function onSuccess() {
                    // after successfully updating configuration, invoke JS Checkout
                    console.log("onSuccess");
                    alert("onSuccess");
                    window.Paytm.CheckoutJS.invoke();
                }).catch(function onError(error){
                    console.log("error => ",error);
                    alert("error => "+JSON.stringify(error));
                });
            });
        }


    }
</script>

</body>
</html>

