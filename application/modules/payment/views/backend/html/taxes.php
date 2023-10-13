<?php

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$languages = Translate::getLangsCodes();


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">
        <div class="row">
            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Tax Config"); ?> </b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form id="form" role="form">

                                <div class="form-group">
                                    <label><?= Translate::sprint("Default Tax") ?></label>
                                    <select id="DEFAULT_TAX" name="DEFAULT_TAX"
                                            class="form-control select2 DEFAULT_TAX">
                                    <?php


                                            echo '<option value="0">'.Translate::sprint('Select').'</option>';

                                            foreach ($taxes as $key => $c){
                                                if(DEFAULT_TAX==$c['id']){
                                                    echo '<option value="'.$c['id'].'" selected>'.$c['name'].', '.$c['value'].'%</option>';
                                                }else{
                                                    echo '<option value="'.$c['id'].'">'.$c['name'].', '.$c['value'].'%</option>';
                                                }
                                            }
                                            //echo '<option value="-2">'.Translate::sprint('Multiple Taxes').'</option>';
                                            echo '<option value="0">'.Translate::sprint('Disable').'</option>';


                                        ?>
                                    </select>
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary" id="btnSaveDefaultTax"><span
                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>




                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Taxes"); ?> </b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <table class="table">
                                <tr>
                                    <th width="40%"><?=Translate::sprint('Tax')?></th>
                                    <th width="40%"><?=Translate::sprint('Percent')?></th>
                                    <th width="20%">
                                    </th>
                                </tr>
                            <?php if(count($taxes)>0): ?>
                            <?php foreach ($taxes as $tax): ?>
                                <tr>
                                    <td width="40%"><?=$tax['name']?></td>
                                    <td width="40%"><?=$tax['value']?>%</td>
                                    <td width="20%" align="right">
                                    <?php if($tax['id']==DEFAULT_TAX): ?>
                                            <a class="text-gray"><i class="mdi mdi-delete"></i>&nbsp;&nbsp;</a>
                                    <?php else: ?>
                                            <a href="<?=admin_url('payment/deleteTax?id='.$tax['id'])?>"><i class="mdi mdi-delete"></i>&nbsp;&nbsp;</a>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                    <tr>
                                        <td colspan="3"><?=Translate::sprint("No Taxes")?></td>
                                    </tr>
                            <?php endif; ?>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Add New Tax"); ?> </b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body taxes">
                        <div class="col-sm-12">
                            <form id="form" role="form">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Name") ?> </label><sub> <?=Translate::sprint("VAT, TVA ...")?></sub>
                                                <input type="text" class="form-control" id="tax_name" placeholder="<?=Translate::sprint("Enter")?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Value") ?> </label><sub> <?=Translate::sprint("10%, 20%")?></sub>
                                                <input type="text" class="form-control" id="tax_value" placeholder="<?=Translate::sprint("Enter")?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary" id="btnAddNewTax"><span
                                    class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Add Tax"); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Modal -->
<div class="modal fade" id="multi_taxes" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Choose all the taxes you want to integrate into the invoice </h4>
            </div>
            <div class="modal-body">

            <?php

                foreach ($taxes as $key => $c){ ?>
                    <div class="form-group">

                        <input type="checkbox" id="cb_multi_taxes"[] name="cb_multi_taxes[]" value="<?=$c['id']?>"  checked>
                        &nbsp;&nbsp;<strong> <?=$c['name'].', '.$c['value']?> </strong>
                        &nbsp;&nbsp;
                        </label>
                    </div>

           <?php }  ?>



            </div>
            <div class="modal-footer">
                <button type="button" id="submit_multi_taxes" class="btn btn-default pull-right btn-primary" data-dismiss="modal">save</button>
            </div>
        </div>

    </div>
</div>
<?php

$script = $this->load->view('backend/html/scripts/taxes-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>









