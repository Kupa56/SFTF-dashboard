<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Modules Manager") ?></b>
                        </div>

                        <a href="<?=admin_url("modules_manager/add")?>" class="btn btn-flat bg-primary pull-right">
                            <i class="mdi mdi-plus"></i>&nbsp;&nbsp;<?=Translate::sprint('Add New Module')?>
                        </a>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                        <?php

                                foreach ($modules as $key => $module){
                                    $data['module'] = $module;
                                    $this->load->view('modules_manager/backend/html/item-card',$data);
                                }

                            ?>

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php


$script = $this->load->view('modules_manager/backend/scripts/manage-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
