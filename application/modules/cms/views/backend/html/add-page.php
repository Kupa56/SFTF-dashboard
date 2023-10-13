<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
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
                            <b><?= Translate::sprint("Create new page") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <form >
                                <div class="col-sm-12">

                                    <div class="form-group">
                                        <label><?= Translate::sprint("Title") ?></label>
                                        <input type="text" class="form-control title" name="title" id="title"
                                               placeholder="Ex: About us, Privacy Policy ....">
                                    </div>

                                    <div class="form-group">
                                        <label><?= Translate::sprint("Slug") ?></label>
                                        <input type="text" class="form-control slug" name="slug" id="slug"
                                               placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label><?= Translate::sprint("Content") ?></label>
                                        <textarea class="form-control" rows="15" id="trumbowyg"
                                                  placeholder="<?= Translate::sprint("Enter") ?> ..."></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Template") ?></label>
                                                <select class="form-control select2 template">
                                                <?php foreach ($templates as $tem): ?>
                                                        <option value="<?=$tem?>" <?=$tem=="pages/content"?"selected":""?>><?=$tem=="pages/content"?_lang("Default template"):$tem?></option>
                                                <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="pull-right">
                            <button type="button" class="btn  btn-default" id="btnADD">
                                <?=_lang("Add")?> </button>
                            <button type="button" class="btn  btn-primary" id="btnAddPublish">
                                <span class="fa fa-paper-plane-o"></span>
                                <?=_lang("Add & Publish")?> </button>
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
$script = $this->load->view('cms/backend/scripts/add-page-script',$data0,TRUE);
AdminTemplateManager::addScript($script);


?>
