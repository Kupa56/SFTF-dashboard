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
                                    <b><?=Translate::sprint("Transactions")?></b>
                                </div>
                                <div class="pull-right col-md-3">
                                    <form method="get" action="<?=admin_url('payment/transactions_logs')?>">
                                        <div class="input input-group-sm">
                                            <input class="form-control" size="30" name="invoice_id" type="text" placeholder="<?=Translate::sprint("Search by invoice ID...")?>" value="">
                                        </div>
                                    </form>
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
                                <th width="10%"><?=Translate::sprint("Invoice")?></th>
                                <th width="20%"><?=Translate::sprint("Client")?></th>
                                <th width="10%"><?=Translate::sprint("Status")?></th>
                                <th width="15%"><?=Translate::sprint("Date","")?></th>
                                <th width="20%"><?=Translate::sprint("TransactionID","")?></th>
                                <th width="15%"><?=Translate::sprint("Method","")?></th>
                                <th width="10%"></th>
                            </tr>
                            </thead>
                            <tbody>
                        <?php  if(count($transactions)>0){ ?>

                            <?php foreach ($transactions as $value): ?>

                                    <tr>
                                        <td>
                                        <?php
                                                if($value['invoice_id']>0)
                                                    echo "<a target='_blank' href='".admin_url("payment/transactions?invoice_id=".$value['invoice_id'])."'>#".$value['invoice_id']."&nbsp;&nbsp;<i class=\"mdi mdi-open-in-new\"></i></a>";
                                            ?>
                                        </td>
                                        <td>
                                        <?php if($value['user_id']>0): ?>
                                                <a target='_blank' href="<?=admin_url('user/profile?id='.$value['user_id'])?>"><?=$this->mUserModel->getUserNameById($value['user_id'])?>&nbsp;&nbsp;<i class="mdi mdi-open-in-new"></i></a>
                                        <?php else: ?>

                                        <?php endif; ?>
                                        </td>
                                        <td>
                                        <?php
                                                if($value['status'] == "invoice_updated"){
                                                   echo ' <span class="badge bg-green">'.$value['status'].'</span>';
                                                }else{
                                                    echo '<span class="badge bg-yellow">'.$value['status'].'</span>';
                                                }
                                            ?>
                                        </td>
                                        <td><?=$value['created_at']?></td>
                                        <td>
                                        <?php
                                                $transaction_id = explode(":",$value['transaction_id']);
                                                echo $transaction_id[1];
                                            ?>
                                        </td>
                                        <td>
                                           xxx
                                        </td>
                                        <td align="right">

                                        <?php if($value['refunded'] == 1): ?>
                                                <span><i class="mdi mdi-check text-green"></i>&nbsp;&nbsp;<?=Translate::sprint("Refund Requested")?></span>
                                        <?php elseif($link = $this->mPaymentModel->getRefundData($value['links']) and $link != NULL): ?>
                                                <u><i class="hidden fa fa-refresh fa-spin" id="refund_proccessing"></i>
                                                <u><a id="refund" data-id="<?=$value['id']?>" href="<?=$link?>"><?=Translate::sprint("Refund")?></a></u>
                                        <?php endif; ?>


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
                                        ),admin_url("payment/transactions"));

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

$script = $this->load->view('backend/html/scripts/transactions-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
