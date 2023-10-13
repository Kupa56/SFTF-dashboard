<?php
if(!isset($module))
    $module = "store";
?>

<script>

    var event_key = "";

    var custom_parameter_callback = null;

    var <?=$module?>_custom_parameters = {
        custom_campaign:"<?=$event_key?>"
    };


    /*
     *   FILL PARAMETERS
     */

    //callback_campaign_<?=$module?>(<?=$module?>_custom_parameters);


    setTimeout(function () {

        $(".select-modules").addClass("hidden");
        $(".campaign_fields").removeClass('hidden');

        module_name = "event";
        module_id = <?=$extras['id']?>;

        results_list = {
            results:{}
        };

        results_list.results = {
            0: <?=json_encode($extras,JSON_FORCE_OBJECT)?>
        };

        calculateEstimation("event",module_id,<?=$module?>_custom_parameters);
        update_device_previews(module_id);

    },500);



</script>