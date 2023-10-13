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
                            <b><?=  $data['title'] = Translate::sprintf("Edit page \"%s\"",array($content['title']));                             ?></b>
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
                                               placeholder="Ex: About us, Privacy Policy ...." value="<?=$content['title']?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?= Translate::sprint("Slug") ?></label>
                                        <input type="text" class="form-control slug" name="slug" id="slug"
                                               placeholder="" value="<?=$content['slug']?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?= Translate::sprint("Content") ?></label>
                                        <textarea class="form-control" rows="15" id="trumbowyg"
                                                  placeholder="<?= Translate::sprint("Enter") ?> ..." ><?=Text::output($content['content'])?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Template") ?></label>
                                                <select class="form-control select2 template">
                                                <?php foreach ($templates as $tem): ?>
                                                        <option value="<?=$tem?>" <?=$tem==$content['template']?"selected":""?>><?=$tem=="pages/content"?_lang("Default template"):$tem?></option>
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

                            <select class="select2 publish_status">
                                <option value="1" <?=$content['status']==1?"selected":""?>><?=_lang("Publish")?></option>
                                <option value="-1" <?=$content['status']==-1?"selected":""?>><?=_lang("Unpublish")?></option>
                            </select>

                            <button type="button" class="btn  btn-primary" id="btnSave">
                                <span class="fa fa-paper-plane-o"></span>
                                <?=_lang("Save")?> </button>
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
$data0['id'] = $content['id'];

$script = $this->load->view('cms/backend/scripts/edit-page-script',$data0,TRUE);
AdminTemplateManager::addScript($script);


?>
