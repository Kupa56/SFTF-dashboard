<?php


$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>


<?php if (GroupAccess::isGranted('payout')): ?>
    <li class="<?php if ($uri_m == "payout") echo "active"; ?>">
        <a href="<?= admin_url("payout/payouts") ?>"><i class="mdi mdi-cash-100"></i>
            &nbsp;<span> <?= Translate::sprint("Payouts") ?></span></a>
    </li>
<?php endif; ?>


