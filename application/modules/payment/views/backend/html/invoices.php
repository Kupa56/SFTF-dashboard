<?php


$transactions = $result[Tags::RESULT];
$pagination = $result[Tags::PAGINATION];


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

        <div class="row transactions">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"  style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?=Translate::sprint("Invoices")?></b>
                                </div>
                                <div class="pull-right col-md-3">
                                    <select id="filter" class="select2">

                                    <?php

                                            $status = RequestInput::get('status');
                                            if($status=="")
                                                $status = 2;
                                            else
                                                $status = intval($status);

                                        ?>

                                        <option value="2" <?php if($status==2) echo 'selected' ?>><?=Translate::sprint("All")?></option>
                                        <option value="0" <?php if($status==0) echo 'selected' ?>><?=Translate::sprint("Unpaid")?></option>
                                        <option value="1" <?php if($status==1) echo 'selected' ?>><?=Translate::sprint("Paid")?></option>
                                        <option value="-1" <?php if($status==-1) echo 'selected' ?>><?=Translate::sprint("Canceled")?></option>
                                    </select>
                                </div>
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
                                <th width="10%"><?=Translate::sprint("Amount")?></th>
                                <th width="10%"><?=Translate::sprint("Tax")?></th>
                                <th width="10%"><?=Translate::sprint("Client")?></th>
                                <th width="20%"><?=Translate::sprint("Items","")?></th>
                                <th width="20%"><?=Translate::sprint("Date","")?></th>
                                <th width="10%"><?=Translate::sprint("Status","")?></th>
                                <th width="15%"><?=Translate::sprint("Method","")?></th>
                                <th width="10%"></th>
                            </tr>
                            </thead>
                            <tbody>
                        <?php  if(count($transactions)>0){ ?>

                            <?php foreach ($transactions as $value): ?>

                                    <tr>
                                        <td><strong><?=$value['id']?></strong></td>
                                    <?php

                                            if($value['tax_id']>0){
                                                $tax = $this->mTaxModel->getTax($value['tax_id']);
                                                if($tax!=NULL){
                                                    $tax_value = ( ($tax['value']/100)*$value['amount'] );
                                                    //$value['amount'] = $value['amount'];
                                                }
                                            }


                                        ?>
                                        <td><?=Currency::parseCurrencyFormat($value['amount'],$value['currency'])?></td>
                                        <td>
                                        <?php
                                            if(isset($tax))
                                                echo $tax['name'].' '.$tax['value'].'%';
                                            else
                                                echo '0%'
                                            ?>
                                        </td>
                                        <td><a href="<?=admin_url('user/profile?id='.$value['user_id'])?>"><?=$this->mUserModel->getUserNameById($value['user_id'])?></a></td>
                                        <td>
                                        <?php

                                                $items = json_decode($value['items'],JSON_OBJECT_AS_ARRAY);
                                                foreach ($items as $item){
                                                    echo $item['item_name']." x ".$item['qty'].'<br>';
                                                }

                                                echo ' ('.$value['module'].')';

                                            ?>

                                        </td>
                                        <td><?=$value['created_at']?></td>
                                        <td>

                                        <?php

                                                if($value['status']==1)
                                                    echo "<span class='badge bg-green'>".Translate::sprint('Paid')."</span>";
                                                else if($value['status']==0)
                                                    echo "<span class='badge bg-yellow'>".Translate::sprint('Unpaid')."</span>";
                                                else if($value['status']==-1)
                                                    echo "<span class='badge bg-red'>".Translate::sprint('Canceled')."</span>";

                                            ?>

                                        </td>
                                        <td>
                                        <?php

                                            if($value['method']=="paypal"){
                                                echo _lang("PayPal");
                                            }elseif($value['method']=="stripe"){
                                                echo _lang("Stripe");
                                            }elseif($value['method']=="cod"){
                                                echo _lang("Cash on delivery");
                                            }else{
                                                echo $value['method'];
                                            }


                                            ?>
                                        </td>
                                        <td align="right">
                                            <a href="<?=admin_url("payment/invoice?id=".$value['id'].'&user_id='.$value['user_id'])?>" data-toggle="tooltip"  data-original-title="<?=Translate::sprint("Print")?>">
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

                                        echo $pagination->links(array(
                                            "page"    =>RequestInput::get("page"),
                                            "status"    =>RequestInput::get("status"),
                                        ),admin_url("payment/invoices"));

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
<?php

$script = $this->load->view('backend/html/scripts/invoices-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
