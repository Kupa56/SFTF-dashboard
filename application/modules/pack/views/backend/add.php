
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
                                    <b><?=Translate::sprint("Add new Pack")?></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= Translate::sprint("Name") ?> </label>
                                    <input type="text" class="form-control" id="name" placeholder="<?=Translate::sprint("Enter")?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= Translate::sprint("Description") ?> </label>
                                    <textarea class="form-control" placeholder="Write a small description regarding your pack...." id="description" name="description" required></textarea>
                                </div>
                            </div>
                        </div>


                        <strong class="uppercase title"><?=Translate::sprint("Subscription config")?></strong>
                        <sup><?=Translate::sprint("Customize user subscription config")?></sup>
                        <div class="row">

                        <?php foreach ($user_subscribe_fields as $field): ?>

                            <?php
                                if($field['_display']==0)
                                    continue;
                                ?>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?=Translate::sprint($field['field_label'])?>
                                        <?php if($field['field_sub_label']!=""): ?>
                                                &nbsp;<span class="font-size10px text-grey2"><?=Translate::sprint($field['field_sub_label'])?></span>
                                        <?php endif; ?>
                                        </label>

                                    <?php if($field['field_comment']): ?>
                                            <br><sup><i class="mdi mdi-information-outline"></i> <?=Translate::sprint($field['field_comment'])?></sup>
                                    <?php endif; ?>

                                    <?php if($field['field_type']==UserSettingSubscribeTypes::INT
                                            OR $field['field_type']==UserSettingSubscribeTypes::DOUBLE):?>

                                            <input type="number" min="-1" max="100" class="form-control"
                                                   placeholder="<?= Translate::sprint($field['field_placeholder']) ?>" name="<?=$field['config_key']?>"
                                                   id="pack_<?=$field['config_key']?>" value="">

                                    <?php elseif($field['field_type']==UserSettingSubscribeTypes::BOOLEAN): ?>

                                            <select class="form-control select2" id="pack_<?=$field['config_key']?>">
                                            <?php if($field['field_placeholder']!=""): ?>
                                                    <option value="0"><?= Translate::sprint($field['field_placeholder']) ?></option>
                                            <?php endif; ?>
                                                <option value="1"><?=Translate::sprint('Enabled')?></option>
                                                <option value="0"><?=Translate::sprint('Disabled')?></option>
                                            </select>

                                    <?php elseif($field['field_type']==UserSettingSubscribeTypes::VARCHAR): ?>

                                            <input type="text" min="-1" max="100" class="form-control"
                                                   placeholder="<?= Translate::sprint("Enter") ?>" name="<?=$field['config_key']?>"
                                                   id="pack_<?=$field['config_key']?>" value="">

                                    <?php endif; ?>

                                    </div>
                                </div>
                        <?php endforeach; ?>



                            <div class="col-sm-12 pricing">
                                <strong class="uppercase title"><?=Translate::sprint("Pricing & Duration")?></strong>


                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Price monthly") ?> (<?=PAYMENT_CURRENCY?>/<?=Translate::sprint("Month")?>)</label>
                                            <input type="number" class="form-control" id="price_per_month" placeholder="<?=Translate::sprint("Enter")?>">
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Price yearly") ?> (<?=PAYMENT_CURRENCY?>/<?=Translate::sprint("Year")?>)</label>
                                            <input type="number" class="form-control" id="price_per_year" placeholder="<?=Translate::sprint("Enter")?>">
                                            <sub class="text-red discounted"></sub>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label id="is_free">
                                            <input type="checkbox" id="is_free_check" class="minimal" checked>
                                            &nbsp;&nbsp;<strong><?=Translate::sprint("Free Pack")?></strong>
                                            &nbsp;&nbsp;
                                        </label>
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Trial Period (days)") ?></label>
                                            <input type="number" class="form-control" id="trial_period" placeholder="<?=Translate::sprint("Enter")?>">
                                            <sub class="text-red discounted"></sub>
                                        </div>
                                    </div>

                                </div>

                                <br><br>


                            </div>


                            <div class="col-sm-12">
                                <strong class="uppercase title"><?=Translate::sprint("Other options")?></strong>
                            </div>
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label><?=Translate::sprint('Link a custom group access with this pack')?></label><br>
                                    <sup><?=Translate::sprint('When the user choose a pack, will be linked with selected group access automatically')?></sup>
                                    <select class="form-control select2" id="group_access">
                                        <option value="0">-- <?=Translate::sprint('Select Group Access')?></option>
                                    <?php foreach ($group_accesses as $grp): ?>
                                            <option value="<?=$grp['id']?>"><?=$grp['name']?></option>
                                    <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?= Translate::sprint("Order") ?></label><br>
                                    <input type="number" class="form-control" id="order" value="<?=$this->mPack->getLastOder()?>" placeholder="<?=Translate::sprint("Enter")?>">
                                </div>


                            </div>


                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label id="recommended">
                                        <input type="checkbox" id="recommended_check" class="minimal" checked>
                                        &nbsp;&nbsp;<strong><?=Translate::sprint("Recommended")?></strong>
                                        &nbsp;&nbsp;
                                    </label>
                                </div>



                                <div class="form-group">
                                    <label id="_display">
                                        <input type="checkbox" id="display_check" class="minimal" checked>
                                        &nbsp;&nbsp;<strong><?=Translate::sprint("Visible to the clients")?></strong>
                                        &nbsp;&nbsp;
                                    </label>
                                </div>

                            </div>

                        </div>



                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary" id="btnCreate" > <span class="glyphicon glyphicon-check"></span>
                            <?=Translate::sprint("Add","")?> </button>
                    </div>
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
<?php

$data['user_subscribe_fields'] = $user_subscribe_fields;
$script = $this->load->view('pack/backend/scripts/add-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>



