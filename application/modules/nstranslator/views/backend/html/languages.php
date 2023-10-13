<?php




?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="box  box-solid">
                    <div class="box-header">
                        <div class="box-title" style="width : 100%;">
                            <div class=" row ">
                                <div class="pull-left col-md-8">
                                    <b><?= Translate::sprint("Languages") ?></b>
                                </div>
                                <div class="pull-right col-md-4">

                                    <a href="#" data-toggle="modal" data-target="#modal-default">
                                        <button type="button" title="<?= Translate::sprint("Add New", "") ?>"
                                                class="btn btn-primary btn-sm pull-right"><span
                                                class="glyphicon glyphicon-plus"></span></button>
                                    </a>


                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body  table-responsive">
                        <div class="table-responsive">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th><?= Translate::sprint("Code") ?></th>
                                    <th><?= Translate::sprint("Name") ?></th>
                                    <th><?= Translate::sprint("Version") ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                            <?php foreach ($languages as $key => $lang): ?>
                                    <tr>
                                        <td><?=$key?></td>
                                        <td><?=$lang['name']?></td>
                                        <td><?=$lang['version']?></td>
                                        <td align="right">

                                            <a href="<?=admin_url('nstranslator/edit?lang='.$key)?>">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            &nbsp;&nbsp; &nbsp;&nbsp;
                                            <a href="<?=admin_url('nstranslator/remove?lang='.$key)?>">
                                                <i class="mdi mdi-delete"></i>
                                            </a>

                                        </td>
                                    </tr>
                            <?php endforeach; ?>


                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->


                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?=Translate::sprint('Add new Language')?></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?=Translate::sprint("Language Code")?></label>
                            <input class="form-control" name="language_code" id="_language_code" placeholder="<?=Translate::sprint("Enter")?>" />
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?=Translate::sprint("Language Name")?></label>
                            <input class="form-control" name="language_name" id="_language_name" placeholder="<?=Translate::sprint("Enter")?>" />
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?=Translate::sprint("Language Direction")?></label>
                            <select class="form-control" id="_direction">
                                <option value="ltr"><?=Translate::sprint("LTR")?></option>
                                <option value="rtl"><?=Translate::sprint("RTL")?></option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="add_new_language" class="btn btn-primary btn-flat pull-right"><?= Translate::sprint("Add") ?></button>

            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




