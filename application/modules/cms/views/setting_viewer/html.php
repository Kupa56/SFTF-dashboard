<?php

$params = array();

?>

<div class="box-body webapp-config-block">

    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-danger errors hidden">
                <h4><?= _lang("Error!") ?></h4>
                <p><?= _lang("Please fill all required fields") ?> </p>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-3">
            <ul class="sub-navigation">
                <li><a href="#Home_page"><?= _lang("Home page") ?></a></li>
                <li><a href="#Contact_info"><?= _lang("Contact info") ?></a></li>
                <li><a href="#Mobile_Info"><?= _lang("Mobile info") ?></a></li>
                <li><a href="#Social_media"><?= _lang("Social media") ?></a></li>
            </ul>
        </div>

        <div class="col-sm-9">

            <div id="Home_page" class="sub-navigation-body">
                <h3 class="box-title">
                    <b><?= _lang("Home page") ?></b>
                </h3>
                <div class="form-group">
                    <label><?= _lang("Title") ?> <sup class="text-red">*</sup></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_home_title"
                           id="WEBAPP_home_title" value="<?= ConfigManager::getValue('WEBAPP_home_title') ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _lang("Short description") ?> <sup class="text-red">*</sup></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_home_short_description"
                           id="WEBAPP_home_short_description" value="<?= ConfigManager::getValue('WEBAPP_home_short_description') ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _lang("Description") ?> <sup class="text-red">*</sup></label>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_home_description"
                           id="WEBAPP_home_description" value="<?= ConfigManager::getValue('WEBAPP_home_description') ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _lang("Keywords") ?></label>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_home_keywords"
                           id="WEBAPP_home_keywords" value="<?= ConfigManager::getValue('WEBAPP_home_keywords') ?>">
                </div>
            </div>

            <div id="Contact_info" class="sub-navigation-body">

                <h3 class="box-title">
                    <b><?= _lang("Contact information") ?></b>
                </h3>

                <div class="form-group">
                    <label><?= _lang("Address") ?></label><br/>
                    <textarea placeholder="<?= Translate::sprint("Enter") ?> ..." class="form-control" name="WEBAPP_contact_address" id="WEBAPP_contact_address"><?= ConfigManager::getValue('WEBAPP_contact_address') ?></textarea>
                </div>

                <div class="form-group">
                    <label><?= _lang("Email") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_contact_email"
                           id="WEBAPP_contact_email" value="<?= ConfigManager::getValue('WEBAPP_contact_email') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Phone") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_contact_phone"
                           id="WEBAPP_contact_phone" value="<?= ConfigManager::getValue('WEBAPP_contact_phone') ?>" >
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <h3 class="box-title">
                            <b><?= _lang("Office location") ?></b>
                        </h3>
                        <div class="form-group">
                            <label><?= _lang("The Maps Embed URL") ?></label> - <a href="https://www.embedmymap.com/" target="_blank"><?=_lang("Generate embed google map")?></a><br/>
                            <input type="text" class="form-control"
                                   placeholder="<?= Translate::sprint("Enter") ?> ..."
                                   name="WEBAPP_contact_map_embed"
                                   id="WEBAPP_contact_map_embed" value="<?= ConfigManager::getValue('WEBAPP_contact_map_embed') ?>" >
                        </div>
                    </div>


                </div>
            </div>

            <div id="Mobile_Info" class="sub-navigation-body">

                <h3 class="box-title">
                    <b><?= _lang("About us") ?></b>
                </h3>

                <div class="form-group">
                    <label><?= _lang("About us content") ?></label><br/>
                    <textarea placeholder="<?= Translate::sprint("Enter") ?> ..." class="form-control" name="WEBAPP_about_us" id="WEBAPP_about_us"><?= ConfigManager::getValue('WEBAPP_about_us') ?></textarea>
                </div>

            </div>

            <div id="Social_media" class="sub-navigation-body">

                <h3 class="box-title">
                    <b><?= _lang("Social media") ?></b>
                </h3>
                <div class="form-group">
                    <label><?= _lang("Facebook") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_facebook"
                           id="WEBAPP_follow_facebook" value="<?= ConfigManager::getValue('WEBAPP_follow_facebook') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Twitter") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_twitter"
                           id="WEBAPP_follow_twitter" value="<?= ConfigManager::getValue('WEBAPP_follow_twitter') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Instagram") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_instagram"
                           id="WEBAPP_follow_instagram" value="<?= ConfigManager::getValue('WEBAPP_follow_instagram') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Telegram") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_telegram"
                           id="WEBAPP_follow_telegram" value="<?= ConfigManager::getValue('WEBAPP_follow_telegram') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Linkedin") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_linkedin"
                           id="WEBAPP_follow_linkedin" value="<?= ConfigManager::getValue('WEBAPP_follow_linkedin') ?>" >
                </div>

                <div class="form-group">
                    <label><?= _lang("Youtube") ?></label><br/>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="WEBAPP_follow_youtube"
                           id="WEBAPP_follow_youtube" value="<?= ConfigManager::getValue('WEBAPP_follow_youtube') ?>" >
                </div>

            </div>

        </div>

    </div>
</div>


<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary" id="btnSaveWebappConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('cms/setting_viewer/scripts/script', $params, TRUE);
AdminTemplateManager::addScript($script);

?>

