
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

            <div class="col-sm-6">
                <form id="form" class="create_campaign">
                    <div class="box box-solid">
                        <div class="box-header">

                            <div class="box-title">
                                <b><i class="mdi mdi-bullseye"></i>&nbsp;&nbsp;
                                    <?=Translate::sprint("Create new campaign","")?></b>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="callout callout-info">
                                <p> <?=Translate::sprint("Alert_compaign","")?></p>
                            </div>


                            <div class="form-group select-modules">
                                <label><?=Translate::sprint("Type campaign","")?> <sup>*</sup></label>
                                <select class="form-control select2 selectCType" style="width: 100%;">
                                    <option selected="selected" value="0">
                                        <?=Translate::sprint("Select campaign type","")?></option>
                                <?php

                                    foreach (CampaignManager::load() as $key => $module){
                                        echo " <option value=\"$key\">".Translate::sprint(ucfirst($key))."</option>";
                                    }

                                    ?>

                                </select>
                            </div>


                        <?php  foreach (CampaignManager::load() as $key => $module): ?>

                                <div class="form-group drop-box drop-box-<?=$key?> hidden">
                                    <label><?=Translate::sprint("Select","")?> <?=Translate::sprint($key)?></label>
                                    <select class="form-control select2 select-<?=$key?>" style="width: 100%;">
                                        <option selected="selected" value="0">
                                            <?=Translate::sprint("Select $key")?></option>
                                    </select>
                                </div>



                            <?php

                                if(isset($module['custom_parameters']) && isset($module['custom_parameters']['html'])){
                                    echo $module['custom_parameters']['html'];
                                    AdminTemplateManager::addScript($module['custom_parameters']['script']);
                                }


                                ?>

                        <?php endforeach; ?>


                            <div class="campaign_fields hidden">
                                <div class="form-group campaign_name">
                                    <label> <?=Translate::sprint("Title","")?></label>
                                    <input type="text" class="form-control" name="campaign_name" id="campaign_name" placeholder="Ex: campaign_for_black_friday">
                                </div>

                                <div class="form-group campaign_text">
                                    <label> <?=Translate::sprint("Text","")?></label>
                                    <input type="text" class="form-control" name="campaign_text" id="campaign_text" placeholder="Ex: campaign_for_black_friday">
                                </div>
                            </div>

                            <div class="form-group box-estimation hidden">
                                <label>
                                    <?=Translate::sprint("Targeting estimation")?></label>
                                <br>
                                <span> <?=Translate::sprintf("This campaign will be reached %s customers",array("<span class=\"target_value\">0</span>",RADUIS_TRAGET),"")?> </span><br/>
                            <?php if(_NOTIFICATION_AGREEMENT_USE):?>
                                <span class="text-blue"><i class="mdi mdi-information-outline"></i> <?=Translate::sprintf("You will target only users that interested by your business")?> </span>
                            <?php endif; ?>
                            </div>



                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">

                        <?php

                            $usr_id = $this->mUserBrowser->getData('id_user');
                            $nbr_campaign_monthly = UserSettingSubscribe::getUDBSetting($usr_id,KS_NBR_CAMPAIGN_MONTHLY);

                            ?>

                        <?php if($nbr_campaign_monthly>0 or $nbr_campaign_monthly==-1): ?>
                                <button type="button" class="btn  btn-primary" id="btnCreate" disabled> <span class="fa fa-paper-plane-o"></span>
                                    <?=Translate::sprint("Push","")?> </button>
                        <?php elseif($nbr_campaign_monthly==0): ?>
                                <button type="button" class="btn  btn-primary" id="btnCreate" disabled> <span class="fa fa-paper-plane-o"></span>
                                    <?=Translate::sprint("Push","")?> </button>
                                &nbsp;&nbsp;
                                <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?=Translate::sprint(Messages::EXCEEDED_MAX_NBR_CAMPAIGNS)?></span>
                        <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.box -->
                </form>
            </div>
            <div class="col-md-6">
                <form id="test">
                    <div class="box box-solid">
                        <div class="box-header">
                            <div class="box-title">
                                <b><i class="mdi mdi-bell-outline"></i>&nbsp;&nbsp;
                                    <?=Translate::sprint("Notification preview")?></b>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="previews-container">
                                <div class="android-preview device-preview">
                                    <div style="text-align: center; padding-top: 10px"><strong>Android</strong></div>
                                    <div class="preview-background android" style="background-image: url(<?=AdminTemplateManager::assets("campaign", "images/android.png")?>)">
                                        <div class="card-notification">
                                            <table style="width: 100%">
                                                <tr>
                                                    <td width="65px">
                                                        <img data-placeholder="<?=AdminTemplateManager::assets("campaign", "images/device_image_placeholder.png")?>" class="notification-image" src="<?=AdminTemplateManager::assets("campaign", "images/device_image_placeholder.png")?>"/>
                                                    </td>
                                                    <td>
                                                       <div class="notification-content" >
                                                           <strong data-placeholder="<?=_lang("Notification title")?>" class="text-black title"><?=_lang("Notification title")?></strong><br>
                                                           <span data-placeholder="<?=_lang("Notification text")?>"  class="text-grey2 text"><?=_lang("Notification text")?></span>
                                                       </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="ios-preview device-preview">
                                    <div style="text-align: center; padding-top: 10px"><strong>iOS</strong></div>
                                    <div class="preview-background ios" style="background-image: url(<?=AdminTemplateManager::assets("campaign", "images/iphone.png")?>)">
                                        <div class="card-notification">
                                            <table style="width: 100%">
                                                <tr>
                                                    <td>
                                                        <div class="notification-content" >
                                                            <strong data-placeholder="<?=_lang("Notification title")?>" class="text-black title"><?=_lang("Notification title")?></strong><br>
                                                            <span data-placeholder="<?=_lang("Notification text")?>"  class="text-grey2 text"><?=_lang("Notification text")?></span>
                                                        </div>
                                                    </td>
                                                    <td width="65px">
                                                        <img data-placeholder="<?=AdminTemplateManager::assets("campaign", "images/device_image_placeholder.png")?>" class="notification-image" src="<?=AdminTemplateManager::assets("campaign", "images/device_image_placeholder.png")?>"/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- /.box -->
                </form>
            <?php if(GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)): ?>
                    <form id="test" class="hidden">
                        <div class="box box-solid">
                            <div class="box-header">
                                <div class="box-title">
                                    <b><i class="mdi mdi-bullseye"></i>&nbsp;&nbsp;
                                        <?=Translate::sprint("Test campaigns","")?></b>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="callout callout-warning">
                                    <p><?=Translate::sprint("To test the campaign in debug mode, you must be sure that at least one shop, event or offer is created")?></p>
                                </div>
                                <div class="form-group">
                                    <label><?=Translate::sprint("Type campaign","")?> <sup>*</sup></label>


                                    <select class="form-control select2 selectTestCType" style="width: 100%;">
                                        <option selected="selected" value="0">
                                            <?=Translate::sprint("Select campaign type","")?></option>

                                    <?php

                                        foreach (json_decode(CAMPAIGN_TYPES,JSON_OBJECT_AS_ARRAY) as $value){
                                            echo " <option value=\"$value\">".Translate::sprint(ucfirst($value))."</option>";
                                        }

                                        ?>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label> <?=Translate::sprint("Guest IDs","")?></label>
                                    <input type="text" class="form-control" name="gids" id="gids" placeholder="Ex: 1,2,3...">
                                </div>



                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <button type="button" class="btn  btn-primary" id="btnTest" > <span class="fa fa-paper-plane-o"></span>
                                    <?=Translate::sprint("Push","")?> </button>
                            </div>

                        </div>
                        <!-- /.box -->
                    </form>
            <?php endif; ?>
            </div>

            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php

$script = $this->load->view('campaign/backend/html/scripts/add-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>


