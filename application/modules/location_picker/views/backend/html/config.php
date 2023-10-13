

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
            <div class="col-sm-6 hidden">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Maps API") ?></b></h3>


                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form class="form" role="form">


                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Maps API"); ?></label>
                                   <select class="select2" id="op_maps_picker">
                                       <option value="0"><?=Translate::sprint("Select dashboard maps API")?></option>
                                       <option value="1" <?=ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==1?"selected":""?>>Here API</option>
                                       <option value="2" <?=ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==2?"selected":""?>>Google API</option>
                                   </select>
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-sm-6 maps-api here-maps-form <?=ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==1?"":"hidden"?>">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Here Maps API") ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form class="form" role="form">


                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Here maps app ID"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." name="LOCATION_PICKER_HERE_MAPS_APP_ID"
                                           id="LOCATION_PICKER_HERE_MAPS_APP_ID" value="<?= ConfigManager::getValue('LOCATION_PICKER_HERE_MAPS_APP_ID') ?>">
                                </div>

                                <div class="form-group">
                                    <label> <?= Translate::sprint("Here maps app Code"); ?> </label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           name="LOCATION_PICKER_HERE_MAPS_APP_CODE" id="LOCATION_PICKER_HERE_MAPS_APP_CODE"
                                           value="<?= ConfigManager::getValue('LOCATION_PICKER_HERE_MAPS_APP_CODE') ?>">
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-sm-6  maps-api google-maps-form <?=ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==2?"":"hidden"?>">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Google Maps API") ?></b></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form class="form" role="form">


                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Google maps app ID"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." name="MAPS_API_KEY"
                                           id="MAPS_API_KEY" value="<?= ConfigManager::getValue('MAPS_API_KEY') ?>">
                                </div>

                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Google places app ID"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." name="GOOGLE_PLACES_API_KEY"
                                           id="GOOGLE_PLACES_API_KEY" value="<?= ConfigManager::getValue('GOOGLE_PLACES_API_KEY') ?>">
                                </div>


                            </form>
                        </div>
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


$script = $this->load->view('location_picker/backend/html/scripts/config-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>




