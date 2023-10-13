<script>


	$('#export-<?=$unique_id?>').on('click',function () {

        $( "exim[data-col]").each(function( index ) {
            var col = $(this).attr('data-col');
            export_cols_<?=$unique_id?>[col] = true;
        }).promise().done(function () {
            createColsFields();
            $("#<?=$modal_id?>").modal('show');
        });

        return false;
    });

	var export_cols_<?=$unique_id?> = {};


	function createColsFields() {

        $('#cols-contianer').html("");

        for (var key in export_cols_<?=$unique_id?>){
            var name = $('exim.exim-col-'+key).attr('exim-col-name');
			var html = '<span><label><input type="checkbox" class="" value="'+key+'" checked />&nbsp;&nbsp;'+name+'</label>&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$('#cols-contianer').append(html);

		}

    }


    $('#export-<?=$modal_id?>').on('click',function () {

        $("#export-<?=$modal_id?>").attr("disabled",true);
        $("#export-<?=$modal_id?> span").html("<?=_lang("Exporting...")?>");
        $("#export-<?=$modal_id?> .loading").removeClass("hidden");

        var cols = {};
        var cols_count = 0;

        $('#cols-contianer input[type=checkbox]').each(function( index ) {

            if($(this).is(':checked')){
                var key = $(this).val();
                cols[key] = true;
                cols_count++;
			}

        }).promise().done(function () {
            console.log(cols);

            if(cols_count === 0){
                alert("No columns selected");
                return false;
			}



            //collect all data
			collectRows(cols);



        });

        return false;
    });


    var exported_rows = {};
    var exported_cols = {};

	function collectRows(_cols) {

		
        $( ".exim-row").each(function( index ) {

            var index = $(this).attr('exim-row');
            exported_rows[index] = {};

        }).promise().done(function () {

            collectRowData(_cols);

        });


    }


    function collectRowData(_cols) {


	    for (var key in exported_rows){
            exported_rows[key] = {};
            for (var col in _cols){
                var value = $( ".exim-row[exim-row="+key+"] exim.exim-col-"+col).text();
                exported_rows[key][col] = value;
            }
		}

        console.log("exported_rows");
        console.log(exported_rows);
        exported_cols = _cols;

        download_from_server();

    }
    
    
    function download_from_server() {


        $.ajax({
            url:"<?=site_url("exim_tool/ajax/export")?>",
            data:{
                "module"     :"<?=$module?>",
                "data"     :exported_rows,
                "format"   :$('#select-export-<?=$modal_id?>').val(),
				"cols"    :exported_cols
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {


            },error: function (request, status, error) {

                $("#export-<?=$modal_id?>").attr("disabled",false);
                $("#export-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                $("#export-<?=$modal_id?> .loading").addClass("hidden");

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if(data.success === 1){
                    var link = data.result;
                    var strWindowFeatures = "location=yes,height=570,width=520,scrollbars=yes,status=yes";
                    var win = window.open(link, "_blank");
				}else {
                    alert("Something wrong!");
                    $("#export-<?=$modal_id?>").attr("disabled",false);
                    $("#export-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                    $("#export-<?=$modal_id?> .loading").addClass("hidden");
                }

                $("#export-<?=$modal_id?>").attr("disabled",false);
                $("#export-<?=$modal_id?> span").html("<?=_lang("Export")?>");
                $("#export-<?=$modal_id?> .loading").addClass("hidden");

                $("#<?=$modal_id?>").modal('hide');
            }
        });


    }


    var request_filter_state = -1;

    $("#open-filter-<?=$modal_id?>").on("click",function () {
        $("#<?=$modal_id?>").modal('hide');
        $("#my-modal-filter").modal('show');
        request_filter_state = 1001;
        return false;
    });


    $("#apply-filter").on('click',function () {
        if(request_filter_state === 1001){
            request_filter_state = -1;
            $("#<?=$modal_id?>").modal('show');
		}
    });



</script>
