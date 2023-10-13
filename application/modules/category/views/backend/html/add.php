
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>
            </div>

        </div>

        <div class="row">

        <?php if(GroupAccess::isGranted('category',ADD_CATEGORY)): ?>
            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"><b><?=Translate::sprint("Add new category")?></b></div>
                    </div>

                    <div class="box-body">

                        <div class="form-group required">

                            <label><?=Translate::sprint("Image")?></label>
                        <?php

                            $upload_plug = $this->uploader->plugin(array(
                                "limit_key"     => "aCFiles",
                                "token_key"     => "SzZaYjEsS-4555",
                                "limit"         => 1,
                            ));

                            echo $upload_plug['html'];
                            AdminTemplateManager::addScript($upload_plug['script']);

                            ?>

                        </div>


                        <div class="form-group required">

                            <label><?=Translate::sprint("Icon")?> <span class="text-red">(PNG with transparent Background, example <a target="_blank" href="https://www.flaticon.com/premium-icon/restaurant_562678?term=food&page=1&position=1&page=1&position=1&related_id=562678&origin=search"><u>here</u></a>)</span></label> <br>


                        <?php

                            $upload_plug2 = $this->uploader->plugin(array(
                                "limit_key"     => "aaCFiles",
                                "token_key"     => "sSzZaYjEsS-4555",
                                "limit"         => 1,
                            ));

                            echo $upload_plug2['html'];
                            AdminTemplateManager::addScript($upload_plug2['script']);

                            ?>

                        </div>


                        <div class="form-group">
                            <label><?=Translate::sprint("Category name")?> <sup>*</sup> </label>
                            <input class="form-control"  id="name" type="text" placeholder="<?=Translate::sprint("Enter")?> ..."/>
                        </div>

                        <div class="form-group">
                            <label><?=Translate::sprint("Color")?> <sup>*</sup> </label>
                            <input class="form-control colorpicker1"  id="color" type="text" placeholder="<?=Translate::sprint("Enter")?> ..."/>
                        </div>


                    </div>
                    <div class="box-footer">
                        <button type="submit" id="btnAdd" class="btn btn-primary btn-flat"><?=Translate::sprint("Add")?></button>
                    </div>
                </div>


            </div>
        <?php endif; ?>

        <?php $this->load->view("category/backend/html/list");?>


        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php if(GroupAccess::isGranted('category',ADD_CATEGORY)): ?>

<?php

        $data['uploader_variable'] = $upload_plug['var'];
        $data['uploader_variable2'] = $upload_plug2['var'];

        $script = $this->load->view('category/backend/html/scripts/add-script',$data,TRUE);
        AdminTemplateManager::addScript($script);

    ?>


<?php endif; ?>