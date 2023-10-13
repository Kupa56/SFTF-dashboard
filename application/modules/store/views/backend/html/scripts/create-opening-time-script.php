<script src="<?=  adminAssets("plugins/iCheck/icheck.min.js")?>"></script>
<script>

    //iCheck
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });

</script>

<script>

    var opening_time = false;

    $('#opening_time').on('ifChecked', function (event) {
        opening_time = true;
        $("#_h").removeClass('hidden');
    });

    $('#opening_time').on('ifUnchecked', function (event) {
        opening_time = false;
        $("#_h").addClass('hidden');
    });


</script>

<script src="<?= AdminTemplateManager::assets('store',"plugins/timepicker/jquery.timepicker.js")?>"></script>
<script>

    var times = {}

    $('.form-group .date-picker').timepicker({ 'timeFormat': '<?=ConfigManager::getValue("DATE_FORMAT")=="24"?"H:i":"h:i A"?>' });

<?php foreach ($days as $day): ?>

            times.<?=$day?> = {
                opening: "",
                closing: "",
            };

        $('#_checked_d_<?=$day?>').on('change',function () {

            var result = $(this).prop('checked');
            if(result){

                $("#_o_d_<?=$day?>").attr('disabled',false);
                $("#_c_d_<?=$day?>").attr('disabled',false);

            }else{

                $("#_o_d_<?=$day?>").attr('disabled',true);
                $("#_c_d_<?=$day?>").attr('disabled',true);

                $("#_o_d_<?=$day?>").val("");
                $("#_c_d_<?=$day?>").val("");

                times.<?=$day?>.opening = "";
                times.<?=$day?>.closing = "";
            }

        });

        //opening event
        $("#_o_d_<?=$day?>").on('changeTime', function() {

            times.<?=$day?>.opening = $(this).val();

        });

        //closeing event
        $("#_c_d_<?=$day?>").on('changeTime', function() {

            times.<?=$day?>.closing = $(this).val();

        });


<?php endforeach; ?>



</script>