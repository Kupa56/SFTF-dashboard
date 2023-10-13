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
                                    <b><?= Translate::sprint("Coupons") ?></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?= Translate::sprint("Offer") ?></th>
                                <th><?= Translate::sprint("Code") ?></th>
                                <th><?= Translate::sprint("User") ?></th>
                                <th><?= Translate::sprint("Status") ?></th>
                                <th><?= Translate::sprint("Created at") ?></th>
                                <th><?= Translate::sprint("Updated at") ?></th>
                                <th>

                                </th>
                            </tr>
                            </thead>
                            <tbody>


                        <?php if (count($result['result']) > 0): ?>
                            <?php foreach ($result['result'] as $coupon): ?>
                                    <tr>
                                        <td>
                                            <?=$coupon['label']?>
                                        </td>
                                        <td>
                                            <?=$coupon['code']?>
                                        </td>
                                        <td>
                                            <?=$coupon['user_coupon']?>
                                        </td>

                                        <td>

                                        <?php if($coupon['status'] == 0): ?>
                                                <span class="badge bg-orange"><?=_lang("Unverified")?></span>
                                        <?php elseif($coupon['status'] == -1): ?>
                                                <span class="badge bg-red"><?=_lang("Canceled")?></span>
                                        <?php elseif($coupon['status'] == 2): ?>
                                                <span class="badge bg-blue"><?=_lang("Used")?></span>

                                        <?php else: ?>
                                                <span class="badge bg-green"><?=_lang("Verified")?></span>
                                        <?php endif; ?>

                                        </td>
                                        <td>
                                            <?=date("d M Y H:i",strtotime($coupon['created_at']))?>
                                        </td>
                                        <td>
                                            <?=date("d M Y H:i",strtotime($coupon['updated_at']))?>
                                        </td>
                                        <td>

                                            <a href="#"
                                               data-update-status
                                               data-id="<?=$coupon['id']?>"
                                               data-offer-id="<?=$coupon['offer_id']?>"
                                               class=" btn btn-default"
                                               title="<?= Translate::sprint("Update") ?>">
                                                <span class="fa fa-pencil"></span>
                                            </a>

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

                                    echo $result['pagination']->links(array(
                                            "id" => intval(RequestInput::get("id"))
                                    ), admin_url("qrcoupon/coupons"));

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>


            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="update-coupon-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b><?=_lang("Update coupon")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label><?=_lang("Change status")?></label>
                    <select id="select_coupon_status" class="select2 form-control">
                        <option value="0"><?=_lang("Unverified")?></option>
                        <option value="1"><?=_lang("Verified")?></option>
                        <option value="2"><?=_lang("Used")?></option>
                        <option value="-1"><?=_lang("Canceled")?></option>
                    </select>
                </div>

                <input type="hidden" id="data-id">
                <input type="hidden" id="data-offer-id">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel")?></button>
                <button type="button" id="saveChange" class="btn btn-flat btn-primary"><?=Translate::sprint("Update")?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php

$script = $this->load->view('qrcoupon/backend/scripts/update-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>

