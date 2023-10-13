<?php

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$languages = Translate::getLangsCodes();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">

            <div class="col-sm-12">


                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">


                        <li class="active">
                            <a href="#payments_methods" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Payments methods")) ?></a>
                        </li>

                    <?php if (PaymentsProvider::isEnabled("paypal")): ?>
                        <li>
                            <a href="#paypal_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("PayPal")) ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("stripe")): ?>
                        <li>
                            <a href="#stripe_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Stripe")) ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("razorpay")): ?>
                        <li>
                            <a href="#razorpay_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Razorpay")) ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("flutterwave")): ?>
                            <li>
                                <a href="#flutterwave_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Flutterwave")) ?></a>
                            </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("hyperpay")): ?>
                            <li>
                                <a href="#hyperpay_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Hyperpay")) ?></a>
                            </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("paytm")): ?>
                            <li>
                                <a href="#paytm_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Paytm")) ?></a>
                            </li>
                    <?php endif; ?>


                    <?php if (PaymentsProvider::isEnabled("paystack")): ?>
                            <li>
                                <a href="#paystack_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("PayStack")) ?></a>
                            </li>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("mercadopago")): ?>
                            <li>
                                <a href="#mercadopago_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("MercadoPago")) ?></a>
                            </li>
                    <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("my-coolpay")): ?>
                            <li>
                                <a href="#my-coolpay_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("My-coolpay")) ?></a>
                            </li>
                        <?php endif; ?>


                        <?php if (PaymentsProvider::isEnabled("transferBank")): ?>
                            <li>
                                <a href="#transferBank_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Bank information")) ?></a>
                            </li>
                    <?php endif; ?>


                        <?php if (PaymentsProvider::isEnabled("wallet")): ?>
                            <li>
                                <a href="#wallet_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Wallet")) ?></a>
                            </li>
                        <?php endif; ?>


                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="payments_methods">

                            <div class="box-body">
                                <div class="col-sm-12 payment_methods">
                                <?php foreach (PaymentsProvider::getModules() as $payment): ?>
                                        <div>
                                            <label>
                                                <input type="checkbox" class="payment_method"
                                                           value="<?= $payment['id'] ?>" <?= PaymentsProvider::isEnabled($payment['id']) ? "checked" : "" ?>/>&nbsp;&nbsp;<?= $payment['id']==PaymentsProvider::APPLE_PAY ? _lang("Apple Pay")." <span class='text-red'>("._lang("Only on iOS").")</span>"  : _lang(PaymentsProvider::findKeyById($payment['id'])) ?>
                                            </label>
                                        </div>
                                <?php endforeach; ?>
                                </div>

                            </div>

                            <div class="box-footer">
                                <button type="button" class="btn  btn-primary" id="btnSave"><span
                                            class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                </button>
                            </div>


                        </div>

                    <?php if (PaymentsProvider::isEnabled("paypal")): ?>
                            <div class="tab-pane" id="paypal_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("PayPal Config")?></strong>

                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Client key and secret key ')?>  ? <a href="https://www.angelleye.com/how-to-create-paypal-app"> documentation </a>
                                    </sup>

                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="PAYPAL_CONFIG_DEV_MODE"
                                                            name="PAYPAL_CONFIG_DEV_MODE"
                                                            class="form-control select2 PAYPAL_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (PAYPAL_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Dev</option>';
                                                            echo '<option value="false" >Prod</option>';
                                                        } else {
                                                            echo '<option value="true"  >Dev</option>';
                                                            echo '<option value="false"  selected>Prod</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Client ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYPAL_CONFIG_CLIENT_ID"
                                                           id="PAYPAL_CONFIG_CLIENT_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?PAYPAL_CONFIG_CLIENT_ID:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYPAL_CONFIG_SECRET_ID"
                                                           id="PAYPAL_CONFIG_SECRET_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?PAYPAL_CONFIG_SECRET_ID:"*** Hidden ***" ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("stripe")): ?>
                            <div class="tab-pane" id="stripe_config">
                                <div class="box-body">

                                    <strong class="uppercase"><?=_lang("Stripe Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Public key and secret key ')?>  ? <a href="https://stripe.com/docs/keys"> documentation </a>
                                    </sup>

                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="STRIPE_CONFIG_DEV_MODE"
                                                            name="STRIPE_CONFIG_DEV_MODE"
                                                            class="form-control select2 STRIPE_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (STRIPE_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Publishable key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="STRIPE_PUBLISHABLE_KEY"
                                                           id="STRIPE_PUBLISHABLE_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?STRIPE_PUBLISHABLE_KEY:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="STRIPE_SECRET_KEY"
                                                           id="STRIPE_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?STRIPE_SECRET_KEY:"*** Hidden ***" ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("razorpay")): ?>
                            <div class="tab-pane" id="razorpay_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Razorpay Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://razorpay.com/docs/payment-gateway/dashboard-guide/settings/api-keys"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="RAZORPAY_CONFIG_DEV_MODE"
                                                            name="RAZORPAY_CONFIG_DEV_MODE"
                                                            class="form-control select2 RAZORPAY_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (RAZORPAY_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="RAZORPAY_KEY_ID"
                                                           id="RAZORPAY_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?RAZORPAY_KEY_ID:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="RAZORPAY_SECRET_KEY"
                                                           id="RAZORPAY_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?RAZORPAY_SECRET_KEY:"*** Hidden ***" ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("flutterwave")): ?>
                            <div class="tab-pane" id="flutterwave_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("flutterwave Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://razorpay.com/docs/payment-gateway/dashboard-guide/settings/api-keys"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="FLUTTERWAVE_CONFIG_DEV_MODE"
                                                            name="FLUTTERWAVE_CONFIG_DEV_MODE"
                                                            class="form-control select2 FLUTTERWAVE_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (FLUTTERWAVE_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="FLUTTERWAVE_KEY_ID"
                                                           id="FLUTTERWAVE_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?FLUTTERWAVE_KEY_ID:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="FLUTTERWAVE_SECRET_KEY"
                                                           id="FLUTTERWAVE_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?FLUTTERWAVE_SECRET_KEY:"*** Hidden ***" ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("hyperpay")): ?>
                            <div class="tab-pane" id="hyperpay_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Hyperpay Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://www.hyperpay.com/integration-guides/"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="HYPERPAY_CONFIG_DEV_MODE"
                                                            name="HYPERPAY_CONFIG_DEV_MODE"
                                                            class="form-control select2 HYPERPAY_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (HYPERPAY_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="HYPERPAY_KEY_ID"
                                                           id="HYPERPAY_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?HYPERPAY_KEY_ID:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="HYPERPAY_SECRET_KEY"
                                                           id="HYPERPAY_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?HYPERPAY_SECRET_KEY:"*** Hidden ***" ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("paytm")): ?>
                            <div class="tab-pane" id="paytm_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Paytm Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://dashboard.paytm.com/"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="PAYTM_CONFIG_DEV_MODE"
                                                            name="PAYTM_CONFIG_DEV_MODE"
                                                            class="form-control select2 PAYTM_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (PAYTM_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYTM_KEY_ID"
                                                           id="PAYTM_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?PAYTM_KEY_ID:"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYTM_SECRET_KEY"
                                                           id="PAYTM_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?PAYTM_SECRET_KEY:"*** Hidden ***" ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("paystack")): ?>
                            <div class="tab-pane" id="paystack_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Paystack Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://support.paystack.com/hc/en-us/articles/360009881600-Paystack-Test-Keys-Live-Keys-and-Webhooks"> documentation </a>
                                    </sup>
                                    <br>
                                    <span class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <strong><?=_lang('Accepted currencies')?></strong> : Ghana (GHS), Nigeria (NGN, USD), South Africa (ZAR)
                                    </span>

                                    <form id="form" role="form">

                                        <div class="row">
                                            <div class="col-sm-6 margin">
                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="PAYSTACK_CONFIG_DEV_MODE"
                                                            name="PAYSTACK_CONFIG_DEV_MODE"
                                                            class="form-control select2 PAYSTACK_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (ConfigManager::getValue('PAYSTACK_CONFIG_DEV_MODE') == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYSTACK_KEY_ID"
                                                           id="PAYSTACK_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('PAYSTACK_KEY_ID'):"*** Hidden ***" ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYSTACK_SECRET_KEY"
                                                           id="PAYSTACK_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('PAYSTACK_SECRET_KEY'):"*** Hidden ***" ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("my-coolpay")): ?>
                            <div class="tab-pane" id="my-coolpay_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("My-coolPay Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://documenter.getpostman.com/view/17178321/UV5ZCx8f#c59ff2b8-8899-4f18-83b7-8e16e68c74a2"> documentation </a>
                                    </sup>
                                    <br>
                                    <span class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <strong><?=_lang('Accepted currencies')?></strong> : XAF & EUR
                                    </span>

                                    <form id="form" role="form">

                                        <div class="row">
                                            <div class="col-sm-6 margin">
                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="PAYSTACK_CONFIG_DEV_MODE"
                                                            name="PAYSTACK_CONFIG_DEV_MODE"
                                                            class="form-control select2 MY_COOLPAY_CONFIG_DEV_MODE">
                                                        <?php
                                                        if (ConfigManager::getValue('MY_COOLPAY_CONFIG_DEV_MODE') == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MY_COOLPAY_KEY_ID"
                                                           id="MY_COOLPAY_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MY_COOLPAY_KEY_ID'):"*** Hidden ***" ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MY_COOLPAY_SECRET_KEY"
                                                           id="MY_COOLPAY_SECRET_KEY"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MY_COOLPAY_SECRET_KEY'):"*** Hidden ***" ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                        <?php endif; ?>


                        <?php if (PaymentsProvider::isEnabled("mercadopago")): ?>
                            <div class="tab-pane" id="mercadopago_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Mercado Pago Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://www.mercadopago.com.ar/developers/es/docs/credentials"> documentation </a>
                                    </sup>
                                    <br>
                                    <span class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <strong><?=_lang('Accepted currencies')?></strong> : ---
                                    </span>

                                    <form id="form" role="form">

                                        <div class="row">
                                            <div class="col-sm-6 margin">
                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="MERCADO_PAGO_CONFIG_DEV_MODE"
                                                            name="MERCADO_PAGO_CONFIG_DEV_MODE"
                                                            class="form-control select2 MERCADO_PAGO_CONFIG_DEV_MODE">
                                                    <?php
                                                        if (ConfigManager::getValue('MERCADO_PAGO_CONFIG_DEV_MODE') == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Public Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MERCADO_PAGO_KEY_ID"
                                                           id="MERCADO_PAGO_KEY_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MERCADO_PAGO_KEY_ID'):"*** Hidden ***" ?>">
                                                </div>


                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Access token") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MERCADO_PAGO_ACCESS_TOKEN"
                                                           id="MERCADO_PAGO_ACCESS_TOKEN"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MERCADO_PAGO_ACCESS_TOKEN'):"*** Hidden ***" ?>">
                                                </div>


                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Client ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MERCADO_PAGO_CLIENT_ID"
                                                           id="MERCADO_PAGO_CLIENT_ID"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MERCADO_PAGO_CLIENT_ID'):"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Client secret") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="MERCADO_PAGO_CLIENT_SECRET"
                                                           id="MERCADO_PAGO_CLIENT_SECRET"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('MERCADO_PAGO_CLIENT_SECRET'):"*** Hidden ***" ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>

                    <?php if (PaymentsProvider::isEnabled("transferBank")): ?>
                            <div class="tab-pane" id="transferBank_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Bank Information")?></strong>
                                    <br>
                                    <span><?=_lang("After customer select transfer bank as a payment, he will receive an email contains bank information")?></span>

                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Full name or company name") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="TRANSFER_BANK_NAME"
                                                           id="TRANSFER_BANK_NAME"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('TRANSFER_BANK_NAME'):"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("SWIFT / BIC code") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="TRANSFER_BANK_SWIFT"
                                                           id="TRANSFER_BANK_SWIFT"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('TRANSFER_BANK_SWIFT'):"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("IBAN / Account Number") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="TRANSFER_BANK_IBAN"
                                                           id="TRANSFER_BANK_IBAN"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('TRANSFER_BANK_IBAN'):"*** Hidden ***" ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Additional information") ?></label>
                                                    <textarea  class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="TRANSFER_BANK_DETAILS"
                                                               id="TRANSFER_BANK_DETAILS"><?= (!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue('TRANSFER_BANK_DETAILS'):"*** Hidden ***" ?></textarea>
                                                </div>


                                            </div>

                                        </div>


                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                    <?php endif; ?>


                        <?php if (PaymentsProvider::isEnabled("wallet")): ?>
                            <div class="tab-pane" id="wallet_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Wallet config")?></strong>

                                    <form id="form" role="form">

                                        <div class="row">
                                            <div class="col-sm-6 margin">
                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Top-up") ?></label><br>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="WALLET_TOP_UP_AMOUNTS"
                                                           id="WALLET_TOP_UP_AMOUNTS"
                                                           value="<?= (!ModulesChecker::isEnabled("demo"))?(ConfigManager::getValue('WALLET_TOP_UP_AMOUNTS')):"*** Hidden ***" ?>">
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                        <?php endif; ?>


                    </div>

                </div>

            </div>

            <div class="col-sm-6">
                <div class="box box-solid hidden">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Payment Config"); ?> </b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form id="form" role="form">

                                <div class="form-group">
                                    <label><?= Translate::sprint("Payment Currency") ?></label>
                                    <select id="PAYMENT_CURRENCY" name="PAYMENT_CURRENCY"
                                            class="form-control select2 PAYMENT_CURRENCY" disabled>
                                    <?php

                                        if (defined('PAYMENT_CURRENCY'))
                                            $def_key = PAYMENT_CURRENCY;
                                        else
                                            $def_key = DEFAULT_CURRENCY;

                                        foreach ($currencies as $key => $c) {
                                            if ($def_key == $c['code']) {
                                                echo '<option value="' . $c['code'] . '" selected>' . $c['name'] . ', ' . $c['code'] . '</option>';
                                            } else {
                                                echo '<option value="' . $c['code'] . '">' . $c['name'] . ', ' . $c['code'] . '</option>';
                                            }

                                        }

                                        ?>
                                    </select>
                                    <sub><i class="mdi mdi-information-outline"></i>
                                        <?= Translate::sprint("You should select default currency and supported for the PayPal or other methods") ?>
                                        <br>
                                        <a target="_blank"
                                           href="https://developer.paypal.com/docs/classic/api/currency_codes/">https://developer.paypal.com/docs/classic/api/currency_codes</a>
                                    </sub>
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary" id="btnSave"><span
                                    class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>
            </div>


        </div>

    </section>

</div>


<?php

$script = $this->load->view('backend/html/scripts/payment-setting-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>






