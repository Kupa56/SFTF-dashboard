<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>



    var curLocation = [<?=isset($lat)?$lat:0?>, <?=isset($lng)?$lng:0?>];
    // use below if you have a model
    // var curLocation = [@Model.Location.Latitude, @Model.Location.Longitude];

    if (curLocation[0] === 0 && curLocation[1] === 0) {
        curLocation = [5.9714, 116.0953];
    }

    var map_<?=isset($var)?$var:"var"?> = L.map('somecomponent_<?=$var?>').setView(curLocation, 13);

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map_<?=$var?>);

    map_<?=$var?>.attributionControl.setPrefix(false);

    var marker = new L.marker(curLocation, {
        draggable: 'true'
    });

    //draggable
    marker.on('dragend', function(event) {
        var position = marker.getLatLng();
        marker.setLatLng(position, {
            draggable: 'true'
        }).bindPopup(position).update();
        $("#lat_<?=$var?>").val(position.lat);
        $("#lng_<?=$var?>").val(position.lng);
        map_<?=$var?>.flyTo([position.lat, position.lng], map_<?=$var?>.getZoom());
    });

    $("#lat_<?=$var?>, #lng_<?=$var?>").change(function() {
        var position = [parseInt($("#lat_<?=$var?>").val()), parseInt($("#lng_<?=$var?>").val())];
        marker.setLatLng(position, {
            draggable: 'true'
        }).bindPopup(position).update();
        map_<?=$var?>.panTo(position);
    });

    map_<?=$var?>.addLayer(marker);


    $("#address_<?=$var?>").keypress(function(event){

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode === 13){

            $.get(location.protocol + '//nominatim.openstreetmap.org/search?accept-language=en&limit=10&format=json&q='+$(this).val(), function(data){

                console.log(data);

                if(data.length>0){

                    let lat = data[0].lat;
                    let lng = data[0].lon;

                    curLocation = [lat, lng];


                    //update fields
                    $("#lat_<?=$var?>").val(lat);
                    $("#lng_<?=$var?>").val(lng);

                    //move & zoom
                    map_<?=$var?>.flyTo([lat, lng], 13);

                    //add marker
                    //if(marker)
                    // map.removeLayer(marker);

                    // marker = L.marker(new L.LatLng(lat,lng)).addTo(map_<?=$var?>);
                    marker.setLatLng(curLocation, {
                        draggable: 'true'
                    }).bindPopup(curLocation).update();
                }else {
                    alert("<?=_lang("Invalid address!, please try to type city, street...")?>")
                }
            });
        }

    });



    var availableTags = [];


    $("#address_<?=$var?>").keyup(function(event){

        $.get(location.protocol + '//nominatim.openstreetmap.org/search?accept-language=en&limit=10&format=json&q='+$(this).val(), function(data){

            console.log(data);

           /* for (let key in data){
                availableTags.push(
                    data[key].display_name
                );
            }

            $( "#address_$var" ).autocomplete({
                source: availableTags
            });*/
        });

    });


</script>