<?php
   // AdminTemplateManager::addScriptLibs(adminAssets("plugins/colorpicker/bootstrap-colorpicker.js"));
    
?>
<script src="<?= adminAssets("plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>
<script>

    $('.store-block .form-group .select2').select2();
    //$('.store-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveStoreConfig").on('click', function () {

        var selector = $(this);
        let dataSet = {};

        $( ".store-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();


        }).promise().done( function(){


            if($('#autoDetectLocation').is(':checked')){
                dataSet['MAP_DEFAULT_LATITUDE'] =  -1;
                dataSet['MAP_DEFAULT_LONGITUDE'] = -1;
            }else{
                dataSet['MAP_DEFAULT_LATITUDE'] =  $('#<?=$location_fields_id['lat']?>').val();
                dataSet['MAP_DEFAULT_LONGITUDE'] = $('#<?=$location_fields_id['lng']?>').val();
            }

            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });


    $('#autoDetectLocation').on('click',function (){

        if($(this).is(':checked')){

            $('.storeSettingLocationContainer').addClass("hidden");
            $('#SETTING_AUTO_LOC_DETECT').val(1);

            $('.gmap .lat').val(-1);
            $('.gmap .lng').val(-1);

        }else{

            $('.storeSettingLocationContainer').removeClass("hidden");
            $('#SETTING_AUTO_LOC_DETECT').val(0);

            const geoOps = {
                enableHighAccuracy: true,
                timeout: 10000 //10 seconds
            };
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback, geoOps);
        }

    });

    function successCallback(pos) {
        $('.map-component').locationpicker({
            location: {latitude: pos.coords.latitude, longitude:pos.coords.longitude},
            radius: 300,
            inputBinding: {
                latitudeInput: $('#<?=$location_fields_id['lat']?>'),
                longitudeInput: $('#<?=$location_fields_id['lng']?>'),
            },
            enableRestriction: false,
            enableAutocomplete: true
        });
    }

    function errorCallback() {

    }



    

</script>

