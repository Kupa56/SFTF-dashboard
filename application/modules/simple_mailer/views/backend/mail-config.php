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
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header" style="width : 100%;">
                        <div class="box-title">
                            <b><?= Translate::sprint("Mail config") ?></b>
                        </div>
                    </div>

                    <div class="box-body mailer-block">
                        <div class="row">

                            <div class="col-sm-6">

                                <div class="group">
                                    <div class="form-group">
                                        <label><?php echo Translate::sprint("Service Mailer"); ?> </label>
                                        <select id="MAILER_ESP_MODULE_ENABLED" name="MAILER_ESP_MODULE_ENABLED"
                                                class="form-control select2 MAILER_ESP_MODULE_ENABLED">
                                            <option value="<?= Simple_mailer::MAILER_LOCAL ?>" <?=ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED')==Simple_mailer::MAILER_LOCAL?"selected":""?>>Local Mail Service</option>
                                            <option value="<?= Simple_mailer::MAILER_SMTP ?>" <?=ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED')==Simple_mailer::MAILER_SMTP?"selected":""?>>Smtp (External service)</option>
                                            <option value="<?= Simple_mailer::MAILER_ESP_SENDGRID ?>" <?=ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED')==Simple_mailer::MAILER_ESP_SENDGRID?"selected":""?>>SendGrid (External service)</option>
                                            <option value="<?= Simple_mailer::MAILER_ESP_MAILJET ?>" <?=ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED')==Simple_mailer::MAILER_ESP_MAILJET?"selected":""?>>MailJet (External service)</option>
                                        </select>
                                    </div>
                                </div>

                            <?php if (ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_SMTP): ?>
                                    <div class="group mailer-group mailer-group-<?=Simple_mailer::MAILER_SMTP?>">
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SMTP Protocol"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                   name="SMTP_PROTOCOL"
                                                   id="SMTP_PROTOCOL" value="<?= ConfigManager::getValue('SMTP_PROTOCOL') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SMTP Host"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_HOST"
                                                   id="SMTP_HOST" value="<?= ConfigManager::getValue('SMTP_HOST') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SMTP Port"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_PORT"
                                                   id="SMTP_PORT" value="<?= ConfigManager::getValue('SMTP_PORT') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SMTP user"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_USER"
                                                   id="SMTP_USER" value="<?= ConfigManager::getValue('SMTP_USER') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SMTP pass"); ?></label>
                                            <input type="password" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_PASS"
                                                   id="SMTP_PASS" value="<?= ConfigManager::getValue('SMTP_PASS') ?>">
                                        </div>
                                    </div>
                            <?php elseif (ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_ESP_SENDGRID): ?>
                                    <div class="group mailer-group mailer-group-<?=Simple_mailer::MAILER_ESP_SENDGRID?>">
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("SendGrid Api key"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                   name="MAILER_EXTERNAL_SENDGRID_API_KEY"
                                                   id="MAILER_EXTERNAL_SENDGRID_API_KEY"
                                                   value="<?= !ModulesChecker::isEnabled("demo") ? ConfigManager::getValue('MAILER_EXTERNAL_SENDGRID_API_KEY') : "*** HIDDEN ***" ?>">
                                        </div>

                                        <div class="form-group">
                                            <p><?=_lang("Note: Make sure that you created Sender identity in your SendGrid account")?> <code><a href="">https://docs.sendgrid.com/ui/sending-email/sender-verification</a></code></p>
                                        </div>

                                        <div class="form-group">
                                            <a href="<?= site_url("simple_mailer/SendGridTest") ?>"
                                               class="linkAccess btn btn-primary" onclick="return false;" data-toggle="tooltip"
                                               title="<?= Translate::sprint("Test", "") ?>">
                                                <i class="mdi mdi-send"></i>&nbsp; <?=_lang("Test API")?>
                                            </a>
                                        </div>
                                    </div>

                            <?php elseif (ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_ESP_MAILGUN): ?>

                                    <div class="group mailer-group mailer-group-<?=Simple_mailer::MAILER_ESP_MAILGUN?>">
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("Mailgun Api key"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                   name="MAILER_EXTERNAL_MAILGUN_API_KEY"
                                                   id="MAILER_EXTERNAL_MAILGUN_API_KEY"
                                                   value="<?= !ModulesChecker::isEnabled("demo") ? ConfigManager::getValue('MAILER_EXTERNAL_MAILGUN_API_KEY') : "*** HIDDEN ***" ?>">
                                        </div>

                                        <div class="form-group">
                                            <p><?=_lang("Note: Make sure that you created Sender identity in your SendGrid account")?> <code><a href="">https://docs.sendgrid.com/ui/sending-email/sender-verification</a></code></p>
                                        </div>

                                        <div class="form-group">
                                            <a href="<?= site_url("simple_mailer/SendGridTest") ?>"
                                               class="linkAccess btn btn-primary" onclick="return false;" data-toggle="tooltip"
                                               title="<?= Translate::sprint("Test", "") ?>">
                                                <i class="mdi mdi-send"></i>&nbsp; <?=_lang("Test API")?>
                                            </a>
                                        </div>
                                    </div>

                            <?php elseif (ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_ESP_MAILJET): ?>

                                    <div class="group mailer-group mailer-group-<?=Simple_mailer::MAILER_ESP_MAILJET?>">
                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("MailJet Public Api key"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                   name="MAILER_EXTERNAL_MAILJET_API_KEY"
                                                   id="MAILER_EXTERNAL_MAILJET_API_KEY"
                                                   value="<?= !ModulesChecker::isEnabled("demo") ? ConfigManager::getValue('MAILER_EXTERNAL_MAILJET_API_KEY') : "*** HIDDEN ***" ?>">
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo Translate::sprint("MailJet secret key"); ?></label>
                                            <input type="text" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                   name="MAILER_EXTERNAL_MAILJET_SECRET_KEY"
                                                   id="MAILER_EXTERNAL_MAILJET_SECRET_KEY"
                                                   value="<?= !ModulesChecker::isEnabled("demo") ? ConfigManager::getValue('MAILER_EXTERNAL_MAILJET_SECRET_KEY') : "*** HIDDEN ***" ?>">
                                        </div>

                                        <div class="form-group">
                                            <p><?=_lang("Note: Make sure that you created Sender email in your MailJet account")?> <code><a href="">https://app.mailjet.com/account/sender</a></code></p>
                                        </div>

                                        <div class="form-group">
                                            <a href="<?= site_url("simple_mailer/MailJetTest") ?>"
                                               class="linkAccess btn btn-primary" onclick="return false;" data-toggle="tooltip"
                                               title="<?= Translate::sprint("Test", "") ?>">
                                                <i class="mdi mdi-send"></i>&nbsp; <?=_lang("Test API")?>
                                            </a>
                                        </div>
                                    </div>

                            <?php endif; ?>

                            </div>


                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label>  <?php echo Translate::sprint("Default_email", "Default email"); ?></label>
                                <?php

                                    $defEmail = ConfigManager::getValue('DEFAULT_EMAIL');
                                    if ($defEmail == "") {
                                        $defEmail = $this->mUserBrowser->getData("email");
                                    }

                                    ?>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           name="DEFAULT_EMAIL"
                                           id="DEFAULT_EMAIL" value="<?= $defEmail ?>">
                                </div>

                                <div class="form-group">

                                <?php

                                    $defEmail = ConfigManager::getValue('REPORT_EMAIL');
                                    if ($defEmail == "") {
                                        $defEmail = ConfigManager::getValue('DEFAULT_EMAIL');
                                    }

                                    ?>

                                    <label><?php echo Translate::sprint("Report email"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           name="REPORT_EMAIL"
                                           id="REPORT_EMAIL" value="<?= $defEmail ?>">
                                </div>


                                <div class="form-group">

                                <?php

                                    $defEmail = ConfigManager::getValue('NOREPLY_EMAIL');
                                    if ($defEmail == "") {
                                        $defEmail = ConfigManager::getValue('DEFAULT_EMAIL');
                                    }

                                    ?>

                                    <label><?php echo Translate::sprint("No-reply email"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." name="NOREPLY_EMAIL"
                                           id="NOREPLY_EMAIL" value="<?= $defEmail ?>">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="pull-right">
                            <button type="button" class="btn  btn-primary btnSaveMailerConfig"><span
                                        class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


</div>








<?php


$script = $this->load->view('simple_mailer/backend/scripts/mailer-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>




