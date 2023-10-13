

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="box box-solid">


                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("APP Client APIs"); ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">

                            <div class="form-group">
                                <label><?php echo Translate::sprint("BASE_URL"); ?> </label>
                                <input type="text" class="form-control" required="required"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." value="<?= site_url() ?>"
                                       readonly>
                            </div>

                            <div class="form-group">
                                <label><?php echo Translate::sprint("BASE_URL_API"); ?> </label>
                                <input type="text" class="form-control" required="required"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                                       value="<?= site_url("api") ?>" readonly>
                            </div>
                            <div class="form-group hidden">
                                <label><?php echo Translate::sprint("CRYPTO_KEY"); ?> <span
                                            style="color: grey;font-size: 11px;"><BR>NB: <?php echo Translate::sprint("Copy_this_key_your_android_res", "Copy this key in your android resource file \"app_config.xml\""); ?></span></label>
                                <input type="text" class="form-control"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." value="<?= (!ModulesChecker::isEnabled("demo"))?CRYPTO_KEY:"*** Hidden ***" ?>"
                                       readonly>
                            </div>

                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
            </div>


            <div class="col-sm-6">
                <div class="box box-solid api_verification">


                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("App Licences") ?></b></h3>
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
                        <?php foreach (ApiUpdater::retrieveApiApps() as $key => $item): ?>
                            <?php if(ConfigManager::defined("API_".md5($item['itemId']))): ?>
                                    <div class="form-group">
                                        <label><?=Translate::sprintf("Generated API for %s",array($item['itemLabel']))?></label>
                                        <input data-id="<?="API_".md5($item['itemId'])?>" class="form-control" required="required" placeholder="<?=Translate::sprint("Enter")?> ..."  value="<?=(!ModulesChecker::isEnabled("demo"))?ConfigManager::getValue("API_".md5($item['itemId'])):"*** Hidden ***"?>" readonly>
                                    </div>
                            <?php else: ?>
                                    <div class="form-group">
                                        <label><?=Translate::sprintf("Purchase ID for %s",array($item['itemLabel']))?> <sup>*</sup> </label>
                                        <input style="width: 80%;display: inline" type="text" class="form-control _EVT_PID_<?=md5($item['itemId'])?>" id="<?=ConfigManager::getValue("EVT_PID_".md5($item['itemId']))?>"  required="required"  placeholder="<?=Translate::sprint("Enter")?> ..."  value="">
                                        <input type="hidden" class="form-control _item_EVT_PID_<?=md5($item['itemId'])?>" value="<?=$item['itemId']?>">
                                        <button style="width: 19%;" class="btn btn-primary enableApiBtn" data-id-key="<?="EVT_PID_".md5($item['itemId'])?>"><?=Translate::sprint("Enable API")?></button>
                                        <sub class="text-red"><?=Translate::sprintf("Put Purchase ID (%s) to generate API",array($item['itemLabel']))?></sub>
                                    </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>


    </section>

</div>


<?php

$data['config'] = $config;

$script = $this->load->view('setting/backend/html/scripts/api-script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>




