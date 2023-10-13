<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>



<?php if (GroupAccess::isGranted('nsbanner')): ?>

    <li class="treeview <?php if ($uri_m == "nsbanner") echo "active"; ?>">
        <a href="<?= admin_url("nsbanner/all") ?>"><i class="mdi mdi-image-multiple"></i> &nbsp;
            <span><?= Translate::sprint("Mobile Slider") ?> </span>
            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>

        </a>


        <ul  class="treeview-menu">

            <li class="<?php if ($uri_parent == "all") echo "active"; ?>">
                <a href="<?= admin_url("nsbanner/all") ?>"><i class="mdi mdi-image-multiple"></i>
                    &nbsp;<span> <?= Translate::sprint("All sliders") ?></span></a>
            </li>

        <?php if (GroupAccess::isGranted('nsbanner', NS_BANNER_GRP_ACTION_ADD)): ?>
                <li class="<?php if ($uri_parent == "add") echo "active"; ?>">
                    <a href="<?= admin_url("nsbanner/add") ?>"><i class="mdi mdi-plus"></i>
                        &nbsp;<span> <?= Translate::sprint("Add new") ?></span></a>
                </li>
        <?php endif; ?>



        </ul>



    </li>


<?php endif; ?>
