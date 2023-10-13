<?php
    if(!isset($module))
        $module = "store";
?>

<?php if(CPT_SELECTOR_ENABLED): ?>
<div class="form-group custom-parameter custom-parameter-<?=$module?> hidden">
    <label><?=Translate::sprint('Platforms')?></label>
    <br>

<?php if(ConfigManager::isAndriodNiOS()):?>
    <label><input class="platforms-iOS-<?=$module?>" name="platforms-iOS-<?=$module?>" value="1" type="checkbox" checked="">&nbsp;&nbsp;iOS</label>&nbsp;&nbsp;&nbsp;
    <label><input class="platforms-android-<?=$module?>" name="platforms-android-<?=$module?>" value="2" type="checkbox" checked="">&nbsp;&nbsp;android</label>&nbsp;&nbsp;&nbsp;
<?php elseif(ConfigManager::isAndroid()): ?>
    <label><input class="platforms-iOS-<?=$module?>" name="platforms-iOS-<?=$module?>" value="1" type="checkbox" disabled>&nbsp;&nbsp;iOS</label>&nbsp;&nbsp;&nbsp;
    <label><input class="platforms-android-<?=$module?>" name="platforms-android-<?=$module?>" value="2" type="checkbox" checked="">&nbsp;&nbsp;android</label>&nbsp;&nbsp;&nbsp;<br>
    <sup class="text-red"><i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?=_lang("To be able to make iOS devices as target, you should verify your iOS purchase ID")?></sup>
<?php elseif(ConfigManager::isIos()): ?>
    <label><input class="platforms-iOS-<?=$module?>" name="platforms-iOS-<?=$module?>" value="1" type="checkbox" checked="">&nbsp;&nbsp;iOS</label>&nbsp;&nbsp;&nbsp;
    <label><input class="platforms-android-<?=$module?>" name="platforms-android-<?=$module?>" value="2" type="checkbox" disabled>&nbsp;&nbsp;android</label>&nbsp;&nbsp;&nbsp;<br>
        <sup class="text-red"><i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?=_lang("To be able to make android devices as target, you should verify your android purchase ID")?></sup>
<?php endif;?>

</div>
<?php else: ?>
<div class="form-group custom-parameter custom-parameter-<?=$module?> hidden">
    <label><input class="platforms-iOS-<?=$module?> hidden" name="platforms-iOS-<?=$module?>" value="1" type="checkbox" checked="">
    <label><input class="platforms-android-<?=$module?> hidden "  name="platforms-android-<?=$module?>" value="2" type="checkbox" checked="">
</div>
<?php endif; ?>


<div class="form-group custom-parameter custom-parameter-<?=$module?> hidden">
    <label><?=Translate::sprint('Getting Option')?></label>
    <br>
    <!--<label><input class="getting_option_<?=$module?>" name="getting_option_<?=$module?>" value="1" type="radio" checked="true">&nbsp;&nbsp;<?=Translate::sprint("All")?></label>&nbsp;&nbsp;&nbsp;-->
    <label><input class="getting_option_<?=$module?>" name="getting_option_<?=$module?>" value="2" type="radio">&nbsp;&nbsp;<?=Translate::sprint("Nearby users")?> (<?=Translate::sprintf("within %s KM",array(RADUIS_TRAGET))?>)</label>&nbsp;&nbsp;&nbsp;
    <label><input class="getting_option_<?=$module?>" name="getting_option_<?=$module?>" value="3" type="radio">&nbsp;&nbsp;<?=Translate::sprint("Random")?></label>&nbsp;&nbsp;&nbsp;
    <br>
    <sup><?=Translate::sprintf("Number max pushes per campaign is %s",array(LIMIT_PUSHED_GUESTS_PER_CAMPAIGN))?></sup>
</div>