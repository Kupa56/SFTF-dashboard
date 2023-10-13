<div class="modal fade" id="module-pack-verification-popup">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b><?=_lang("Purchase Verify!")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="form-group ">
                    <label><?=_lang("Please enter purchase ID")?></label>
                    <input type="text" class="form-control" id="pid_plug" placeholder="<?=_lang("Enter")?>">
                </div>

                <sup><i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?=_lang("How to get this purchase ID: Please log in with your Envato account then go to downloads")?></sup>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel","Cancel")?></button>
                <button type="button" id="verify" class="btn btn-flat btn-primary"><?=Translate::sprint("Verify")?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>

    function quickVerifySPK(module_id,action){

        $("#module-pack-verification-popup").modal("show");
        $("#module-pack-verification-popup #verify").on("click",function () {

            let selector = $(this);

            let pid_plug = $("#module-pack-verification-popup #pid_plug").val();

            if(pid_plug==="")
                return ;

            setPIDSPK(selector,pid_plug,module_id,action);

            return false;
        });

    }


    function setPIDSPK(selector,pid,module,action) {

        $.ajax({
            url:"<?=  site_url("pack/ajax/set_pid")?>",
            data:{
                pid:pid
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

                NSTemplateUIAnimation.button.default = selector;

                if(data.success===1){

                    $("#module-pack-verification-popup").modal("hide");

                    if(action === "install")
                        $(".module_item.module_item_"+module+" #m_install").click();
                    else if(action === "upgrade")
                        $(".module_item.module_item_"+module+" #m_upgrade").click();

                }else if(data.success===0){
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }
        });



    }

</script>

