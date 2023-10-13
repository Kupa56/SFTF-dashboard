<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<?php if(ModulesChecker::isEnabled('pack') && GroupAccess::isGranted("pack")): ?>
    <li class="<?php if ($uri_m == "pack") echo "active"; ?>">
        <a href="<?= admin_url("pack/pack_manager") ?>"><i class="mdi mdi-poll"></i>
            &nbsp;<span> <?= Translate::sprint("Subscription") ?></span>
        </a>
    </li>
<?php endif; ?>