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
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>
            </div>
        </div>

        <div class="row">

            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"  style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?=Translate::sprint("Balance")?></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body box-title">
                        <strong><?=_lang("Remaining account credits")?></strong>
                        <div class="box-title">
                        <?php
                            $balance = $this->mWalletModel->getBalance(SessionManager::getData('id_user'))
                            ?>
                            <b class="font-size20px <?=$balance==0?"text-red":""?>">
                            <?php
                                    echo Currency::parseCurrencyFormat(
                                        $balance,
                                        DEFAULT_CURRENCY
                                    )
                                ?>
                            </b>

                            &nbsp;&nbsp;<a data-toggle="modal" data-target="#add-balance-modal" href="#"><i class="mdi mdi-plus"></i><?=_lang("Top-up")?></a>
                        </div>
                    </div>

                </div>
                <!-- /.box -->
            </div>


            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"  style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?=Translate::sprint("Invoices")?></b>
                                </div>
                                <div class="pull-right col-md-4"></div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <!--    <th>ID</th>-->
                                <th width="5%"><strong>#</strong></th>
                                <th width="15%"><?=Translate::sprint("Amount")?></th>
                                <th width="10%"><?=Translate::sprint("Tax")?></th>
                                <th width="15%"><?=Translate::sprint("Items","")?></th>
                                <th width="10%"><?=Translate::sprint("Date")?></th>
                                <th width="35%"></th>
                                <th width="10%"></th>
                            </tr>
                            </thead>
                            <tbody>
                        <?php  if(count($unpaid_invoices)>0){ ?>

                                <tr>
                                    <td colspan="7" align="left">
                                        <strong><?=Translate::sprint("Unpaid invoices")?></strong>
                                    </td>
                                </tr>

                            <?php foreach ($unpaid_invoices as $value): ?>

                                    <tr>
                                        <td><strong><?=$value['id']?></strong></td>
                                    <?php
                                            if($value['tax_id']>0){
                                                $tax = $this->mTaxModel->getTax($value['tax_id']);
                                                if($tax!=NULL){
                                                    $tax_value = ( ($tax['value']/100)*$value['amount'] );
                                                  //  $value['amount'] = $value['amount']+$tax_value;
                                                 }
                                            }else if($value['tax_id']==-2) {
                                                $litTaxes = json_decode($value['taxes'], JSON_OBJECT_AS_ARRAY);
                                                $litTaxes = is_array($litTaxes) ? $litTaxes : array($litTaxes);
                                                $newAmount = $value['amount'];
                                                foreach ($litTaxes as $val) {
                                                    $percent = 0;
                                                    $mTax = $this->mTaxModel->getTax($val);
                                                    if ($mTax != NULL) {
                                                        $newAmount = (($mTax['value'] / 100) * $value['amount']) + $newAmount;
                                                    }
                                                }
                                            }


                                        ?>
                                        <td><?=Currency::parseCurrencyFormat($value['amount'],$value['currency'])?></td>
                                        <td>
                                        <?php
                                                if(isset($tax))
                                                    echo $tax['name'].' '.$tax['value'].'%';
                                                else if(isset($litTaxes))
                                                    foreach ($litTaxes as $val) {
                                                        $mTax = $this->mTaxModel->getTax($val);
                                                        if ($mTax != NULL) {
                                                            echo $mTax['name'].' '.$mTax['value'].'%<br>';
                                                        }
                                                    }
                                                 else
                                                    echo '--'
                                            ?>
                                        </td>
                                        <td>
                                        <?php

                                                echo "<strong>"._lang($value['module'])."</strong>: <br/>";
                                                $items = json_decode($value['items'],JSON_OBJECT_AS_ARRAY);
                                                foreach ($items as $item){
                                                    if($value['module']=="wallet"){
                                                        echo Translate::sprintf($item['item_name'],array(
                                                                Currency::parseCurrencyFormat($value['amount'],$value['currency'])
                                                        ));
                                                    }else{
                                                        echo $item['item_name']." x ".$item['qty'].'<br>';
                                                    }

                                                }

                                            ?>

                                        </td>
                                        <td><?=$value['created_at']?></td>
                                        <td align="left">
                                        <?php

                                            if($value['method']=="cod"){
                                                echo _lang("Payment with cash") ;
                                            }

                                            ?>
                                        </td>
                                        <td align="center">
                                     <?php if($value['method']!="cod"): ?>
                                                <a href="<?=site_url("payment/make_payment?id=".$value['id'])?>">
                                                    <button class="btn btn-primary"><i class="mdi mdi-paypal"></i>&nbsp;&nbsp;<?=Translate::sprint("Pay online")?></button>
                                                </a>
                                        <?php else: ?>
                                                <span class="badge bg-orange"><?=_lang("Pending")?></span>
                                        <?php endif; ?>
                                        </td>
                                    </tr>

                           <?php endforeach; ?>

                        <?php } ?>


                        <?php  if(count($paid_invoices)>0){ ?>

                                <tr>
                                    <td colspan="7" align="left">
                                        <strong><?=Translate::sprint("Paid invoices")?></strong>
                                    </td>
                                </tr>

                            <?php foreach ($paid_invoices as $value): ?>

                                    <tr>
                                        <td><strong><?=$value['id']?></strong></td>
                                    <?php

                                        if($value['tax_id']>0){
                                            $tax = $this->mTaxModel->getTax($value['tax_id']);
                                            if($tax!=NULL){
                                                $tax_value = ( ($tax['value']/100)*$value['amount'] );
                                            }
                                        }else if($value['tax_id']==-2) {
                                            $litTaxes = json_decode($value['taxes'], JSON_OBJECT_AS_ARRAY);
                                            $litTaxes = is_array($litTaxes) ? $litTaxes : array($litTaxes);
                                            $newAmount = $value['amount'];
                                            foreach ($litTaxes as $val) {
                                                $percent = 0;
                                                $mTax = $this->mTaxModel->getTax($val);
                                            }

                                        }

                                        ?>
                                        <td><?=Currency::parseCurrencyFormat($value['amount'],$value['currency'])?></td>
                                        <td>
                                        <?php
                                            if(isset($tax))
                                                echo $tax['name'].' '.$tax['value'].'%';
                                            else if(isset($litTaxes))
                                                foreach ($litTaxes as $val) {
                                                    $mTax = $this->mTaxModel->getTax($val);
                                                    if ($mTax != NULL) {
                                                        echo $mTax['name'].' '.$mTax['value'].'%<br>';
                                                    }
                                                }
                                            else
                                                echo '--'
                                            ?>
                                        </td>
                                        <td>
                                        <?php

                                            echo "<strong>"._lang($value['module'])."</strong>: <br/>";
                                            $items = json_decode($value['items'],JSON_OBJECT_AS_ARRAY);
                                            foreach ($items as $item){
                                                if($value['module']=="wallet"){
                                                    echo Translate::sprintf($item['item_name'],array(
                                                        Currency::parseCurrencyFormat($value['amount'],$value['currency'])
                                                    ));
                                                }else{
                                                    echo $item['item_name']." x ".$item['qty'].'<br>';
                                                }

                                            }

                                            ?>

                                        </td>
                                        <td><?=$value['updated_at']?></td>
                                        <td align="left">
                                            <?=$value['method']?> / <?=$value['transaction_id']?>
                                        </td>
                                        <td align="right">
                                            <a target="_blank" href="<?=admin_url("payment/printBill?id=".$value['id'])?>" data-toggle="tooltip"  data-original-title="<?=Translate::sprint("Print")?>">
                                                <i class="mdi mdi-printer font-size16px"></i>&nbsp;&nbsp;
                                            </a>
                                        </td>
                                    </tr>

                            <?php endforeach; ?>

                        <?php } ?>


                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-12 pull-right">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php

                                    echo $paid_pagination->links(array(
                                        "page"    =>RequestInput::get("page"),
                                        "status"    =>RequestInput::get("status"),
                                    ),admin_url("payment/billing"));

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
                                    <h3 class="text-red"><?=Translate::sprint("Are you sure?")?></h3>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel","Cancel")?></button>
                            <button type="button" id="_delete"  class="btn btn-flat btn-primary"><?=Translate::sprint("OK")?></button>
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

<div class="modal fade" id="add-balance-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b><?=_lang("Top-up")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <select id="select_amount" class="select2">
                    <?php
                            $amounts = $this->mWalletModel->getTopUp();
                    
                        ?>
                    <?php foreach ($amounts as $a): ?>
                        <option value="<?=$a?>"><?=Currency::parseCurrencyFormat($a,DEFAULT_CURRENCY)?></option>
                    <?php endforeach; ?>
                        <option value="-1"><?=_lang("Custom amount")?></option>
                    </select>
                </div>

                <div class="form-group custom_amount hidden">
                    <input type="number" id="amount" class="form-control" value="<?=$amounts[0]?>" placeholder="<?=_lang("Enter amount")?>" />
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel","Cancel")?></button>
                <button type="button" id="add_balance" class="btn btn-flat btn-primary"><?=Translate::sprint("Top-up")?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php

$script = $this->load->view('backend/html/scripts/billing-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
