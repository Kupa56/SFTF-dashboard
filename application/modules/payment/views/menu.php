<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


$unpaid = $this->mPaymentModel->getUnpaidInvoicesCount();

?>





<?php if (GroupAccess::isGranted('payment') && GroupAccess::isGranted('payment', CONFIG_PAYMENT)): ?>

<li class="treeview <?php if ($uri_m == "payment") echo "active"; ?>">
    <a href="<?= admin_url("payment/billing") ?>"><i class="mdi mdi-receipt"></i> &nbsp;
        <span><?= _lang("Payment") ?> </span>
        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>

    </a>


    <ul  class="treeview-menu">


    <?php if (GroupAccess::isGranted('payment', CONFIG_PAYMENT)): ?>
            <li class="<?php if ($uri_parent == "invoices") echo "active"; ?>">
                <a href="<?= admin_url("payment/invoices") ?>"><i class="mdi mdi-receipt"></i>
                    &nbsp;<span> <?= _lang("Invoices") ?></span></a>
            </li>
    <?php endif; ?>


    <?php if (GroupAccess::isGranted('payment', DISPLAY_LIST_TRANSACTIONS)): ?>
            <li class="hidden <?php if ($uri_parent == "transactions") echo "active"; ?>">
                <a href="<?= admin_url("payment/transactions") ?>"><i class="mdi mdi-credit-card"></i>
                    &nbsp;<span> <?= _lang("Transactions") ?></span></a>
            </li>
    <?php endif; ?>


    <?php if (GroupAccess::isGranted('payment', CONFIG_PAYMENT)): ?>
        <li class="<?php if ($uri_parent == "payment_settings") echo "active"; ?>">
            <a href="<?= admin_url("payment/payment_settings") ?>"><i class="mdi  mdi-cog-outline"></i>
                &nbsp;<span> <?= _lang("Payment config") ?></span></a>
        </li>
    <?php endif; ?>

        <?php if (GroupAccess::isGranted('payment', MANAGE_TAXES)): ?>
            <li class="<?php if ($uri_parent == "taxes") echo "active"; ?>">
                <a href="<?= admin_url("payment/taxes") ?>"><i class="mdi  mdi-bank"></i>
                    &nbsp;<span> <?= _lang("Manage Taxes") ?></span></a>
            </li>
        <?php endif; ?>


        <?php if (GroupAccess::isGranted('digital_wallet', DIGITAL_WALLET_SEND_RECEIVE)): ?>
            <li class="<?php if ($uri_parent == "sendDigitalMoney") echo "active"; ?>">
                <a href="<?= admin_url("digital_wallet/manageWallet") ?>"><i class="mdi mdi-send"></i>
                    &nbsp;<span> <?= _lang("Manage Wallet") ?></span></a>
            </li>
        <?php endif; ?>


    </ul>



</li>
<?php else: ?>


<li class="treeview <?php if ($uri_m == "payment") echo "active"; ?>">
    <a href="<?= admin_url("payment/billing") ?>"><i class="mdi mdi-receipt"></i> &nbsp;
        <span><?= _lang("Payment") ?> </span>
        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
    </a>

    <ul class="treeview-menu">

        <li class="treeview <?php if ($uri_m == "payment" and $uri_parent=="billing") echo "active"; ?>">
            <a href="<?= admin_url("payment/billing") ?>"><i class="mdi mdi-receipt"></i> &nbsp;
                <span><?= _lang("Billing") ?> </span>
                <?php if ($unpaid > 0): ?>
                    <span class="pull-right-container">
                                      <small class="badge pull-right bg-yellow"><?= $unpaid ?></small>
                                    </span>
                <?php endif; ?>
            </a>
        </li>

        <?php if (GroupAccess::isGranted('digital_wallet', DIGITAL_WALLET_SEND_RECEIVE)): ?>
            <li class="<?php if ($uri_parent == "sendDigitalMoney") echo "active"; ?>">
                <a href="<?= admin_url("digital_wallet/sendDigitalMoney") ?>"><i class="mdi mdi-send"></i>
                    &nbsp;<span> <?= _lang("Send Money") ?></span></a>
            </li>
        <?php endif; ?>

    </ul>
</li>




<?php endif; ?>
