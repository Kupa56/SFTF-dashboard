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

                <label><?= _lang("Logo") ?> <sup class="text-red">*</sup> </label>

            <?php

                if (!is_array(APP_LOGO))
                    $images = json_decode(APP_LOGO, JSON_OBJECT_AS_ARRAY);
                if (preg_match('#^([a-zA-Z0-9]+)$#', APP_LOGO)) {
                    $images = array(APP_LOGO => APP_LOGO);
                }

                $imagesData = array();

                if (count($images) > 0) {
                    foreach ($images as $key => $value)
                        $imagesData = _openDir($value);
                    if (!empty($imagesData))
                        $imagesData = array($imagesData);
                }

                ?>


            <?php

                $upload_plug = $this->uploader->plugin(array(
                    "limit_key" => "aUvFiles",
                    "token_key" => "SzsYUjEsS-4555",
                    "limit" => 1,
                    "cache" => $imagesData
                ));

                echo $upload_plug['html'];
                AdminTemplateManager::addScript($upload_plug['script']);

                ?>
            </div>
            <div class="form-group">
                <label><?php echo Translate::sprint("Dashboard analytics"); ?>
                    <sup>*</sup></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="DASHBOARD_ANALYTICS" id="DASHBOARD_ANALYTICS"
                       value="<?= $config['DASHBOARD_ANALYTICS'] ?>">
            </div>
            <div class="form-group">
                <label><?php echo Translate::sprint("Dashboard Color"); ?>   </label>
                <input type="text" class="form-control colorpicker1"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="DASHBOARD_COLOR" id="DASHBOARD_COLOR"
                       value="<?= $config['DASHBOARD_COLOR'] ?>">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group hidden">
                <label><?php echo Translate::sprint("Enable default front-end"); ?>   </label>
                <select id="ENABLE_FRONT_END" name="ENABLE_FRONT_END"
                        class="form-control select2 ENABLE_FRONT_END">
                <?php
                    if ($config['ENABLE_FRONT_END']) {
                        echo '<option value="true" selected>true</option>';
                        echo '<option value="false" >false</option>';
                    } else {
                        echo '<option value="true"  >true</option>';
                        echo '<option value="false"  selected>false</option>';
                    }
                    ?>
                </select>

            </div>
            <div class="form-group hidden">
                <div class="row">
                    <div class="col-sm-6">
                        <label><?php echo Translate::sprint("Upload limitation"); ?>
                            <sup>*</sup>
                            <span style="color: grey;font-size: 11px;"><?= Translate::sprint("Number uploaded images per stores & events") ?></span></label>

                        <input type="text" class="form-control"
                               placeholder="<?= Translate::sprint("Enter") ?> ..."
                               name="IMAGES_LIMITATION" id="IMAGES_LIMITATION"
                               value="<?= $config['IMAGES_LIMITATION'] ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label><?php echo Translate::sprint("Default_language"); ?>
                    <sup>*</sup></label>
                <select id="DEFAULT_LANG" name="DEFAULT_LANG"
                        class="form-control select2 DEFAULT_LANG">
                    <option value='0'><?=_lang("-- Languages")?></option>
                <?php

                    foreach ($languages as $key => $lng) {
                        if ($config['DEFAULT_LANG']
                            == $key) {
                            echo '<option value="' . $key . '" selected>' . $lng['name'] . '</option>';
                        } else {
                            echo '<option value="' . $key . '">' . $lng['name'] . '</option>';
                        }

                    }

                    ?>
                </select>

            </div>
            <div class="form-group">
                <label><?php echo Translate::sprint("Number items per page", ""); ?>
                    <sup>*</sup></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="NO_OF_ITEMS_PER_PAGE" id="NO_OF_ITEMS_PER_PAGE"
                       value="<?= $config['NO_OF_ITEMS_PER_PAGE'] ?>">
            </div>

        </div>
    </div>