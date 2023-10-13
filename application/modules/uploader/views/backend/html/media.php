<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content media">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Media") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
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
                                    AdminTemplateManager::addHtml($upload_plug['script']);
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

                            <div class="col-sm-12 preview hidden">

                                <h3><?=_lang("Preview")?></h3>
                                <div class="">
                                    <img src="#"  height="200"/>
                                </div>
                                <div class="form-group">
                                    <label>100x100</label>
                                    <input type="text" class="s100_100 form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>200x200</label>
                                    <input type="text" class="s200_200 form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>560x560</label>
                                    <input type="text" class="s500_500 form-control"/>
                                </div>
                                <div class="form-group">
                                    <label><?=_lang("Original")?></label>
                                    <input type="text" class="sfull form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>





<?php

$data0 = array();
$script = $this->load->view('uploader/backend/html/scripts',$data0,TRUE);
AdminTemplateManager::addScript($script);


?>

