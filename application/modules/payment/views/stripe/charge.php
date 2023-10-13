<?php
$this->load->view("payment/client_view/header-client");
?>


<!-- The Styling File -->
<style>

    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

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

                <form action="<?=site_url("payment/stripe_api/create_payment_with_stripe")?>" method="post" id="payment-form">
                    <div class="form-row">
                        <h1 for="card-element"><?=_lang("Credit or debit card")?></h1>
                        <p><?=_lang("Pay with credit or debit card by using stripe.com")?></p><br>
                        <div id="card-element">
                            <!-- a Stripe Element will be inserted here. -->
                        </div>
                        <!-- Used to display form errors -->
                        <div id="card-errors"></div>
                    </div>
                    <button class="btn btn-primary" id="pay-now"><?=Translate::sprintf("Pay now %s",array(Modules::run("payment/Stripe_api/getAmount")))?></button>

                </form>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>

</section>
<!-- The needed JS files -->
<!-- JQUERY File -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>
<!-- Your JS File -->
<script>



    // Stripe API Key
    var stripe = Stripe('<?=STRIPE_PUBLISHABLE_KEY?>');
    var elements = stripe.elements();
    // Custom Styling
    var style = {
        base: {
            color: '#32325d',
            lineHeight: '24px',
            fontFamily: '"Montserrat", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
    // Create an instance of the card Element
    var card = elements.create('card', {style: style});
    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');
    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    // Handle form submission
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {

        var pay_btn = document.getElementById('pay-now');
        pay_btn.disabled = true;

        event.preventDefault();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;

                pay_btn.disabled = false;
            } else {
                stripeTokenHandler(result.token);
            }
        });
    });
    // Send Stripe Token to Server
    function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        // Add Stripe Token to hidden input
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        // Submit form
        form.submit();
    }



</script>

<script>

    var NSTemplateUIAnimation = {

        button: {

            set loading(selector){
                var text  = selector.text().trim();
                selector.attr("disabled",true);
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.html("<i class=\"btn-saving-cart fa fa-check\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
                selector.addClass('bg-green');
                selector.attr("disabled",true);
            },
            set default(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.attr("disabled",false);
            },

            // selector.html('<i class="btn-saving-cart fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Mail Sent")?>&nbsp;&nbsp;');
        },

        buttonWithIcon: {

            set loading(selector){
                var text  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },
            set default(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },

        },


    };

</script>

</body>
</html>

