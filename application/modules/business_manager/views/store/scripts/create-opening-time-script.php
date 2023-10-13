
<script>

    var opening_time = false;

    $('#opening_time').on('change', function () {
        if($(this).is(":checked")){
            opening_time = true;
            $("#op-form").removeClass('hidden');
        }else{
            opening_time = false;
            $("#op-form").addClass('hidden');
        }
    });



</script>

<script src="<?= AdminTemplateManager::assets('store',"plugins/timepicker/jquery.timepicker.js")?>"></script>
<script>


    var times = {}

    $('.custom-form .date-picker').timepicker({ 'timeFormat': 'h:i A' });

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