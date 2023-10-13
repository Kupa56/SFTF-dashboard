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
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Upload new module") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                    <?php

                        $upload_plug = $this->uploader->plug_files_uploader(array(
                            "limit_key"     => "publishFiles",
                            "token_key"     => "SzYjES-4555",
                            "limit"         => 1,
                            "types"         => array("application/zip"),
                            "template_html"         => "modules_manager/plug_file_uploader/html",
                            "template_script"       => "modules_manager/plug_file_uploader/script",
                            "template_style"        => "modules_manager/plug_file_uploader/style",
                            "script_trigger_callback"        => "file_uploaded",
                        ));

                        echo $upload_plug['html'];
                        AdminTemplateManager::addScript($upload_plug['script']);

                        ?>

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


$script = $this->load->view('modules_manager/backend/scripts/add-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
