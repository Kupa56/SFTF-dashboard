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
    $image_url = adminAssets("images/def_logo.png");
}

?>

<li>
    <a href="<?=admin_url("business_manager/offer?id=".$object['id_offer'])?>" data-id="" class="item item-offer">
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
                <div class="item-subtitle bottom-subtitle"><i class="mdi mdi-map-marker"></i> <?=$object['store_name']?></div>
                <div class="item-subtitle badge " style="color: #FFFFFF;background-color: red !important;">
                <?php

                    if (is_array($object['currency']))
                        $object['currency'] = $object['currency']['code'];

                    if ($object['value_type'] == 'price') {
                        echo Currency::parseCurrencyFormat($object['offer_value'], $object['currency']) ;
                    } else if ($object['value_type'] == 'percent') {
                        echo  intval($object['offer_value']).'%' ;
                    } else {
                        echo Translate::sprint("Promotion");
                    }

                    ?>
                </div>
            </div>
        </div>
    </a>
</li>