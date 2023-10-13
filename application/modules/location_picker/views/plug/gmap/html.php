<div class="form-group hidden">
    <label> <?= Translate::sprint("City") ?> :</label>
    <select class="form-control select2" id="findCitySelector">

    </select>
</div>

<div class="next_step gmap">

    <div class="form-group">
        <label> <?= Translate::sprint("Location", "") ?> :</label>
        <input type="text" class="form-control"
               placeholder="<?= Translate::sprint("Search") ?> ..." name="places" value="<?=isset($address)?$address:""?>" id="places_<?= $var ?>"/>
    </div>


    <div class="<?=((!isset($hideMap) OR (isset($hideMap) && $hideMap==FALSE))?"":"hidden")?> map-component" id="somecomponent_<?= $var ?>" style="width:<?= isset($size_width) ? $size_width : "100%" ?>;height:<?= isset($size_height) ? $size_height : "500px" ?>;margin-bottom: 15px"></div>


    <div class="form-group  <?= isset($config['lat']) && $config['address'] == TRUE ? "" : "hidden" ?>">
        <label> <?= Translate::sprint("Address", "") ?> :</label>
        <input type="text" class="form-control"
               placeholder="<?= isset($placeholder['address']) ? $placeholder['address'] : Translate::sprint("Enter") ?> ..."
               name="address" id="<?= "address_" . $var ?>" value="<?= $address ?>" />
    </div>
    <div class="form-group hidden">
        <div class="row">
            <div class="col-md-6 <?= isset($config['lat']) && $config['lat'] == TRUE ? "" : "hidden" ?>">
                <label><?= Translate::sprint("Lat", "") ?> : </label> <input
                        class="form-control lat" type="text" name="lat" id="<?= "lat_" . $var ?>" value="<?= $lat ?>"/>
            </div>
            <div class="col-md-6 <?= isset($config['lat']) && $config['lng'] == TRUE ? "" : "hidden" ?>">
                <label><?= Translate::sprint("Lng", "") ?> : </label> <input
                        class="form-control lng" type="text" name="long" id="<?= "lng_" . $var ?>" value="<?= $lng ?>"/>
            </div>
        </div>
        <input type="hidden" id="<?= "city_" . $var ?>" value="<?= isset($city) && $city != "" ? $city : "" ?>"/>
        <input type="hidden" id="<?= "country_" . $var ?>"
               value="<?= isset($country) && $country != "" ? $country : "" ?>"/>
    </div>

</div>

