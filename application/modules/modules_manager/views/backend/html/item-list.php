<tr class="module_item module_item_<?=$module['module_name']?>">
    <td><label><input class="module_check" data-module="<?=$module['module_name']?>" id="module_check" type="checkbox"></label></td>
    <td>
        <div class="image-container-70"
              style="background-image: url('<?=$module['detail']['icon']?>') ;background-size: auto 100%;">
            <img class="direct-chat-img invisible" src="<?=$module['detail']['icon']?>" alt="Message User Image"/>
        </div>
    </td>
    <td>
        <div class="clearfix">
            <strong class="font-size18px"><?=ucfirst($module['module_name'])?></strong><br>
            <p><?=$module['detail']['description']?></p>


            <u><?=Translate::sprint("Version")?></u>:
        <?php if ($module["_installed"] == 1 && $module["version_code"] < $module["detail"]['version_code']): ?>
                <i class="mdi mdi-information-outline text-yellow"></i>
        <?php endif; ?>

            <?=$module['detail']['version_name']?> (<?=$module['detail']['version_code']?>)

        <?php if(isset($module['detail']['help'])): ?>
                &nbsp;&nbsp;<a data-toggle="tooltip" data-original-title="<?=$module['detail']['help']?>" href="#" class="text-blue cursor-pointer">
                    <i class="mdi mdi-help-circle-outline"></i>
                </a>
        <?php endif; ?>


        </div>
    </td>
    <td>

    <?php if($module["_enabled"]==1 && method_exists($this->{$module["module_name"]},'settings') && $this->{$module["module_name"]}->settings() != NULL  ): ?>
            <a href="<?=$this->{$module["module_name"]}->settings()?>"><i class="mdi mdi-cog-outline"></i>&nbsp;&nbsp;<?=_lang("Settings")?></a>
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

    </td>
</tr>


