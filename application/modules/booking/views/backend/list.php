<?php
$reservations = $data[Tags::RESULT];
$pagination = $data['pagination'];
$this->load->model("user/user_model", "mUserModel");
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
                            <div class="pull-left col-md-8 box-title">
                                <b><?= Translate::sprint("Reservations") ?></b>
                            <?php
                                $CI =& get_instance();
                                $url = $CI->config->site_url($CI->uri->uri_string());
                                $query_uri = $_SERVER['QUERY_STRING'];
                                if ($query_uri != ""): ?>
                                    <a href="<?= current_url() ?>"><span
                                                class="badge bg-red"><i
                                                    class="mdi mdi-close"></i>&nbsp;&nbsp;<?= _lang("Clear filter") ?></span></a>
                            <?php endif; ?>
                            </div>
                            <div class="pull-right col-md-4">
                                <div class="row">
                                    <div class="pull-right col-sm-4">

                                        <a href="#" data-toggle="modal" data-toggle="tooltip"
                                           data-target="#modal-default-filter">
                                            <button type="button"
                                                    title="<?= Translate::sprint("Filter") ?>"
                                                    class="btn btn-primary btn-sm pull-right">
                                                <span class="glyphicon glyphicon-filter"></span>
                                            </button>
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?= Translate::sprint("Order ID") ?></th>
                                <th><?= Translate::sprint("Client") ?></th>
                                <th><?= Translate::sprint("Business/Owner") ?></th>
                                <th><?= Translate::sprint("Status") ?></th>
                                <th><?= Translate::sprint("Payment") ?></th>
                                <th><?= Translate::sprint("Subtotal") ?></th>
                                <th><?= Translate::sprint("Date") ?></th>
                                <th>

                                <?php

                                    $export_plugin = $this->exim_tool->plugin_export(array(
                                        'module' => 'orders'
                                    ));

                                    echo $export_plugin['html'];
                                    AdminTemplateManager::addScript($export_plugin['script']);

                                    ?>

                                </th>
                            </tr>
                            </thead>
                            <tbody id="list">

                        <?php

                            $total_commission = 0;
                            $total_amount = 0;

                            ?>
                        <?php if (!empty($reservations)) : ?>

                            <?php foreach ($reservations as $key => $reservation): ?>

                                <?php
                                        $token = $this->mUserBrowser->setToken(Text::encrypt($reservation['id']));
                                    ?>

                                    <tr class="store_<?= $token ?>" role="row" class="odd">

                                        <td>
                                            <span style="font-size: 14px">  <b> <?= "#" . str_pad($reservation['id'], 6, 0, STR_PAD_LEFT) ?> </b> </span>
                                        </td>
                                        <td>
                                            <u><?= ucfirst(  textClear($this->mUserModel->getFieldById("name", $reservation['user_id']))  ) ?></u>

                                        <?php if (GroupAccess::isGranted("user", MANAGE_USERS)): ?>
                                                &nbsp;&nbsp;<a target="_blank"
                                                               href="<?= admin_url("user/edit?id=" . $reservation['user_id']) ?>"><i
                                                            class="mdi mdi-open-in-new"></i></a>
                                        <?php endif; ?>

                                        </td>

                                        <td>
                                        <?php
                                            $store = $this->mBookingModel->getStore($reservation['store_id']);
                                            echo $store['name'];
                                            ?>
                                            <br/>
                                            <a target="_blank"
                                               href="<?= admin_url("user/edit?id=" . $store['user_id']) ?>"><?= ucfirst($this->mUserModel->getUserNameById($store['user_id'])) ?>
                                                <i class="mdi mdi-open-in-new"></i>
                                            </a>
                                        </td>

                                        <td>

                                        <?php

                                            if (isset($reservation['status']) && $reservation['status'] != "") {
                                                $statusParser = explode(";", $reservation['status']);
                                                echo "<span class=badge style='background:" . $statusParser[1] . "'>" . _lang($statusParser[0] ). "</span>";
                                            }
                                            ?>
                                        </td>


                                        <td>
                                        <?php

                                            $pcode = $reservation['payment_status'];
                                            $payments = Booking_payment::PAYMENT_STATUS;
                                            if (isset($payments[$pcode])) {
                                                echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>" . ucfirst(_lang($payments[$pcode]['label'])) . "</span>";
                                            }else if($pcode == "cod_paid"){
                                                echo "<span class='badge bg-green'>"._lang("Paid with cash")."</span>";
                                            }

                                            ?>
                                        </td>


                                        <td>

                                        <?php

                                            $cart = json_decode($reservation['cart'], JSON_OBJECT_AS_ARRAY);
                                            $sub_total = 0;
                                            $currency = DEFAULT_CURRENCY;

                                            $commission = 0;


                                            foreach ($cart as $item) {

                                                if(empty($item))
                                                    continue;

                                                $callback = NSModuleLinkers::find($item['module'], 'getData');

                                                if ($callback != NULL) {

                                                    $params = array(
                                                        'id' => $item['module_id']
                                                    );

                                                    $result = call_user_func($callback, $params);

                                                }

                                                $sub_total = $sub_total + ($item['amount'] * $item['qty']);
                                                $total_amount = $total_amount + $sub_total;
                                            }

                                            if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {

                                                $percent = 0;
                                                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                                                if ($tax != NULL) {
                                                    $percent = $tax['value'];

                                                }

                                            }

                                            echo "<b>" . Currency::parseCurrencyFormat($sub_total, $currency) . "</b>";


                                            ?>

                                        </td>



                                        <td>
                                            <span>  <?= MyDateUtils::convert($reservation['updated_at'], "UTC", TimeZoneManager::getTimeZone(), "d M, Y h:i:s A") ?>  </span>
                                        </td>

                                        <td align="right">

                                            <a class="btn btn-default" data-toggle="tooltip"
                                               href="<?= admin_url("booking/view?id=" . $reservation['id']) ?>"
                                               title="<?= Translate::sprint("Edit") ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>

                                        </td>

                                    </tr>


                                <?php


                                    $store = $this->mBookingModel->getStore($reservation['store_id']);
                                    $statusParser = explode(";", $reservation['status']);

                                    $array  = array(
                                        'booking_id' => "#" . str_pad($reservation['id'], 6, 0, STR_PAD_LEFT),
                                        'client' => ucfirst($this->mUserModel->getFieldById("name", $reservation['user_id'])),
                                        'client_phone' => ucfirst( textClear($this->mUserModel->getFieldById("telephone", $reservation['user_id']))  ),
                                        'business_owner' => ucfirst(    textClear($this->mUserModel->getUserNameById($store['user_id']))    ),
                                        'status' => $statusParser[0],
                                        'date' => $reservation['updated_at'],
                                    );

                                    echo Exim_toolManager::setupRows($array);
                                    ?>

                            <?php endforeach; ?>

                        <?php else: ?>
                                <tr>
                                    <td colspan="7" align="center">
                                        <div style="text-align: center"><?= Translate::sprint("No data found", "") ?></div>
                                    </td>
                                </tr>

                        <?php endif; ?>


                            </tbody>
                        </table>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php
                                    echo $pagination->links(array(
                                        "status" => intval(RequestInput::get("status")),
                                        "search" => RequestInput::get("search"),
                                        "owner_id" => intval(RequestInput::get("owner_id")),
                                    ), $pagination_url);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->


            <!-- /.box -->
        </div>
        <!-- /.col -->
        <!-- /.row -->
</div>


<!--  Model popup : begin-->
<div class="modal fade" id="modal-default-filter">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Filter order") ?> </h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6">

                    <?php if (GroupAccess::isGranted('booking', GRP_MANAGE_BOOKING_CONFIG)): ?>

                            <div class="form-group">
                                <label><?= _lang("Business Owner") ?></label>
                                <select id="select_owner" name="select_owner" class="form-control select2">
                                    <option selected="" value="0">-- <?= Translate::sprint("Select owner") ?></option>
                                </select>
                            </div>

                    <?php endif; ?>

                    <?php $status = array() ?>

                        <div class="form-group">
                            <label><?= _lang("Select date") ?></label>
                            <input type="text" class="form-control" name="datefilter" placeholder="Range date"
                                   value=""/>
                        </div>

                        <div class="form-group">
                            <label><?= _lang("Order Status") ?></label>
                            <select id="select_order_status" class="form-control select2">
                                <option selected="" value="0">-- <?= Translate::sprint("Select") ?></option>
                            <?php foreach ($status as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['label'] ?></option>
                            <?php endforeach; ?>

                            </select>
                        </div>


                        <div class="form-group">
                            <label><?= _lang("Limit") ?></label>
                            <input type="number" class="form-control" name="limit" id="limit"
                                   value="<?= NO_OF_ITEMS_PER_PAGE ?>"/>
                        </div>


                    </div>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel") ?></button>
                <button type="button" id="_filter"
                        data=""
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Apply") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--  Model popup : end-->


<?php
$script = $this->load->view('booking/backend/scripts/reservations-script', NULL, TRUE);
AdminTemplateManager::addScript($script);
?>
