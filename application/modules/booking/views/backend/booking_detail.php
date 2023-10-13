<?php

$invoice = $this->mBookingPayment->getInvoice($reservation['id']);

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
                <div class="box box-solid">
                    <div class="box-header" style="width : 100%;">
                        <div class=" row ">
                            <div class="pull-left col-md-12 box-title">
                                <b><?= Translate::sprint("Booking Detail") ?> #<?= $reservation['id'] ?></b>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <div class="btn-group">
                                    <?php if($reservation['status_id'] == 0): ?>

                                            <a class="btn btn-primary bg-green-gradient" id="edit-status-confirm"
                                               href="#"><i
                                                        class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Confirm") ?>
                                            </a>

                                            <a class="btn btn-primary bg-color-yellow" id="edit-status-decline"
                                               href="#"><i
                                                        class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Decline") ?>
                                            </a>
                                    <?php else: ?>
                                            <a class="btn btn-primary bg-gray" id="edit-status" href="#"><i
                                                        class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Edit") ?>
                                            </a>
                                    <?php endif; ?>

                                    <?php if (GroupAccess::isGranted("messenger")): ?>
                                            <a class="btn btn-primary bg-gray"
                                               href="<?= admin_url("messenger/messages/?username=" . $this->mUserModel->getFieldById("username", $reservation['user_id'])) ?>"><i
                                                        class="mdi mdi-email-outline"></i>&nbsp;&nbsp;<?= _lang("Inbox") ?>
                                            </a>
                                    <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h4 style="font-family: 'Montserrat' !important"> <?= _lang("Booking") ?>
                                            #<?= str_pad($reservation['id'], 6, 0, STR_PAD_LEFT) ?></h4>
                                        <?= _lang("Booking date") ?>
                                        : <?= date("D M Y h:i:s A", strtotime($reservation['updated_at'])) ?><br>
                                    </div>
                                    <div class="box-body">


                                        <table class="table">
                                            <tr>
                                                <td><strong><?= _lang("Status") ?></strong>: </td>
                                                <td>
                                                <?php

                                                    if (isset($reservation['status']) && $reservation['status'] != "") {
                                                        $statusParser = explode(";", $reservation['status']);
                                                        echo "<span class=badge style='background:" . $statusParser[1] . "'>" . $statusParser[0] . "</span>";
                                                    }

                                                    ?>
                                                </td>
                                            </tr>

                                    <?php if (ModulesChecker::isEnabled("booking_payment")
                                            && ModulesChecker::isEnabled("payment")
                                            && GroupAccess::isGranted("booking", GRP_MANAGE_BOOKING_CONFIG)): ?>


                                                <tr>
                                                <?php   $store = $this->mBookingModel->getStore($reservation['store_id']); ?>
                                                    <td><strong><?=_lang("Business")?></strong>:&nbsp;</td>
                                                    <td>
                                                        <a target="_blank"
                                                           href="<?= admin_url("user/edit?id=" . $store['user_id']) ?>"><?= $store['name'] ?>
                                                            (<?= ucfirst($this->mUserModel->getUserNameById($store['user_id'])) ?>
                                                            )
                                                            <i class="mdi mdi-open-in-new"></i></a>
                                                    </td>
                                                </tr>


                                    <?php endif; ?>

                                    <?php if (ModulesChecker::isEnabled("booking_payment")): ?>

                                            <tr>
                                                <td><strong> <?= _lang("Payment") ?></strong>:&nbsp;</td>
                                                <td>
                                                <?php

                                                    $invoice = $this->mBookingModel->getInvoice($reservation['id']);

                                                    $pcode = $reservation['payment_status'];
                                                    $payments = Booking_payment::PAYMENT_STATUS;
                                                    if (isset($payments[$pcode])) {
                                                        echo "<span class='' style='color: " . $payments[$pcode]['color'] . "'>" . ucfirst(_lang($payments[$pcode]['label'])) .( ($invoice->method != "" && $pcode=="paid")?" (".$invoice->method.") ":""). "</span>";
                                                    } else if ($pcode == "cod_paid") {
                                                        echo "<span class='text-green'>" . ucfirst(_lang("Paid with cash")) . "</span>";
                                                    }

                                                    ?>

                                                <?php if(($pcode != "cod_paid" && $pcode != "paid")
                                                        OR GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING_CONFIG)): ?>
                                                        &nbsp;&nbsp;<a href="#" id="edit-payment-status"><i
                                                                    class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Edit payment") ?>
                                                        </a>
                                                <?php endif; ?>
                                                </td>
                                            </tr>

                                        <?php if ($invoice != NULL && $invoice->transaction_id != "") : ?>
                                            <tr>
                                                <td><strong><?=_lang("Transaction ID")?></strong>:&nbsp;</td>
                                                <td>
                                                    <?=$invoice->transaction_id?>
                                                </td>
                                            </tr>
                                                <tr>
                                                    <td><strong><?=_lang("Payment method")?></strong>:&nbsp;</td>
                                                    <td><?=_lang( is_numeric($invoice->method)?_lang(PaymentsProvider::findKeyById($invoice->method)):$invoice->method)?></td>
                                                </tr>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                        </table>

                                    </div>


                                </div>

                            </div>
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5 pull-right">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h4 style="font-family: 'Montserrat' !important"><?= _lang("Client Information") ?></h4>
                                    </div>
                                    <div class="box-body">

                                    <?php

                                        $cf_id = intval($reservation['cf_id']);
                                        $reservation['cf_data'] = json_decode($reservation['cf_data'], JSON_OBJECT_AS_ARRAY);
                                        if (isset($reservation['cf_data'])){

                                            $cf_object = CFManagerHelper::getByID($cf_id);
                                            $fields = json_decode($cf_object['fields'],JSON_OBJECT_AS_ARRAY);

                                            foreach ($fields as $key => $field) {

                                                $data = $reservation['cf_data'][ $field['label'] ];

                                                if ($data == "")
                                                    $data = "--";


                                                if ( $field['type'] == "input.location") {

                                                    if ($key == "") {
                                                        echo "<span><strong>".$field['label']."</strong>: -- </span><br>";
                                                    } else {

                                                        if (preg_match("#;#", $data)) {
                                                            $l = explode(";", $data);
                                                            echo "<span><strong>".$field['label']."</strong>: <a class='loc-detail' href='#' data-address='$l[0]' data-lat='$l[1]' data-lng='$l[2]'><i class='mdi mdi-map-marker'></i>&nbsp;&nbsp;$l[0]</a> </span><br>";
                                                        } else {
                                                            echo "<span><strong>".$field['label']."</strong>: $data </span><br>";
                                                        }

                                                    }
                                                } else
                                                    echo "<span><strong>".$field['label']."</strong>: $data</span><br>";

                                            }
                                        }

                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                       <div class="row">
                           <div class="col-sm-12" style="margin-top: 20px">
                               <div class="col-xs-12 table-responsive">
                                   <table class="table table-striped">
                                       <tbody>
                                       <tr style="text-transform: uppercase">
                                           <th><?= _lang("Service(s)") ?></th>
                                           <th align="right" width="20%"
                                               class="right-align"><?= _lang("Price per item") ?></th>
                                           <th align="right" width="20%" class="right-align"><?= _lang("Amount") ?></th>
                                       </tr>


                                   <?php
                                       $cart = json_decode($reservation['cart'], JSON_OBJECT_AS_ARRAY);

                                       $sub_total = 0;
                                       $currency = DEFAULT_CURRENCY;

                                       ?>

                                   <?php if(count($cart)>0): ?>

                                       <?php foreach ($cart as $item): ?>
                                               <tr>
                                                   <td>
                                                   <?php

                                                       if(empty($item))
                                                           continue;


                                                       $callback = NSModuleLinkers::find($item['module'], 'getData');

                                                       if ($callback != NULL) {

                                                           $params = array(
                                                               'id' => $item['module_id']
                                                           );

                                                           $result = call_user_func($callback, $params);

                                                           echo $result['label'];

                                                           if (isset($item['options']))
                                                               echo BookingHelper::optionsBuilderString($item['options']);

                                                       }


                                                       ?>
                                                   </td>


                                                   <td align="right" valign="top">
                                                   <?php

                                                        echo Currency::parseCurrencyFormat($item['amount'], DEFAULT_CURRENCY);
                                                       ?>
                                                   </td>
                                                   <td align="right" valign="top">
                                                   <?php

                                                       echo Currency::parseCurrencyFormat($item['amount'] * $item['qty'], DEFAULT_CURRENCY);
                                                       $sub_total = $sub_total + $item['amount'] * $item['qty'];

                                                       ?>
                                                   </td>

                                               </tr>
                                       <?php endforeach; ?>

                                   <?php else: ?>

                                           <tr>
                                               <td><?= _lang("No services") ?></td>
                                           </tr>

                                   <?php endif; ?>


                                       </tbody>
                                   </table>
                               </div>

                               <div class="clearfix" style="margin-bottom: 20px;"></div>

                               <div class="col-sm-4">
                               </div>

                               <div class="col-sm-4">
                               </div>

                               <div class="col-md-4">
                                   <table class="table table-hover">
                                       <tbody>
                                       <tr id="sub_amount">
                                           <th width="40%"><span class="margin"><?= _lang("SUBTOTAL") ?></span></th>
                                           <td width="60%" align="right">
                                               <strong id="amount_init" style="font-size: 17px;">
                                               <?php
                                                   echo Currency::parseCurrencyFormat($sub_total, $currency);
                                                   ?>
                                               </strong>
                                           </td>
                                       </tr>


                                   <?php if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0): ?>

                                       <?php

                                           $percent = 0;
                                           $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                                           if ($tax != NULL) {
                                               $percent = $tax['value'];
                                           }

                                           $tax_value = (($percent / 100) * $sub_total);
                                           $sub_total = $tax_value + $sub_total;

                                           ?>

                                           <tr>
                                               <td>
                                                   <span class="margin"><?= $tax['name'] ?>(<?= intval($percent) ?>%)</span>
                                               </td>
                                               <td align="right">
                                                   <b><?= Currency::parseCurrencyFormat($tax_value, $currency) ?></b>
                                               </td>
                                           </tr>

                                   <?php endif; ?>



                                       <tr>
                                           <th>
                                               <span class="margin"><?= _lang("TOTAL") ?></span>
                                           <?php if (isset($percent) && $percent > 0): ?>
                                                   <br/>
                                                   <span class="margin text-grey2"><i><?= _lang("Tax included") ?></i></span>
                                           <?php endif; ?>
                                           </th>
                                           <td align="right" id="currency">
                                               <strong id="amount_total" style="font-size: 17px;">
                                               <?php
                                                   echo Currency::parseCurrencyFormat($sub_total, $currency);
                                                   ?>
                                               </strong>
                                           </td>
                                       </tr>

                                       </tbody>
                                   </table>
                               </div>

                           </div>
                       </div>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
</div>


<div class="modal fade" id="modal-location-detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Location Detail") ?></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div id="loc-address" style="padding-bottom: 15px;padding-left: 15px;">
                        <strong><?= _lang("Address") ?></strong>: <span></span></div>
                    <div id="loc-maps" style="width:100%;height:300px;margin-bottom: 15px"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal"
                        class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("DONE") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="modal-edit-status">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Edit Status") ?></h4>
            </div>
            <div class="modal-body">

            <?php

                $status = array(
                        0 => "pending",
                        1 => "confirmed",
                        -1 => "canceled",
                )
                ?>

                <div class="form-group">
                    <label><?= _lang("Select Order status") ?></label>
                    <select class="form-control select2" id="select2-order-status">
                    <?php foreach ($status as $id => $s): ?>
                            <option value="<?= $id ?>" <?= $id == $reservation['status_id'] ? "selected" : "" ?>><?= _lang($s) ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group hidden message_container">
                    <label><?= _lang("Include a message to the client") ?></label>
                    <textarea class="form-control" id="c_message"
                              placeholder="<?= _lang("Enter message...") ?>"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal"
                        class="btn btn-flat btn-default pull-left"><?= Translate::sprint("CANCEL") ?></button>
                <button type="button" id="update-status"
                        class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("SAVE") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="modal-edit-payment-status">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Edit Payment") ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?= _lang("Select status") ?></label>
                    <select class="form-control select2" id="select2-payment-status">
                    <?php foreach (Booking_payment::PAYMENT_STATUS as $k => $ps): ?>
                            <option value="<?= $k ?>" <?= $k == $reservation['payment_status'] ? "selected" : "" ?>><?= _lang($ps['label']) ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>


            <?php if (ModulesChecker::isEnabled("booking_payment")): ?>
                    <div class="form-group">
                        <label><?= _lang("Transaction ID") ?></label>
                        <input class="form-control" type="text" id="transactionId" value="<?=$invoice->transaction_id?>" <?=$invoice->transaction_id!=""?"disabled":""?> />
                    </div>
            <?php endif; ?>


            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal"
                        class="btn btn-flat btn-default pull-left"><?= Translate::sprint("CANCEL") ?></button>
                <button type="button" id="update-payment-status"
                        class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("SAVE") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php

$data = array();
$data["booking_id"] = $reservation['id'];
$script = $this->load->view('booking/backend/scripts/booking-detail-script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>
