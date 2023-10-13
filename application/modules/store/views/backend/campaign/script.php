<?php
if(!isset($module))
    $module = "store";
?>

<script>


    var custom_parameter_callback = null;

    var <?=$module?>_custom_parameters = {
        platforms: {
            ios: 1,
            android: 1
        },
        getting_option: 1 // (1/2)
    };


    /*
     *   PLATFORMS
     */
    $('.custom-parameter-<?=$module?> .platforms-iOS-<?=$module?>').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            // the checkbox is now checked
            <?=$module?>_custom_parameters.platforms.ios = 1;
        } else {
            // the checkbox is now no longer checked
            <?=$module?>_custom_parameters.platforms.ios = 0;
        }

        callback_campaign_<?=$module?>(<?=$module?>_custom_parameters);
    });


    $('.custom-parameter-<?=$module?> .platforms-android-<?=$module?>').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            // the checkbox is now checked
            <?=$module?>_custom_parameters.platforms.android = 1;
        } else {
            // the checkbox is now no longer checked
            <?=$module?>_custom_parameters.platforms.android = 0;
        }

        callback_campaign_<?=$module?>(<?=$module?>_custom_parameters);
    });



    /*
     * GETTING OPTION
     */

    $('.custom-parameter-<?=$module?> input[type=radio][name=getting_option_<?=$module?>]').change(function() {

        <?=$module?>_custom_parameters.getting_option = this.value;
        callback_campaign_<?=$module?>(<?=$module?>_custom_parameters);

    });


</script>