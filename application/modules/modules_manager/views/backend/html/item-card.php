<div class="col-sm-12 col-md-6 module_item module_item_<?=$module['module_name']?>">
    <div class="box box-solid">
        <div class="box-body">
            <h4 class="uppercase" style="background-color:#f7f7f7; font-size: 18px; padding: 7px 10px; margin-top: 0;">
                <?=$module['detail']['name']?>
            </h4>
            <div class="media">
                <div class="media-left">
                    <a href="https://www.creative-tim.com/product/now-ui-kit-pro?affiliate_id=97705" class="ad-click-event">
                        <img src="<?=$module['detail']['icon']?>" alt="<?=$module['detail']['name']?>" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px rgba(0,0,0,.15);border: 1px solid #eeeeee;">
                    </a>
                </div>
                <div class="media-body">
                    <div class="clearfix">
                        <p><?=$module['detail']['description']?></p>


                        <u><?=Translate::sprint("Version")?></u>:
                    <?php if ($module["_installed"] == 1 && $module["version_code"] < $module["detail"]['version_code']): ?>
                            <i class="mdi mdi-information-outline text-yellow"></i>
                    <?php endif; ?><?=$module['detail']['version_name']?> (<?=$module['detail']['version_code']?>)
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer ">

            <?php if(isset($module['detail']['help'])): ?>
                    <a data-toggle="tooltip" data-original-title="<?=$module['detail']['help']?>" href="#" class="pull-left text-blue cursor-pointer font-size20px">
                        <i class="mdi mdi-help-circle-outline"></i>
                    </a>
            <?php endif; ?>


            <?php if ($module["_installed"] == 0): ?>
                    <button data-button="<?=$module["module_name"]?>" id="m_install"
                       class="btn btn-flat uppercase cursor-pointer bg-blue btn-sm ad-click-event pull-right">
                        <?= Translate::sprint("Install") ?>
                    </button>
            <?php elseif ($module["_enabled"] == 0): ?>
                    <button data-button="<?=$module["module_name"]?>" id="m_enable"
                       class="btn btn-flat uppercase cursor-pointer bg-green btn-sm ad-click-event pull-right">
                        <?= Translate::sprint("Enable") ?>
                    </button>
            <?php elseif ($module["_enabled"] == 1): ?>
                    <button data-button="<?=$module["module_name"]?>" id="m_disable"
                       class="btn btn-flat uppercase cursor-pointer bg-orange btn-sm ad-click-event pull-right" <?=($module["detail"]['required'] == 1? "disabled": "") ?>>
                        <?= Translate::sprint("Disable") ?>
                    </button>
            <?php endif; ?>

            <?php if ($module["_installed"] == 1 && $module["version_code"] < $module["detail"]['version_code']): ?>
                    <button data-button="<?=$module["module_name"]?>" id="m_upgrade"
                            class="btn btn-flat uppercase cursor-pointer bg-yellow btn-sm ad-click-event pull-right">
                        <?= Translate::sprint("Upgrade") ?>
                    </button>
            <?php endif; ?>

            <?php if ($module["_installed"] == 1 && $module["_enabled"] == 0 && $module["detail"]['required'] == 0): ?>
                    <button data-button="<?=$module["module_name"]?>" id="m_uninstall"
                            class="btn btn-flat uppercase cursor-pointer bg-red btn-sm ad-click-event pull-right">
                        <?= Translate::sprint("Uninstall") ?>
                    </button>
            <?php endif; ?>






        </div>
    </div>
</div>