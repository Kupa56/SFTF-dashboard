
<?php

if (!is_array($object['images']))
    $images = json_decode($object['images'], JSON_OBJECT_AS_ARRAY);
else
    $images = $object['images'];

if (isset($images[0])) {
    $images = $images[0];
    if (isset($images['200_200']['url'])) {
        $image_url = $images['200_200']['url'];
    } else {
        $image_url = adminAssets("images/def_logo.png");
    }
} else {
    $image_url = $images['200_200']['url'];
}

?>

<li>
    <a href="<?=admin_url("business_manager/event?id=".$object['id_event'])?>" data-id="<?=$object['id_event']?>" class="item item-event">
        <div class="item-content">
            <div class="item-media">
                <img src="<?=$image_url?>" alt="">
            <?php if($object['verified']==0): ?>
                    <span class="status orange"><strong><?=_lang("Not approved")?></strong></span>
            <?php elseif($object['status']==0): ?>
                    <span class="status red"><strong><?=_lang("Disabled")?></strong></span>
            <?php else: ?>
                    <span class="status green"><strong><?=_lang("Enabled")?></strong></span>
            <?php endif; ?>

            </div>
            <div class="item-inner">
                <div class="item-title"><?=$object['name']?></div>
                <div class="item-subtitle bottom-subtitle"><i class="mdi mdi-map-marker"></i> <?=$object['address']?></div>

            <?php


                $current = date("Y-m-d H:i:s",time());
                $currentData = $current;
                $event['date_b'] = MyDateUtils::convert($object['date_b'],"UTC","UTC","Y-m-d");
                $event['date_e'] = MyDateUtils::convert($object['date_e'],"UTC","UTC","Y-m-d");

                $currentData = date_create($currentData);
                $dateStart = date_create($object['date_b']);
                $dateEnd = date_create($object['date_e']);

                $differenceStart = $currentData->diff($dateStart);
                $differenceEnd = $currentData->diff($dateEnd);

                $diff_millseconds_start = strtotime($object['date_b']) - strtotime($current);
                $diff_millseconds_end = strtotime($object['date_e']) - strtotime($current);


                echo Translate::sprint("Start").": ".date("Y-m-d",strtotime($object['date_b']))." - ";
                echo Translate::sprint("End").": ".date("Y-m-d",strtotime($object['date_e']))."<br>";

                ?>

            <?php

                if ($object['status'] == 0)
                    echo '<span class="item-subtitle badge red"><i class="mdi mdi-history"></i> &nbsp;'.Translate::sprint("Disabled").'&nbsp;&nbsp;</span>';
                else if ($object['status'] == 1) {
                    if ($diff_millseconds_start>0) {
                        echo '<span class="item-subtitle  badge bg-green"><i class="mdi mdi-history"></i> &nbsp;'.Translate::sprint("Published","").'&nbsp;&nbsp;</span>';
                    } else if($diff_millseconds_start<0 && $diff_millseconds_end>0) {
                        echo '<span class="item-subtitle badge green"><i class="mdi mdi-check"></i> &nbsp;'.Translate::sprint("Started","").'&nbsp;&nbsp;</span>';
                    }else {
                        echo '<span class="item-subtitle badge red"><i class="mdi mdi-close"></i> &nbsp;'.Translate::sprint("Finished","").'&nbsp;&nbsp;</span>';
                    }
                }


                ?>
            </div>
        </div>
    </a>
</li>