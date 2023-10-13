<script src="<?= adminAssets("plugins/fastclick/fastclick.js") ?>"></script>
<script src="<?= adminAssets("plugins/locationpicker/locationpicker.jquery.js") ?>"></script>
<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key=<?= MAPS_API_KEY ?>&callback=Function.prototype&libraries=places'></script>
<script>

    $(document).on({
        'DOMNodeInserted': function() {
            $('.pac-item, .pac-item span', this).addClass('no-fastclick');
        }
    }, '.pac-container');

    $('#somecomponent_<?=$var?>').locationpicker({
        location: {latitude: <?=$lat?>, longitude:<?=$lng?>},
        radius: 300,
        inputBinding: {
            latitudeInput: $('#lat_<?=$var?>'),
            longitudeInput: $('#lng_<?=$var?>'),
            radiusInput: $('#radius_<?=$var?>'),
            locationNameInput: $('#places_<?=$var?>')
        },
        enableRestriction: false,
        //countriesRestrictions: ["ca"],
        enableAutocomplete: true
    });


    <?php if(!isset($lat) or empty($lat) or (isset($lat) && $lat==-1)): ?>


    navigator.geolocation.getCurrentPosition(successCallback09779, errorCallback09779, {
        enableHighAccuracy: true,
        timeout: 1000 //10 seconds
    });

    function successCallback09779(pos) {

        $('#somecomponent_<?=$var?>').locationpicker({
            location: {latitude: pos.coords.latitude, longitude:pos.coords.longitude},
            radius: 300,
            inputBinding: {
                latitudeInput: $('#lat_<?=$var?>'),
                longitudeInput: $('#lng_<?=$var?>'),
                radiusInput: $('#radius_<?=$var?>'),
                locationNameInput: $('#places_<?=$var?>')
            },
            enableRestriction: false,
            //countriesRestrictions: ["ca"],
            enableAutocomplete: true
        });

    }

    function errorCallback09779() {

    }

    <?php endif; ?>

</script>