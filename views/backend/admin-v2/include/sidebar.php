<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>

<ul class="sidebar-menu">

    <li id="menu-search" class="header menu-search hidden">
        <form autocomplete="off">
            <input type="text" placeholder="<?=_lang("Quick search...")?>" autocomplete="off" />
            <i class="mdi mdi-magnify"></i>
        </form>

    </li>

    <li class="header"><?= Translate::sprint("MENU", "") ?></li>
    <li class="<?php if ($uri_parent == "" || $uri_parent == "index") echo "active"; ?>">
        <a href="<?= admin_url("") ?>"><i class="mdi mdi-chart-line"></i> &nbsp;<span>
                        <?= Translate::sprint("Dashboard") ?></span></a>
    </li>

<?php

    $menuList = AdminTemplateManager::loadMenu();



    if(!empty($menuList)){
        foreach ($menuList as $menu){
            $this->load->view($menu['path']);
        }
    }

    ?>



<?php if (GroupAccess::isGranted('setting')) { ?>
        <li class="treeview <?php if(AdminTemplateManager::isSettingActive()) echo 'active'?>">
            <a href="<?= admin_url("application") ?>"><i class="mdi mdi-cog-outline"></i> &nbsp;
                <span> <?= Translate::sprint("Application") ?></span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">

            <?php

                $menuList = AdminTemplateManager::loadMenuSetting();
                if(!empty($menuList)){
                    foreach ($menuList as $menu){
                        $this->load->view($menu['path']);
                    }
                }

                ?>


            </ul>
        </li>
<?php } ?>



</ul>
          