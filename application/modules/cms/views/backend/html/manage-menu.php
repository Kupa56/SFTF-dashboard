<?php


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="box  box-solid">
                    <div class="box-header">
                        <div class="box-title">
                            <b><?= Translate::sprint("Manage Main menu") ?></b>
                        </div>
                        <button class="pull-right btn btn-primary create-new-grp-menu"><i class="mdi mdi-plus"></i>&nbsp;&nbsp;<?=_lang("Create new menu")?></button>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="row" id="grp-menu-container">
                        <?php

                            $main_menus = $this->mCMS->getMenu();

                            foreach ($main_menus as $menu){
                                $data['menu'] = $menu;
                                $this->load->view('cms/backend/html/menu/options/menu_row',$data);
                            }

                            ?>

                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->


                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

$data1 = array();
$data1['pages'] = $pages;
$modal1 = $this->load->view("cms/backend/html/menu/modal-create-menu",$data1,TRUE);
AdminTemplateManager::addHtml($modal1);

$data0 = array();
$script = $this->load->view('cms/backend/html/menu/script', $data0, TRUE);
AdminTemplateManager::addScript($script);

?>

