<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>
<script>


    function file_uploaded(results) {

        setTimeout(function () {

            $("#delete").remove();
            $(".uploaded-file.item_"+results.dir).append("<br><i class='fa fa-refresh fa-spin'></i>&nbsp;<?=Translate::sprint("Moving & Unzipping...")?>");

            $.ajax({
                url:"<?=  site_url("ajax/modules_manager/install_internal_path")?>",
                data:{
                    'dir':results.dir,
                    'path':results.dis
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },error: function (request, status, error) {
                    alert(request.responseText);
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                    if(data.success===1){
                        $(".uploaded-file.item_"+results.dir).append("<br><i class='mdi mdi-check'></i>&nbsp;<?=Translate::sprint("Successful!")?>");
                        setTimeout(function () {
                            document.location.href = "<?=admin_url("modules_manager/manage")?>";
                        },2000);
                    }else if(data.success===0){
                        var errorMsg = "";
                        for(var key in data.errors){
                            errorMsg = errorMsg+data.errors[key]+"\n";
                        }
                        if(errorMsg!==""){
                            alert(errorMsg);
                            document.location.reload();
                        }
                    }
                }
            });


        },1000);


    }


</script>


    
