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

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Modules Manager") ?></b>
                        </div>

                        <a href="<?=admin_url("modules_manager/add")?>" class="btn btn-flat bg-primary pull-right">
                            <i class="mdi mdi-plus"></i>&nbsp;&nbsp;<?=Translate::sprint('Add New Module')?>
                        </a>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td width="3%"><label><input id="check_all" type="checkbox"></label></td>
                                        <td width="77%" colspan="2">
                                            <div class="form-group no-margin" style="width: 150px">
                                                <select class="select2 hidden" id="action">
                                                    <option value="0"><?=Translate::sprint("Action")?></option>
                                                    <option value="1"><?=Translate::sprint("Enable all")?></option>
                                                    <option value="2"><?=Translate::sprint("Disable all")?></option>
                                                    <option value="3"><?=Translate::sprint("Install all")?></option>
                                                    <option value="4"><?=Translate::sprint("Uninstall all")?></option>
                                                    <option value="5"><?=Translate::sprint("Upgrade all")?></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td width="20%"></td>
                                    </tr>
                                <?php

                                    foreach ($modules as $key => $module){

                                        if(isset($module['detail']['displayed']) && $module['detail']['displayed']==0)
                                            continue;

                                        $data['module'] = $module;
                                        $this->load->view('modules_manager/backend/html/item-list',$data);
                                    }

                                    ?>
                                </table>
                            <div>
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
<?php


$script = $this->load->view('modules_manager/backend/scripts/manage-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
