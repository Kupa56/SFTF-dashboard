<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<li class="<?php if ($uri_parent == "managePages") echo "active"; ?>">
    <a href="<?= admin_url("cms/managePages") ?>"><i class="mdi mdi-account-cog"></i>
        &nbsp;<span> <?= Translate::sprint("Manage Pages") ?></span></a>
</li>