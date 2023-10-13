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
                                    <b><?= Translate::sprintf("Edit \"%s\"", array($lang)) ?></b>
                                </div>
                                <div class="pull-right col-md-4">

                                    <a href="#" data-toggle="modal" data-target="#modal-default">
                                        <button type="button" title=""
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
                            <form>
                                <table id="list_languages" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <!--<th>ID</th>-->
                                        <th width="50%"><?= Translate::sprint("Key") ?></th>
                                        <th width="50%"><?= Translate::sprint("Value") ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                <?php foreach ($merged_data as $key => $value): ?>
                                        <tr>
                                            <td><?=$key?></td>
                                            <td>
                                                <input data-key="<?=$key?>" class="lang-input form-control width100p" type="text" value="<?=$value?>">
                                                <span class="invisible"><?=$value?></span>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>


                                    </tbody>
                                </table>

                                <hr />
                                <div class="form-group col-md-4">
                                    <input type="text" class="form-control" id="_name" value="<?=$config['name']?>">
                                </div>

                                <div class="form-group col-md-4">
                                    <select class="form-control" id="_direction">
                                        <option value=""><?=Translate::sprint("Language Direction")?></option>
                                        <option value="ltr"><?=Translate::sprint("LTR")?></option>
                                        <option value="rtl"><?=Translate::sprint("RTL")?></option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="number" class="form-control" id="_version" value="<?=$config['version']?>">
                                </div>

                            </form>

                        </div>

                    </div>
                    <div class="box-footer">
                        <button id="save" class="btn btn-primary pull-right">
                            <i class="mdi mdi-content-save-outline"></i>&nbsp;&nbsp;<?=Translate::sprint('Save')?>
                        </button>
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
                <h4 class="modal-title"> <?=Translate::sprint('Add new')?></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label><?=Translate::sprint("Key")?></label>
                            <input class="form-control" name="key" id="_key" placeholder="<?=Translate::sprint("Enter")?>" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label><?=Translate::sprint("Value")?></label>
                            <input class="form-control" name="key" id="_value" placeholder="<?=Translate::sprint("Enter")?>" />
                            <input type="hidden" id="_code" value="<?=$lang?>" />
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="add_new_key" class="btn btn-primary btn-flat pull-right"><?= Translate::sprint("Add") ?></button>

            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




