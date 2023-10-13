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
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title" style="width : 100%;">

                            <div class=" row ">
                                <div class="pull-left col-md-8">
                                    <b><?= Translate::sprint("Custom fields") ?></b>
                                </div>
                                <div class="pull-right col-md-4">
                                    <a href="<?= admin_url("cf_manager/add") ?>">
                                        <button type="button" data-toggle="tooltip"
                                                title="<?= Translate::sprint("Add new Checkout Fields", "") ?> "
                                                class="btn btn-primary btn-sm pull-right"><span
                                                class="glyphicon glyphicon-plus"></span></button>
                                    </a>


                                </div>
                                <!--  DENY ACCESS TO ROLE "GUEST" -->
                            </div>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="cf_list" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="70%"><?= Translate::sprint("Label") ?></th>
                                <th width="25%"><?= Translate::sprint("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>

                        <?php if (!empty($list)) : ?>

                            <?php foreach ($list as $value): ?>

                                <tr>
                                    <td><?=$value['id']?></td>
                                    <td><?=$value['label']?></td>
                                    <td>
                                    <?php if($value['editable']==1):?>
                                        <a class="btn" href="<?=admin_url("cf_manager/edit?id=".$value['id'])?>"><span class="glyphicon glyphicon-edit"></span></a>
                                        &nbsp;&nbsp;<button class="btn remove" data-id="<?=$value['id']?>"><span class="glyphicon glyphicon-trash"></span></button>
                                    <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach;?>

                        <?php else: ?>
                                <tr>
                                    <td colspan="3" align="center">
                                        <div
                                            style="text-align: center"><?= Translate::sprint("No data found", "") ?></div>
                                    </td>
                                </tr>

                        <?php endif; ?>
                            </tbody>
                        </table>

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

<div class="modal fade" id="alert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?=_lang("Alert!")?></h4>
            </div>
            <div class="modal-body">

                <p class="text-red"> <?= Translate::sprint("Are you sure to do this operation?") ?></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                <button type="button" id="apply"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Confirm") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php

$script = $this->load->view('cf_manager/backend/scripts/list-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>
