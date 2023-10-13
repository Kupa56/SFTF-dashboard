
<script>


	var export_request_<?=$unique_id?> = "all";

	$("#<?=$modal_id?> #all").on('change',function () {

        export_request_<?=$unique_id?> = "all";

        $("#<?=$modal_id?> .period_form").addClass('hidden');

    });


    $("#<?=$modal_id?> #specific").on('change',function () {

        export_request_<?=$unique_id?> = "specific";

        $("#<?=$modal_id?> .period_form").removeClass('hidden');

    });

    $.fn.datepicker.defaults.format = "yyyy-mm-dd";

    $('#<?=$modal_id?> .datepicker').datepicker();



    $('#export2-<?=$modal_id?>').on('click',function () {

        $("#export2-<?=$modal_id?>").attr("disabled",true);
        $("#export2-<?=$modal_id?> span").html("<?=_lang("Exporting...")?>");
        $("#export2-<?=$modal_id?> .loading").removeClass("hidden");


        var cols_to_export = [];

        $( "#<?=$modal_id?> #cols-contianer input[type=checkbox]").each(function( index ) {

            if($(this).is(':checked')){

                cols_to_export.push(
                    $(this).val()
				)

			}
        }).promise().done(function () {

            console.log(cols_to_export);
            export2_data(cols_to_export);

        });



    });


    
    function export2_data(columns) {

        $.ajax({
            url:"<?=ajax_url("exim_tool/export2_data")?>",
            data:{
                "module"     :"<?=$module?>",
                "format"   :$('#select-export-<?=$modal_id?>').val(),
                "columns"    :columns,
                "date_from"    :$('#<?=$modal_id?> #date_from').val(),
                "date_to"    :$('#<?=$modal_id?> #date_to').val(),
                "export_request"    :export_request_<?=$unique_id?>,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {


            },error: function (request, status, error) {

                $("#export2-<?=$modal_id?>").attr("disabled",false);
                $("#export2-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                $("#export2-<?=$modal_id?> .loading").addClass("hidden");

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);


                if(data.code === 1){

                    $("#<?=$modal_id?>").modal('hide');

                    var link = data.result;
                    var strWindowFeatures = "location=yes,height=570,width=520,scrollbars=yes,status=yes";
                    var win = window.open(link, "_blank");
                }else {

                    $("#export2-<?=$modal_id?>").attr("disabled",false);
                    $("#export2-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                    $("#export2-<?=$modal_id?> .loading").addClass("hidden");


                    console.log(data);

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br>";
                    }
                    if (errorMsg !== "") {
                        $("#<?=$modal_id?> .message-error .messages").html(errorMsg);
                        $("#<?=$modal_id?> .message-error").removeClass("hidden");
                    }

                }


                $("#export2-<?=$modal_id?>").attr("disabled",false);
                $("#export2-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                $("#export2-<?=$modal_id?> .loading").addClass("hidden");



            }
        });
    }


    var EximTool = {

        export2: {
            set button(selector) {
                selector.on('click',function () {
                    $("#<?=$modal_id?>").modal('show');
                    return false;
                });
            },
        }


    }


</script>
