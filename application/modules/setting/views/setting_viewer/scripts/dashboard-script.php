<script src="<?=adminAssets("plugins/colorpicker/bootstrap-colorpicker.js")?>"></script>
<script>


    //$('.dashboard-block .form-group .select2').select2();
    //$('.dashboard-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveDashboardConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        dataSet["APP_LOGO"] = <?=$uploader_variable?>;

        $( ".dashboard-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });




</script>

