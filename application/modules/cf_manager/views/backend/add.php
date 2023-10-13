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
                            <b><?= Translate::sprint("Add new custom fields") ?></b> <br>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-8">


                                <div class="callout callout-info">
                                    <p>
                                        <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= Translate::sprint("What's the purpose of using checkout fields in my app ?") ?>
                                        <?= Translate::sprint("this will allow you to request more information from
                                 your customer during the validation of the order, eg email, date of availability,
                                  number of people ... etc") ?></p>

                                </div>


                                <br>


                                <table class="table cf-list">
                                    <tr>
                                        <td></td>
                                        <th><?= _lang("Field's type") ?></th>
                                        <th><?= _lang("Label") ?></th>
                                        <th><?= _lang("Default") ?></th>
                                        <th><?= _lang("Is required") ?></th>
                                        <th></th>
                                    </tr>
                                <?php $d = substr(md5(time()), 0, 10); ?>
                                    <tbody class="dd">
                                    <tr class="first_line line line_<?= $d ?>" data-id="<?= $d ?>">
                                        <td><span class="cursor-pointer" style="font-size: 22px"><i
                                                        class="mdi mdi-menu text-gray"></i></span></td>
                                        <td valign="center">
                                            <div class="form-group no-padding">
                                                <select class="form-control" id="field_type">
                                                <?php foreach ($map as $key => $field): ?>
                                                    <?php foreach ($field['types'] as $type): ?>
                                                            <option value="<?= $key . "." . $type ?>"><?= _lang($type) ?></option>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td valign="center">
                                            <div class="form-group  no-padding">
                                                <input type="text" class="form-control" id="field_label"
                                                       placeholder="<?= _lang("Enter label...") ?>"/>
                                            </div>
                                        </td>
                                        <td valign="center">
                                            <div class="form-group  no-padding">
                                                <input type="text" class="form-control" id="field_default"
                                                       placeholder="<?= _lang("Enter default...") ?>"/>
                                            </div>
                                        </td>
                                        <td valign="center">
                                            <div class="form-group no-padding">
                                                <label><input type="radio" class="required is_required"
                                                              name="is_required_<?= $d ?>"
                                                              value="1"/>&nbsp;&nbsp;<?= _lang("Yes") ?></label>&nbsp;&nbsp;
                                                <label><input type="radio" class="required" name="is_required_<?= $d ?>"
                                                              value="0"/>&nbsp;&nbsp;<?= _lang("No") ?></label>
                                            </div>
                                        </td>
                                        <td align="right"><a class="remove cursor-pointer" style="font-size: 16px"><i
                                                        class="mdi mdi-close text-red"></i></a></td>
                                    </tr>
                                    </tbody>

                                    <tr>
                                        <td colspan="5">
                                            <button class="btn btn-primary btn-flat" id="add_new_line"><i
                                                        class="mdi mdi-playlist-plus"></i>&nbsp;&nbsp;<?= _lang("Add new Line") ?>
                                            </button>
                                        </td>
                                    </tr>
                                </table>

                                <input id="form_id" type="hidden" value="1">


                                <div class="form-group col-md-6">
                                    <label><?= _lang("Label") ?></label>
                                    <input class="form-control" type="text" id="cf_label"
                                           placeholder="<?= _lang("Enter label...") ?>">
                                </div>

                            </div>

                            <div class="col-sm-6">

                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="save-cf" class="btn btn-primary btn-flat pull-right"><i
                                    class="mdi mdi-content-save-outline"></i>&nbsp;&nbsp;<?= _lang("Save") ?>
                        </button>
                    </div>
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php


$script = $this->load->view('cf_manager/backend/scripts/add-script', NULL, TRUE);
AdminTemplateManager::addScript($script);

?>
