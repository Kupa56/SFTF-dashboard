<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<?php if (GroupAccess::isGranted('cms')) : ?>
   <li class="treeview <?php if ($uri_m == "cms") echo "active"; ?>">
        <a href="<?= admin_url("cms/managePages") ?>"><i class="mdi mdi-content-copy"></i> &nbsp;
            <span><?= Translate::sprint("CMS & Pages") ?></span>
            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
        </a>
        <ul class="treeview-menu">

            <li  class="<?php if ($uri_m == "cms" && $uri_parent == "managePages") echo "active"; ?>">
                <a href="<?= admin_url("cms/managePages") ?>"><i class="mdi mdi-content-copy"></i>
                    &nbsp;<?= Translate::sprint("Manage pages") ?></a></li>

            <li  class="<?php if ($uri_m == "cms" && $uri_parent == "manageMenu") echo "active"; ?>">
                <a href="<?= admin_url("cms/manageMenu") ?>"><i class="mdi mdi-menu"></i>
                    &nbsp;<?= Translate::sprint("Manage menu") ?></a></li>


        </ul>
    </li>
<?php endif;?>
