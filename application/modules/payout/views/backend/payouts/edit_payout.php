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

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Edit payout") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- Businss owner  dropdown list -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Select owner") ?></label>
                                    <select id="select_owner" name="select_owner" class="form-control select2">
                                        <option selected="" value="<?= $payout['user_id']?>"><?= $this->mUserModel->getUserNameById($payout['user_id']) ?></option>
                                    </select>
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Payment method") ?></label>
                                    <select id="method" class="form-control select2 method"
                                            style="width: 100%;">
                                        <option selected="selected" value="Bank"><?= Translate::sprint("Bank") ?></option>
                                        <option value="Cash"><?= Translate::sprint("Cash") ?></option>
                                        <option  value="Wallet"><?= Translate::sprint("Digital Wallet") ?></option>
                                    </select>
                                </div>


                                <!-- Payment Method -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Payment Status") ?></label>
                                    <select id="status" class="form-control select2 status"
                                            style="width: 100%;">
                                        <option selected="selected" value="processing"><?= Translate::sprint("Processing") ?></option>
                                        <option  value="paid"><?= Translate::sprint("Paid") ?></option>
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Amount") ?></label>
                                    <input type="text" class="form-control" name="amount" id="amount" value="<?=$payout['amount']?>"
                                           placeholder="Ex: 8000.00$" disabled>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <!-- Note -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Note", "") ?></label>
                                    <textarea class="form-control" rows="7" id="editable-textarea"
                                              placeholder="<?= Translate::sprint("Enter") ?> ..."><?=$payout['note']?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary pull-right" id="btnEdit"><span
                                    class="glyphicon glyphicon-check"></span>
                            <?= Translate::sprint("Save Payout") ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -- -->


<?php
$data['id'] = $payout['id'];
$data['payout'] = $payout;
$script = $this->load->view('payout/backend/payouts/scripts/edit-payout-script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>