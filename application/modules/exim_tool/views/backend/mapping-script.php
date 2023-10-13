<?php

AdminTemplateManager::addScriptLibs(TEMPLATE_SKIN_URL."/plugins/select2/select2.full.min.js");

?>
<script>

	$(".fields .select2").select2();


	$("#btnApplyImport").on('click',function () {

	    let selector = $(this);
	    var fields = {};

        $(".fields .field").each(function(i, elm) {

            let dfield = $(this).children(".default_field").val();
            let ifield = $(this).children(".imported_field").val();

            fields[dfield] = ifield;

        }).promise().done( function(){
            console.log(fields);
            apply_import(selector,fields);
		} );

	    return false;
    });

	function apply_import(selector, fields) {

        NSTemplateUIAnimation.button.loading = selector;

        $.ajax({
            url:"<?=ajax_url("exim_tool/import2_data")?>",
            data:{
                "file"     :"<?=$file?>",
                "fields"   :fields,
                "module"    :"<?=$module?>",
                "file_encoding"    :"<?=(isset($file_encoding) && in_array($file_encoding,Exim_tool::Encoding))?$file_encoding:""?>",
                "file_delimiter"    :"<?=(isset($file_delimiter) && in_array($file_delimiter,Exim_tool::Delimiter))?$file_delimiter:""?>",
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;


            },error: function (request, status, error) {

                NSTemplateUIAnimation.button.default = selector;

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if(data.code === 1){

                    NSTemplateUIAnimation.button.success = selector;
                    NSDocument.href = data.url;

                }else {

                    NSTemplateUIAnimation.button.default = selector;
                    console.log(data);


                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "<br>";
                    }
                    if (errorMsg !== "") {
                        $(" .message-error .messages").html(errorMsg);
                        $(".message-error").removeClass("hidden");
                    }

                    if(data.error_html !== ""){
                        $(".errors-container").html(data.error_html);
					}

                }

            }
        });

    }


</script>
