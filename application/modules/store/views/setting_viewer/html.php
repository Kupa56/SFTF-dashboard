<?php

$params = array();

?>

<div class="box-body store-block">
    <div class="row">
        <div class="col-sm-6">

            <div class="form-group">
                <label><?php echo Translate::sprint("Maps api key"); ?> <sup>*</sup></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="MAPS_API_KEY"
                       id="MAPS_API_KEY" value="<?= $config['MAPS_API_KEY'] ?>">
            </div>

            <div class="form-group">
                <label><input type="checkbox"
                              placeholder="<?= Translate::sprint("Enter") ?> ..."
                              name="autoDetectLocation"
                              id="autoDetectLocation" <?=ConfigManager::getValue("SETTING_AUTO_LOC_DETECT")?"checked":""?>  />&nbsp;&nbsp;<?=_lang("Auto location detect")?> </label>
                <input type="hidden" class="form-control"
                       name="SETTING_AUTO_LOC_DETECT"
                       id="SETTING_AUTO_LOC_DETECT" value="<?=ConfigManager::getValue("SETTING_AUTO_LOC_DETECT")?>" />
            </div>

            <div class="storeSettingLocationContainer <?=ConfigManager::getValue("SETTING_AUTO_LOC_DETECT")?"hidden":""?>">
                <?php
                $map = LocationManager::plug_pick_location(array(
                    'lat'=>  ConfigManager::getValue("SETTING_AUTO_LOC_DETECT")?-1:ConfigManager::getValue('MAP_DEFAULT_LATITUDE'),
                    'lng'=>ConfigManager::getValue("SETTING_AUTO_LOC_DETECT")?-1:ConfigManager::getValue('MAP_DEFAULT_LONGITUDE'),
                    'address'=>''
                ),array(
                    'lat'=>TRUE,
                    'lng'=>TRUE,
                    'address'=>FALSE
                ));

                echo $map['html'];
                AdminTemplateManager::addScript($map['script']);
                $params['location_fields_id'] = $map['fields_id'];

                ?>
             </div>


        </div>

        <div class="col-sm-6">

            <div class="form-group">
                <label> <?php echo Translate::sprint("Date format"); ?></label>
                <select id="DATE_FORMAT" name="DATE_FORMAT"
                        class="form-control select2 DATE_FORMAT">
                    <option value="24" <?=ConfigManager::getValue("DATE_FORMAT")=="24"?"selected":""?>>24H format</option>
                    <option value="12" <?=ConfigManager::getValue("DATE_FORMAT")=="12"?"selected":""?>>12H format</option>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("Opening time enabled"); ?></label>
                <select class="select2 form-control" id="OPENING_TIME_ENABLED" name="OPENING_TIME_ENABLED">
                    <option value="1" <?=ConfigManager::getValue("OPENING_TIME_ENABLED")==TRUE?"selected":""?>><?=_lang("Enabled")?></option>
                    <option value="0" <?=ConfigManager::getValue("OPENING_TIME_ENABLED")==FALSE?"selected":""?>><?=_lang("Disabled")?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("Anything required approval to be published"); ?></label>
                <select class="select2 form-control" id="ANYTHINGS_APPROVAL" name="ANYTHINGS_APPROVAL">
                    <option value="1" <?= ConfigManager::getValue("ANYTHINGS_APPROVAL") ?"selected":""?>><?=_lang("Yes, Require approval")?></option>
                    <option value="0" <?= !ConfigManager::getValue("ANYTHINGS_APPROVAL") ?"selected":""?>><?=_lang("Publish automatically")?></option>
                </select>
            </div>



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


$script = $this->load->view('store/setting_viewer/scripts/script', $params, TRUE);
AdminTemplateManager::addScript($script);

?>

