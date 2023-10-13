<?php


?>

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

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title" style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?= Translate::sprint("Packs") ?></b>
                                </div>
                                <div class="pull-right col-md-4">

                                    <a href="<?= admin_url("pack/add") ?>">
                                        <button type="button" class="btn btn-primary btn-sm pull-right"><span
                                                    class="glyphicon glyphicon-plus"></span></button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th ><?= Translate::sprint("Name", "") ?></th>
                                <th ><?= Translate::sprint("Subscription Config", "") ?></th>
                                <th ><?= Translate::sprint("Default Group Access", "") ?></th>
                                <th ><?= Translate::sprint("Price", "") ?> (<?= PAYMENT_CURRENCY ?>)</th>
                                <th ><?= Translate::sprint("Trial", "") ?></th>
                                <th ><?= Translate::sprint("Duration", "") ?></th>
                                <th >

                                </th>
                            </tr>
                            </thead>
                            <tbody>


                        <?php if (count($packs) > 0): ?>
                            <?php foreach ($packs as $pack): ?>
                                    <tr>
                                        <td>
                                        <?php
                                            echo "<b>#" . $pack->_order . "</b>&nbsp;&nbsp;";
                                            echo "<u>" . $pack->name . "</u>";
                                            if ($pack->recommended > 0)
                                                echo "&nbsp;&nbsp;<span class='badge bg-blue'>" . Translate::sprint("Recommended") . "</span>";
                                            ?>
                                        </td>

                                        <td>
                                        <?php

                                            $fields = UserSettingSubscribe::load();
                                            foreach ($fields as $field) {
                                                if ($field['_display'] == 1 and isset($pack->{$field['field_name']})) {
                                                    if ($field['field_type'] == UserSettingSubscribeTypes::INT)
                                                        echo ucfirst(Translate::sprint($field['field_name'])) . ' (' . $pack->{$field['field_name']} . ') <br>';
                                                    else if ($field['field_type'] == UserSettingSubscribeTypes::BOOLEAN) {
                                                        if ($pack->{$field['field_name']} == 1)
                                                            echo ucfirst(Translate::sprint($field['field_name'])) . ' (' . Translate::sprint('Enabled') . ') <br>';
                                                        else
                                                            echo ucfirst(Translate::sprint($field['field_name'])) . ' (' . Translate::sprint('Disabled') . ') <br>';
                                                    }
                                                }
                                            }

                                            ?>

                                        </td>

                                        <td>

                                        <?php

                                            if ($pack->grp_access_id > 0) {

                                                $grp = $this->mGroupAccessModel->getGroupAccess($pack->grp_access_id);

                                                if ($grp != NULL)
                                                    echo $grp['name'];

                                            }

                                            ?>

                                        </td>

                                        <td>
                                        <?php
                                            if ($pack->price > 0) {
                                                echo "<strong>" . Currency::parseCurrencyFormat($pack->price, PAYMENT_CURRENCY) . "</strong>";
                                            } else {
                                                echo "<strong>" . Translate::sprint("Free") . "</strong>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?= $pack->trial_period . " " . Translate::sprint("Days") ?>
                                        </td>
                                        <td>
                                            <?= $pack->duration . " " . Translate::sprint("Days") ?>
                                        </td>
                                        <td align="right">

                                        <?php if (GroupAccess::isGranted('pack', EDIT_PACK)) { ?>
                                                &nbsp;
                                                <a href="<?= admin_url("pack/edit?id=" . $pack->id) ?>"
                                                   class=" btn btn-default"
                                                   title="<?= Translate::sprint("View") ?>">
                                                    <span class="fa fa-pencil"></span>
                                                </a>
                                        <?php } ?>


                                        <?php if (GroupAccess::isGranted('pack', DELETE_PACK)) { ?>
                                                &nbsp;<a href="#" class="remove btn btn-default"
                                                   data-id="<?=$pack->id?>"
                                                   title="<?= Translate::sprint("Delete") ?>">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                        <?php } ?>

                                        </td>
                                    </tr>
                            <?php endforeach; ?>

                        <?php else: ?>
                                <tr>
                                    <td colspan="7" align="center"><?= Translate::sprint("No result") ?></td>
                                </tr>
                        <?php endif; ?>


                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-12 pull-right">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php

                                    echo $packs_pagination->links(array(), admin_url("pack/pack_manager"));

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">

                            <div class="row">

                                <div style="text-align: center">
                                    <h3 class="text-red"><?= Translate::sprint("Are you sure?") ?></h3>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left"
                                    data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                            <button type="button" id="_delete"
                                    class="btn btn-flat btn-primary"><?= Translate::sprint("OK") ?></button>
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

$script = $this->load->view('pack/backend/scripts/packs-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>

