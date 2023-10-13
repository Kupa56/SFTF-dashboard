

<div id="somecomponent_<?=$var?>" style="width:100%;height:500px;margin-bottom: 15px" class="map-view "></div>


<div class="form-group address-form <?=isset($config['address']) && $config['address']==TRUE?"":"hidden"?>">
    <label> <?= Translate::sprint("City") ?></label>
    <div class="input-group ">
        <input type="text" class="form-control"
               placeholder="<?= Translate::sprint("Enter") ?> ..." name="address" id="<?="address_".$var?>" value="<?=$address?>">
        <span class="input-group-addon"><i class="mdi mdi-magnify search-icon"></i><i class="fa fa-spinner fa-spin spin-icon hidden"></i></span>
    </div>
    <input type="hidden" id="<?="city_".$var?>" value="<?=isset($city) && $city != ""? $city:"" ?>"/>
    <input type="hidden" id="<?="country_".$var?>" value="<?=isset($country) && $country != ""? $country:"" ?>"/>

</div>

<div class="form-group <?=isset($custom_address) && $custom_address != ""? "":"hidden" ?>  custom-address-form <?=isset($config['custom_address']) && $config['custom_address']==TRUE?"":"hidden"?>">
    <label> <?= Translate::sprint("Address") ?></label>
    <input type="text" class="form-control"
           placeholder="<?= Translate::sprint("Enter") ?> ..." name="custom_address" id="<?="custom_address_".$var?>" value="<?=$custom_address?>">
</div>

<div class="form-group location-form <?=isset($lat) && $lat != ""? "":"hidden" ?> ">
    <div class="row">
        <div class="col-md-6 <?=isset($config['lat']) && $config['lat']==TRUE?"":"hidden"?>">
            <label><?= Translate::sprint("Latitude") ?> : </label> <input
                    class="form-control" type="text" name="lat" id="<?="lat_".$var?>" value="<?=$lat?>"/>
        </div>
        <div class="col-md-6 <?=isset($config['lat']) && $config['lng']==TRUE?"":"hidden"?>">
            <label><?= Translate::sprint("Longitude") ?> : </label> <input
                    class="form-control" type="text" name="long" id="<?="lng_".$var?>"  value="<?=$lng?>"/>
        </div>
    </div>
</div>