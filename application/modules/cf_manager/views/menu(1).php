<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<?php if (GroupAccess::isGranted('cf_manager')) : ?>
    <li class="treeview <?php if ($uri_m == "cf_manager") echo "active"; ?>">
        <a href="<?= admin_url("cf_manager/cf_list") ?>"><i class="mdi mdi-square-edit-outline "></i> &nbsp;
            <span><?= Translate::sprint("Checkout Fields") ?></span>
            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
        </a>

        <ul class="treeview-menu">


            <li  class="<?php if ($uri_m == "cf_manager" && $uri_parent == "my_cf_list") echo "active"; ?>">
                <a href="<?= admin_url("cf_manager/cf_list") ?>"><i class="mdi mdi-format-list-bulleted"></i>
                    &nbsp;<?= Translate::sprint("Checkout Fields") ?></a></li>

            <li  class="<?php if ($uri_m == "cf_manager" && $uri_parent == "add") echo "active"; ?>">
                <a href="<?= admin_url("cf_manager/add") ?>"><i class="mdi mdi-plus-box  "></i>
                    &nbsp;<?= Translate::sprint("Add new") ?></a></li>

        </ul>
    </li>
<?php endif; ?>
