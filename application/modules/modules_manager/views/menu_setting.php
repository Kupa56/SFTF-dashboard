<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<?php if(GroupAccess::isGranted('modules_manager',MANAGE_MODULES)): ?>
    <li class="<?php if ($uri_parent == "manage") echo "active"; ?>">
        <a href="<?= admin_url("modules_manager/manage") ?>"><i class="mdi mdi-power-plug"></i>
            &nbsp;<span> <?= Translate::sprint("Modules Manager") ?></span></a>
    </li>
<?php endif; ?>