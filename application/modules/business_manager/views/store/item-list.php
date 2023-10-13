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
    $image_url = !empty($images) ? $images['200_200']['url'] : adminAssets("images/def_logo.png");
}

?>

<li>
    <a href="<?=admin_url("business_manager/store?id=".$object['id_store'])?>" data-id="<?=$object['id_store']?>" class="item item-store">
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
                <div class="item-subtitle badge " style="color: #FFFFFF;background-color: <?=$object['category_color']!=""?$object['category_color']:"gray"?>!important;"><?=$object['category_name']?></div>
                <div class="item-title"><?=$object['name']?></div>
                <div class="item-subtitle bottom-subtitle"><i class="mdi mdi-map-marker"></i> <?=$object['address']?>
                </div>
            </div>
        </div>
    </a>
</li>

