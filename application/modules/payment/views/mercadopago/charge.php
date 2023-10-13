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
                <form action="/payment/mercadopago/process_payment" method="post" id="paymentForm">
                    <h3><?=_lang("Buyer Details")?></h3>
                    <div>
                        <div class="form-group">
                            <label for="email"><?=_lang("E-mail")?></label>
                            <input class="form-control" id="email" name="email" type="text" value="<?=$client['email']?>"/>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="docType"><?=_lang("Document Type")?></label>
                                    <select class="form-control" id="docType" name="docType" data-checkout="docType" type="text"></select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="docNumber"><?=_lang("Document Number")?></label>
                                    <input class="form-control" id="docNumber" name="docNumber" data-checkout="docNumber" type="text"/>
                                </div>
                            </div>
                        </div>

                    </div>
                    <h3>Card Details</h3>
                    <div>
                        <div class="form-group">
                            <label for="cardholderName"><?=_lang("Card Holder")?></label>
                            <input class="form-control" id="cardholderName" data-checkout="cardholderName" type="text">
                        </div>

                        <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label for="cardNumber"><?=_lang("Card Number")?></label>
                                    <input class="form-control" type="text" id="cardNumber" data-checkout="cardNumber"
                                           onselectstart="return false" onpaste="return false"
                                           oncopy="return false" oncut="return false"
                                           ondrag="return false" ondrop="return false" autocomplete=off placeholder="1234 1234 1234 1234">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="securityCode"><?=_lang("CVV")?></label>
                                    <input class="form-control" id="securityCode" data-checkout="securityCode" type="text"
                                           onselectstart="return false" onpaste="return false"
                                           oncopy="return false" oncut="return false"
                                           ondrag="return false" ondrop="return false" placeholder="123" autocomplete=off>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for=""><?=_lang("Expiration Date")?></label>
                            <div class="row">
                                <div class="col-sm-4 col-4 col-md-4 col-lg-4 col-xs-4 no-margin no-padding">
                                    <input  class="form-control" type="text" placeholder="MM" id="cardExpirationMonth" data-checkout="cardExpirationMonth"
                                            onselectstart="return false" onpaste="return false"
                                            oncopy="return false" oncut="return false"
                                            ondrag="return false" ondrop="return false" autocomplete=off>
                                </div>
                                <div class="col-sm-4 col-4 col-md-4 col-lg-4 col-xs-4 no-margin no-padding">
                                    <input  class="form-control" type="text" placeholder="YY" id="cardExpirationYear" data-checkout="cardExpirationYear"
                                            onselectstart="return false" onpaste="return false"
                                            oncopy="return false" oncut="return false"
                                            ondrag="return false" ondrop="return false" autocomplete=off>
                                </div>
                            </div>

                        </div>

                        <div id="issuerInput" class="form-group">
                            <label for="issuer"><?=_lang("Issuer")?></label>
                            <select class="form-control" id="issuer" name="issuer" data-checkout="issuer"></select>
                        </div>
                        <div class="form-group">
                            <label for="installments"><?=_lang("Installments")?></label>
                            <select class="form-control" type="text" id="installments" name="installments"></select>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="transactionAmount" id="transactionAmount" value="<?=$details_subtotal?>" />
                            <input type="hidden" name="paymentMethodId" id="paymentMethodId" />
                            <input type="hidden" name="description" id="description" />
                            <br>
                            <button class="btn btn-primary" id="pay-now" type="submit"><?=Translate::sprintf("Pay now %s",array(Currency::parseCurrencyFormat($details_subtotal,$currency)))?></button>
                            <a class="btn btn-default" href="<?=$callback_error_url.'#error-mp-charge00'?>" id="button"><?=_lang("Cancel")?></a>
                            <br>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>

<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<script>

    window.Mercadopago.setPublishableKey('<?=ConfigManager::getValue('MERCADO_PAGO_KEY_ID')?>');
    window.Mercadopago.getIdentificationTypes();

    document.getElementById('cardNumber').addEventListener('change', guessPaymentMethod);

    function guessPaymentMethod(event) {
        let cardnumber = document.getElementById("cardNumber").value;
        if (cardnumber.length >= 6) {
            let bin = cardnumber.substring(0,6);
            window.Mercadopago.getPaymentMethod({
                "bin": bin
            }, setPaymentMethod);
        }
    };

    function setPaymentMethod(status, response) {
        if (status == 200) {
            let paymentMethod = response[0];
            document.getElementById('paymentMethodId').value = paymentMethod.id;

            getIssuers(paymentMethod.id);
        } else {
            alert(`payment method info error: ${response}`);
        }
    }

    function getIssuers(paymentMethodId) {
        window.Mercadopago.getIssuers(
            paymentMethodId,
            setIssuers
        );
    }

    function setIssuers(status, response) {
        if (status == 200) {
            let issuerSelect = document.getElementById('issuer');
            response.forEach( issuer => {
                let opt = document.createElement('option');
                opt.text = issuer.name;
                opt.value = issuer.id;
                issuerSelect.appendChild(opt);
            });

            getInstallments(
                document.getElementById('paymentMethodId').value,
                document.getElementById('transactionAmount').value,
                issuerSelect.value
            );
        } else {
            alert(`issuers method info error: ${response}`);
        }
    }

    function getInstallments(paymentMethodId, transactionAmount, issuerId){
        window.Mercadopago.getInstallments({
            "payment_method_id": paymentMethodId,
            "amount": parseFloat(transactionAmount),
            "issuer_id": parseInt(issuerId)
        }, setInstallments);
    }

    function setInstallments(status, response){
        if (status == 200) {
            document.getElementById('installments').options.length = 0;
            response[0].payer_costs.forEach( payerCost => {
                let opt = document.createElement('option');
                opt.text = payerCost.recommended_message;
                opt.value = payerCost.installments;
                document.getElementById('installments').appendChild(opt);
            });
        } else {
            alert(`installments method info error: ${response}`);
        }
    }

    doSubmit = false;
    document.getElementById('paymentForm').addEventListener('submit', getCardToken);
    function getCardToken(event){
        event.preventDefault();
        if(!doSubmit){
            let $form = document.getElementById('paymentForm');
            window.Mercadopago.createToken($form, setCardTokenAndPay);
            return false;
        }
    };

    function setCardTokenAndPay(status, response) {
        if (status == 200 || status == 201) {
            let form = document.getElementById('paymentForm');
            let card = document.createElement('input');
            card.setAttribute('name', 'token');
            card.setAttribute('type', 'hidden');
            card.setAttribute('value', response.id);
            form.appendChild(card);
            doSubmit=true;
            form.submit();
        } else {
            alert("Verify filled data!\n"+JSON.stringify(response, null, 4));
        }
    };

</script>

</body>
</html>

