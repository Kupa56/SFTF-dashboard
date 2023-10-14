<?php

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$languages = Translate::getLangsCodes();

?>

<div class="box-body dashboard-block">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label><?= Translate::sprint("App name", "") ?> <sup
                            class="text-red">*</sup> </label>
                <input type="text" class="form-control" required="required"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="APP_NAME"
                       id="APP_NAME" value="<?= $config['APP_NAME'] ?>">
            </div>

            <div class="form-group required">

                

            <?php

                // if (!is_array(APP_LOGO))
                //     $images = json_decode(APP_LOGO, JSON_OBJECT_AS_ARRAY);
                // if (preg_match('#^([a-zA-Z0-9]+)$#', APP_LOGO)) {
                //     $images = array(APP_LOGO => APP_LOGO);
                // }

                // $imagesData = array();

                // if (count($images) > 0) {
                //     foreach ($images as $key => $value)
                //         $imagesData = _openDir($value);
                //     if (!empty($imagesData))
                //         $imagesData = array($imagesData);
                // }

                ?>


            <?php

                // $upload_plug = $this->uploader->plugin(array(
                //     "limit_key" => "aUvFiles",
                //     "token_key" => "SzsYUjEsS-4555",
                //     "limit" => 1,
                //     "cache" => $imagesData
                // ));

                //echo $upload_plug['html'];
                //AdminTemplateManager::addScript($upload_plug['script']);

                ?>
            </div>
           
        </div>
        
    </div>
</div>

<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary btnSaveDashboardConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
        </button>
    </div>
</div>


<?php


$data['config'] = $config;
//$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('setting/setting_viewer/scripts/dashboard-script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>
