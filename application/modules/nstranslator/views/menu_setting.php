<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<?php if(GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE)): ?>
    <li class="<?php if ($uri_parent == "languages") echo "active"; ?>">
        <a href="<?= admin_url("nstranslator/languages") ?>"><i class="mdi mdi-translate"></i>
            &nbsp;<span> <?= Translate::sprint("Languages") ?></span></a>
    </li>

<?php endif; ?>