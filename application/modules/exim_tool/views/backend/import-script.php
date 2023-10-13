<script>




	$("#continue").on('click',function () {

	    NSTemplateUIAnimation.button.loading = $(this);

	    let file_encoding = $("#file_encoding").val();
	    let file_delimiter = $("#file_delimiter").val();

       setTimeout(function () {

           let uploaded_file =  <?=$uploader_files_variable?>;
           for (let key in uploaded_file){
               NSDocument.href = "<?=admin_url("exim_tool/mapping?module=".$module."&file=")?>"+key+"&file_encoding="+file_encoding+"&file_delimiter="+file_delimiter;
               return;
           }

           alert("<?=_lang("Please upload file")?>");

       },500);

	    return false;
    });

</script>
