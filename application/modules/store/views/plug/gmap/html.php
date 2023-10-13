<div class="form-group">
    <label> <?= Translate::sprint("Search", "") ?> :</label>
    <input type="text" class="form-control"
           placeholder="<?= Translate::sprint("Search") ?> ..." name="places" id="places_<?=$var?>">
</div>
<div id="somecomponent_<?=$var?>" style="width:100%;height:500px;margin-bottom: 15px"></div>
<div class="form-group  <?=isset($config['lat']) && $config['address']==TRUE?"":"hidden"?>">
    <label> <?= Translate::sprint("Address", "") ?> :</label>
    <input type="text" class="form-control"
           placeholder="<?= Translate::sprint("Enter") ?> ..." name="address" id="<?="address_".$var?>" value="<?=$address?>">
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-6 <?=isset($config['lat']) && $config['lat']==TRUE?"":"hidden"?>">
            <label><?= Translate::sprint("Lat", "") ?> : </label> <input
                class="form-control" type="text" name="lat" id="<?="lat_".$var?>" value="<?=$lat?>"/>
        </div>
        <div class="col-md-6 <?=isset($config['lat']) && $config['lng']==TRUE?"":"hidden"?>">
            <label><?= Translate::sprint("Lng", "") ?> : </label> <input
                class="form-control" type="text" name="long" id="<?="lng_".$var?>"  value="<?=$lng?>"/>
        </div>
    </div>
</div>