

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content _config">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-6">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Store Options") ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form class="form" role="form">
                            <div class="form-group">
                                <label><?php echo Translate::sprint("Opening time enabled"); ?></label>
                                <select class="select2" id="OPENING_TIME_ENABLED">
                                    <option value="1" <?=ConfigManager::getValue("OPENING_TIME_ENABLED")==TRUE?"selected":""?>>Enabled</option>
                                    <option value="0" <?=ConfigManager::getValue("OPENING_TIME_ENABLED")==FALSE?"selected":""?>>Disabled</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>

            </div>
    </section>

</div>


<?php


$script = $this->load->view('store/backend/html/scripts/options-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>




