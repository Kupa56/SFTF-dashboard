<script src="<?= adminAssets("plugins/locationpicker/locationpicker.jquery.min.js") ?>"></script>
<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key=<?= MAPS_API_KEY ?>&callback=Function.prototype&libraries=places'></script>
<script>


    $('#somecomponent_<?=$var?>').locationpicker({
        location: {latitude: <?=$lat?>, longitude:<?=$lng?>},
        radius: 300,
        inputBinding: {
            latitudeInput: $('#lat_<?=$var?>'),
            longitudeInput: $('#lng_<?=$var?>'),
            radiusInput: $('#radius_<?=$var?>'),
            locationNameInput: $('#places_<?=$var?>')
        },
        enableAutocomplete: true
    });


</script>