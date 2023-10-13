<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>

<script src="<?=  adminAssets("plugins/easyautocomplete/jquery.easy-autocomplete.min.js")?>"></script>

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


    var addresses_<?=$var?> = [];
    //item query
    var options = {
        url: function (query) {
            return "<?=site_url("location_picker/ajax/getAddresses")?>?q=" + query;
        },
        getValue: function (element) {
            console.log(element);

            //addresses_<?=$var?>[element.address] = element;
            console.log(addresses_<?=$var?>);

            $('.address-form .search-icon').addClass('hidden');
            $('.address-form .spin-icon').removeClass('hidden');


            return element.address;
        },
        ajaxSettings: {
            dataType: "json",
            method: "GET",
            data: {
                dataType: "json"
            }
        },

        //eac-container-address_9fca12ee4c
        preparePostData: function (data) {
            data.query = $("#<?="address_".$var?>").val();
            return data;
        },
        list: {

            onClickEvent: function () {
                var data = $("#<?="address_".$var?>").getSelectedItemData();
                //load more data's item

                $('.address-form .search-icon').removeClass('hidden');
                $('.address-form .spin-icon').addClass('hidden');

                $('#country_<?=$var?>').val(data.country);

                if(data.city !== undefined)
                    $('#city_<?=$var?>').val(data.city);


                $('.custom-address-form').removeClass("hidden");
                $('.location-form').removeClass("hidden");

                updateMap(data);
                console.log(data);

            },

            onLoadEvent: function () {
                $('.address-form .search-icon').addClass('hidden');
                $('.address-form .spin-icon').removeClass('hidden');
            },

            onLoadDone:function (){

            },

            showAnimation: {
                type: "normal", //normal|slide|fade
                time: 100,
                callback: function () {

                }
            },
            hideAnimation: {
                type: "normal", //normal|slide|fade
                time: 100,
                callback: function () {

                }
            },
            match: {
                enabled: false
            }
        },
        requestDelay: 100
    };

    $("#<?="address_".$var?>").easyAutocomplete(options);

    function updateMap(data) {

        let lat = data.latitude;
        let lng = data.longitude;

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

    }

</script>