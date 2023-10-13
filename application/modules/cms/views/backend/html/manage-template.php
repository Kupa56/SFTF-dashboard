<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Manage Template") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body webapp-template-block">

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
                                    <li><a href="#Template"><?= _lang("Template") ?></a></li>

                                <?php foreach ($config_schema as $key => $value): ?>
                                        <li><a href="#<?= str_replace(' ', '', $key) ?>"><?= _lang($key) ?></a></li>
                                <?php endforeach; ?>

                                    <li><a href="#Custom_Css"><?= _lang("Custom CSS") ?></a></li>
                                    <li><a href="#Custom_Javascript"><?= _lang("Custom Javascript") ?></a></li>


                                <?php

                                    //ability to update if needed
                                    $config = TemplateUtils::getCurrentTemplate();
                                    $newVersion = $config['Version'];
                                    $prefix = $config['templateId']."_tpl";
                                    $oldVersion = ConfigManager::getValue($prefix."_Version");
                                    if (version_compare($newVersion, $oldVersion, '>')) {
                                        //do update here...
                                        echo "<li><strong><a href=\"".admin_url("cms/updateTemplate")."\"><i class='mdi mdi-information'></i>&nbsp;&nbsp;".Translate::sprintf("New update (%s)",array($newVersion))."</a></strong></li>";
                                    }
                                    ?>

                                    <li><a href="<?=admin_url("cms/recompileTranslate")?>"><?=_lang("Recompile translate file")?></a></li>
                                </ul>
                            </div>

                            <div class="col-sm-9">

                                <div id="Template" class="sub-navigation-body">
                                    <h3 class="box-title">
                                        <b><?= _lang("Template Selector") ?></b>
                                    </h3>
                                    <ul class="template-selector">
                                    <?php foreach ($templates as $template): ?>
                                            <li class="<?= $template['active'] == 1 ? "active" : "" ?>"
                                                data-id="<?= $template['templateId'] ?>">
                                                <img src="<?= $template['Image'] ?>"/>
                                                <strong><?= $template['templateName'] ?></strong>
                                                <i><?= $template['Description'] ?></i>
                                                <i><?= $template['Version'] ?></i>
                                            </li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <div class="form-group">
                                        <input type="hidden" class="form-control" name="DEFAULT_TEMPLATE"
                                               id="DEFAULT_TEMPLATE"
                                               value="<?= ConfigManager::getValue('DEFAULT_TEMPLATE') ?>" required>
                                        <input type="hidden" class="form-control" name="FRONTEND_TEMPLATE_NAME"
                                               id="FRONTEND_TEMPLATE_NAME"
                                               value="<?= ConfigManager::getValue('DEFAULT_TEMPLATE') ?>" required>
                                    </div>
                                </div>

                                <!-- Custom Blocks -->
                            <?php foreach ($config_schema as $key => $value): ?>

                                    <div id="<?= str_replace(' ', '', $key) ?>" class="sub-navigation-body">
                                        <h3 class="box-title">
                                            <b><?= ucfirst(_lang($key)) ?></b>
                                        </h3>

                                    <?php


                                        if(isset($value['path'])){
                                            $path = Path::getPath(array(
                                                    'views',
                                                'frontend',
                                                $current['templateId'],
                                                $value['path'].'.php'
                                            ));
                                            if(file_exists($path)){
                                                $this->load->view('frontend/'.$current['templateId'].'/'.$value['path']);
                                            }
                                            continue;
                                        }


                                        ?>
                                        <div class="row">
                                            <div class="col-sm-6">
                                            <?php if(is_array($value) && TemplateUtils::hasRadio($value)
                                                    && !TemplateUtils::hasTreeObject($value)): ?>
                                                    <div class="form-group">

                                                    <?php

                                                            $willChecked =  FALSE;
                                                            foreach ($value as $field){
                                                                if(ConfigManager::getValue($prefix.'_' . $key) == $field ){
                                                                    $willChecked = TRUE;
                                                                }
                                                            }

                                                            if($willChecked == FALSE){
                                                                $first_elem =  array_values($value)[0]; // Outputs: Apple
                                                                ConfigManager::setValue($prefix.'_' . $key,$first_elem);
                                                            }

                                                        ?>
                                                    <?php foreach ($value as $field): ?>
                                                                <label>
                                                                    <input type="radio" class="form-check" placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                                           name="<?=$prefix?>_<?= $key ?>"
                                                                           id="<?=$prefix?>_<?= $key ?>"
                                                                           value="<?=$field?>" <?=(ConfigManager::getValue($prefix.'_' . $key)==$field?"checked":"")?>/>
                                                                    <?= ucfirst(_lang($field)) ?>
                                                                </label><br/>
                                                    <?php endforeach; ?>
                                                    </div>
                                            <?php else: ?>
                                                <?php foreach ($value as $fk => $field): ?>

                                                    <?php if (is_array($field)): ?>
                                                            <div class="form-group">
                                                                <label><?= ucfirst(_lang($fk)) ?> <sup class="text-red">*</sup></label><br/>
                                                                <select class="form-control <?=$prefix?>_<?= $key ?>_<?= $fk ?>"
                                                                        id="<?=$prefix?>_<?= $key ?>_<?= $fk ?>"
                                                                        name="<?=$prefix?>_<?= $key ?>_<?= $fk ?>">
                                                                <?php foreach ($field as $fk2 => $option): ?>
                                                                        <option value="<?= $option ?>" <?=ConfigManager::getValue($prefix.'_' . $key . '_' . $fk)==$option?"selected":""?>><?= $option ?></option>
                                                                <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                    <?php else: ?>
                                                            <div class="form-group">
                                                                <label><?= ucfirst(_lang($fk)) ?> <?=($field!="")?"<sup
                                                                        class=\"text-red\">*</sup>":""?></label><br/>
                                                                <input type="text"
                                                                       class="form-control <?= preg_match("#color#", $fk) ? "colorpicker1" : "" ?>"
                                                                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                                       name="<?=$prefix?>_<?= $key ?>_<?= $fk ?>"
                                                                       id="<?=$prefix?>_<?= $key ?>_<?= $fk ?>"
                                                                       value="<?= ConfigManager::getValue($prefix.'_' . $key . '_' . $fk)==""?$field:ConfigManager::getValue($prefix.'_' . $key . '_' . $fk) ?>"
                                                                    <?=($field!="")?"required":""?>>
                                                            </div>

                                                    <?php endif; ?>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                            <?php endforeach; ?>
                                <!-- End Custom Blocks -->

                                <div id="Custom_Css" class="sub-navigation-body">
                                    <h3 class="box-title">
                                        <b><?=_lang("Custom CSS")?></b>
                                    </h3>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <textarea rows="20" class="form-control"
                                                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                       name="<?=$prefix?>_customCss"
                                                       id="<?=$prefix?>_customCss"><?= ConfigManager::getValue($prefix.'_customCss') ?></textarea>
                                                <sub class="text-blue"><?=_lang("Custom css will be placed inside head tag for all pages (do not add style tags)")?></sub>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="Custom_Javascript" class="sub-navigation-body">
                                    <h3 class="box-title">
                                        <b><?=_lang("Custom Javascript")?></b>
                                    </h3>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <textarea rows="20" class="form-control"
                                                          placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                          name="<?=$prefix?>_customJavascript"
                                                          id="<?=$prefix?>_customJavascript"><?= ConfigManager::getValue($prefix.'_customJavascript') ?></textarea>
                                                <sub class="text-blue"><?=_lang("Custom javascript will be placed in the end of body tag (do not add javascript tags)")?></sub>
                                            </div>
                                        </div>
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


                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php

$params = array();
$script = $this->load->view('cms/backend/scripts/manage-template-script', $params, TRUE);
AdminTemplateManager::addScript($script);


?>
