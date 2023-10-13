<?php

$params = array();

?>

<div class="box-body uploader-block">
    <div class="row">

        <div class="col-sm-6">

            <div class="form-group">
                <label> <?php echo Translate::sprint("Select storage"); ?></label>
                <select id="APP_STORAGE" name="APP_STORAGE"
                        class="form-control select2 APP_STORAGE">
                    <option value="local" <?=ConfigManager::getValue("APP_STORAGE")=="local"?"selected":""?>>Local</option>
                    <option value="google_bucket" <?=ConfigManager::getValue("APP_STORAGE")=="google_bucket"?"selected":""?>>Google Bucket</option>
                </select>
            </div>

            <?php if(ConfigManager::getValue("APP_STORAGE")=="google_bucket"): ?>

                <div class="form-group">
                    <label> <?php echo Translate::sprint("Bucket name"); ?> <span class="text-red">*</span></label>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="APP_STORAGE_BUCKET_NAME"
                           id="APP_STORAGE_BUCKET_NAME" value="<?= ConfigManager::getValue('APP_STORAGE_BUCKET_NAME') ?>">
                </div>

                <div class="form-group">
                    <label> <?php echo Translate::sprint("Service Account file"); ?> (.json) <span class="text-red">*</span></label>
                    <input type="text" class="form-control"
                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                           name="APP_STORAGE_BUCKET_FILE"
                           id="APP_STORAGE_BUCKET_FILE" data-file="json" value="<?= ConfigManager::getValue('APP_STORAGE_BUCKET_FILE') ?>">
                </div>

            <?php endif;?>

        </div>


    </div>
</div>


<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary btnSaveStoreConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('uploader/setting_viewer/scripts/script', $params, TRUE);
AdminTemplateManager::addScript($script);

?>

