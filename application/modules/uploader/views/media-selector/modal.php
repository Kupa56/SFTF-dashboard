<div class="modal fade"
     id="modal-media-selector">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Media")?></b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group required">
                        <?php

                            $upload_plug = $this->uploader->plugin(array(
                                "limit_key"     => "publishFiles",
                                "token_key"     => "SzYjES-4555",
                                "limit"         => 1,
                                "script_trigger_callback"        => "file_uploaded0",
                            ));

                            echo $upload_plug['html'];
                            AdminTemplateManager::addScript($upload_plug['script']);

                            ?>


                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="images-container">

                        </div>
                    </div>
                    <div class="col-sm-12 text-center">
                        <button data-page="1" class="btn" id="pre-loading"><?=_lang("Load")?></button>
                    </div>


            </div>
            <div class="modal-footer">
                <button type="button" id="create"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Select") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<?php

$data0 = array();
$script = $this->load->view('uploader/backend/html/scripts',$data0,TRUE);
AdminTemplateManager::addScript($script);


?>