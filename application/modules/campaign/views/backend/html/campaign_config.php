<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content campaign_config">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath . "/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-6">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Campaign config") ?></b></h3>
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
                                    <label><?php echo Translate::sprint("Target_raduis", "Target raduis"); ?>
                                        <sup>KM</sup></label>
                                    <input type="number" min="0" max="100" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." name="RADUIS_TRAGET"
                                           id="RADUIS_TRAGET" value="<?= ConfigManager::getValue('RADUIS_TRAGET') ?>">
                                </div>

                                <div class="form-group">
                                    <label> <?= Translate::sprint("Number max pushes per campaign", ""); ?> </label>
                                    <input type="number" min="0" max="1000" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           name="LIMIT_PUSHED_GUESTS_PER_CAMPAIGN" id="LIMIT_PUSHED_GUESTS_PER_CAMPAIGN"
                                           value="<?= ConfigManager::getValue('LIMIT_PUSHED_GUESTS_PER_CAMPAIGN') ?>">
                                </div>


                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Number_max_pushes_per_campaign_with_cronjob_in_every_execute", "Maximum number of pushes  per campaign with crontab in each run"); ?> </label>
                                    <input type="number" min="0" max="100" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           name="NBR_PUSHS_FOR_EVERY_TIME" id="NBR_PUSHS_FOR_EVERY_TIME"
                                           value="<?= ConfigManager::getValue('NBR_PUSHS_FOR_EVERY_TIME') ?>">
                                </div>

                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save", "Save"); ?>
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-sm-6">
                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Set up Cron Job") ?></b></h3>
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
                            <p style="color: grey;font-size: 14px;">
                              <?php
                                    echo Translate::sprint("Set this command in your cronjob") . " <BR><CODE> /usr/bin/php -q " . FCPATH . "cronjob.php</CODE>";
                                    echo '<br><i><u><a target="_blank" href="https://www.youtube.com/watch?v=YwpUjz1tMbA&t=152s">Tutorial Video</a></u></i>'
                                  ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        <?php if (ModulesChecker::isEnabled('bookmark')): ?>
                <div class="col-sm-6 hidden">

                    <div class="box box-solid ">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b><?= Translate::sprint("Notification config") ?></b></h3>
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
                                        <label>
                                            <?= Translate::sprint("Notification agreement") ?><br>
                                            <span style="color: grey;font-size: 11px;"><?= _lang("By using this option, will be able to ask users if they want receive notification") ?></span>
                                        </label>

                                        <select id="_NOTIFICATION_AGREEMENT_USE" name="PUSH_CAMPAIGNS_WITH_CRON"
                                                class="form-control select2 _NOTIFICATION_AGREEMENT_USE">
                                        <?php

                                            if (ConfigManager::getValue('_NOTIFICATION_AGREEMENT_USE')) {
                                                echo '<option value="true" selected>true</option>';
                                                echo '<option value="false" >false</option>';
                                            } else {
                                                echo '<option value="true"  >true</option>';
                                                echo '<option value="false"  selected>false</option>';
                                            }

                                            ?>
                                        </select>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="button" class="btn  btn-primary btnSave"><span
                                        class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save", "Save"); ?>
                            </button>
                        </div>
                    </div>

                </div>
        <?php endif; ?>
    </section>

</div>


<?php


$script = $this->load->view('campaign/backend/html/scripts/campaign-config-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>




