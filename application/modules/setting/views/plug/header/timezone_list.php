<?php

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$selected_timezone = $this->mUserBrowser->getData('user_timezone');


?>


<li class="dropdown timezone_selector">
    <a href="#" id="select_timezone">
        <i class="mdi mdi-timetable"></i> &nbsp;
        <?php if(!isMobile()): ?>
        <?=MyDateUtils::convert(date("Y-m-d H:i",time()),"UTC",TimeZoneManager::getTimeZone(),"h:i A") ?> : <?=$selected_timezone?>
        <?php else: ?>
            <?=$selected_timezone?>
        <?php endif; ?>
    </a>

    <select style="" id="select_timezone_options" class="select2 hidden">
        <option value="0"><?=Translate::sprint("-- Select timezone")?></option>
    <?php foreach ($timezones as $tz) : ?>
            <option value="<?=$tz?>" <?= $tz==$selected_timezone? "selected":"" ?> ><?=$tz?></option>
    <?php endforeach; ?>
    </select>
</li>

<?php

$data["selected_timezone"] = $selected_timezone;
$script = $this->load->view('setting/plug/header/timezone_list_script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>

<style>

    .timezone_selector .select2-container{
        padding-top: 8px;
        padding-bottom: 8px;
        min-width: 200px;
    }

    .timezone_selector .select2-container .select2-selection__arrow{
        height: 44px;
    }

</style>

