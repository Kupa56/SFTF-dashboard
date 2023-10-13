<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<?php if (GroupAccess::isGranted('cf_manager')) : ?>
    <li  class="<?php if ($uri_m == "cf_manager" && $uri_parent == "my_cf_list") echo "active"; ?>">
        <a href="<?= admin_url("cf_manager/cf_list") ?>"><i class="mdi mdi-square-edit-outline "></i>
            &nbsp;<?= Translate::sprint("Checkout Fields") ?></a></li>
<?php endif; ?>
