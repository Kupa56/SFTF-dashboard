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
            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Edit new Banner", "") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <div class="form-group">
                                    <label><?= Translate::sprint("Module") ?></label>
                                    <select id="select_module" class="form-control select2 select_module" style="width: 100%;">
                                        <option value="0"><?=Translate::sprint("Select")?></option>


                                    <?php

                                            foreach (CampaignManager::load() as $key => $module){
                                                echo " <option value=\"$key\">".Translate::sprint(ucfirst($key))."</option>";
                                            }

                                        ?>

                                        <option value="link"><?=Translate::sprint("Link")?></option>

                                    </select>
                                </div>

                            <?php  foreach (CampaignManager::load() as $key => $module): ?>

                                    <div class="form-group drop-box drop-box-<?=$key?> hidden">
                                        <label><?=Translate::sprint("Select","")?> <?=Translate::sprint($key)?></label>
                                        <select class="form-control select2 select-<?=$key?>" style="width: 100%;">
                                            <option selected="selected" value="0">
                                                <?=Translate::sprint("Select $key")?></option>
                                        </select>
                                    </div>

                            <?php endforeach; ?>


                                <div class="form-group drop-box drop-box-link hidden">
                                    <label><?= Translate::sprint("Link", "") ?></label>
                                    <input type="text" class="form-control" name="link" id="link"
                                           placeholder="Ex: http://www.google.com" value="<?=($banner['module']=="link"? $banner['module_id'] : "") ?>">
                                </div>

                                <div class="form-group">
                                    <label><?= Translate::sprint("Title", "") ?></label>
                                    <input type="text" class="form-control" name="title" id="title"
                                           placeholder="Ex: black friday" value="<?=$banner['title']?>">
                                </div>

                                <div class="form-group">
                                    <label><?= Translate::sprint("Description", "") ?></label>
                                    <textarea id="description" class="form-control" placeholder="<?= Translate::sprint("Enter") ?> ..."><?=$banner['description']?></textarea>
                                </div>

                                <!--                                <select class="form-control" id="tags"  multiple="multiple" placeholder>-->
                                <!--                                </select>-->


                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-sm-6">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-solid">
                            <div class="box-header">

                                <div class="box-title">
                                    <b><?= Translate::sprint("Images", "") ?></b>
                                </div>

                            </div>


                            <div class="box-body">
                                <!-- text input -->
                                <div class="form-group required">

                                <?php


                                    $images = $banner['image'];
                                    if ($images != "" AND !is_array($images)) {
                                        $images = json_decode($images);
                                    }

                                    ?>

                                <?php

                                    $upload_plug = $this->uploader->plugin(array(
                                        "limit_key"     => "asOhFiles",
                                        "token_key"     => "SszYjEsS-4555",
                                        "limit"         => 1,
                                        "cache"         => $images,
                                    ));

                                    echo $upload_plug['html'];
                                    AdminTemplateManager::addScript($upload_plug['script']);

                                    ?>


                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="box box-solid">
                            <div class="box-header">

                                <div class="box-title">
                                    <b><?= Translate::sprint("Scheduling", "") ?></b>
                                    <span style="color: grey;font-size: 11px;">(  <?php echo Translate::sprint("For promotion offers, leave this field as the default ", ""); ?>)</span>
                                </div>

                            </div>

                            <div class="box-body">
                                <!-- text input -->
                                <div class="col-sm-12 ">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label><input id="enable_scheduling" type="checkbox"> <?=Translate::sprint("Enable Scheduling")?></label>
                                        </div>
                                        <div class="col-md-6 hidden scheduling_date_begin">
                                            <label> <?= Translate::sprint("Date Begin", "") ?> </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="mdi mdi-calendar"></i>
                                                </div>
                                                <input class="form-control" data-provide="datepicker"
                                                       placeholder="YYYY-MM-DD" type="text"
                                                       name="date_b"
                                                       id="date_b"
                                                       value="<?= date("Y-m-d", time()) ?>"/>
                                            </div>

                                        </div>
                                        <div class="col-md-6 hidden scheduling_date_end">
                                            <label>
                                                <?= Translate::sprint("Date End") ?> </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="mdi mdi-calendar"></i>
                                                </div>
                                                <input class="form-control" data-provide="datepicker"
                                                       type="text" placeholder="YYYY-MM-DD"
                                                       name="date_e"
                                                       id="date_e"/>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn  btn-primary" id="btnSave"><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php


$data['uploader_variable'] = $upload_plug['var'];
$data['banner'] = $banner;

$script = $this->load->view('nsbanner/backend/scripts/edit-script',$data,TRUE);
AdminTemplateManager::addScript($script);

?>
