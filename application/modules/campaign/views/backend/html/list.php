<?php

$list = $campaigns[Tags::RESULT];
$pagination = $campaigns["pagination"];

$action = RequestInput::get('action');

switch ($action){
    case "all_campaigns":
        $title = "All Campaigns";
        break;
    case "my_campaigns":
        $title = "My Campaigns";
        break;
    case "pushed_campaigns":
        $title = "Pushed campaigns";
        break;
    case "completed_campaigns":
        $title = "Completed campaigns";
        break;
    case "pending_campaigns":
        $title = "Not approved campaigns";
        break;
    default:
        $title = "Campaigns";
        break;

}

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

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><i class="mdi mdi-history"></i>&nbsp;&nbsp;
                                <?= Translate::sprint($title) ?></b>
                            &nbsp; &nbsp; &nbsp;

                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">


                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <!--<th>ID</th>-->
                                <th><?= Translate::sprint("Name", "") ?></th>
                                <th><?= Translate::sprint("Module", "") ?></th>
                            <?php if (GroupAccess::isGranted('campaign', MANAGE_CAMPAIGNS)) : ?>
                                    <th><?= Translate::sprint("Owner", "") ?></th>
                            <?php endif; ?>
                                <th><?= Translate::sprint("Status", "") ?></th>
                                <th align="right">

                                <?php if(GroupAccess::isGranted('campaign', MANAGE_CAMPAIGNS)): ?>


                                    <?php
                                        $this->load->model("campaign/campaign_model");
                                        $nbrCampaigns = 0;
                                        $nbrCampaigns = $this->campaign_model->getPendingCampaigns();
                                        ?>

                                    <select class="select2 pull-right" id="campaigns_actions">
                                        <option value="all_campaigns" <?=$action=="all_campaigns"? "selected":""?>><?=Translate::sprint("All campaigns")?></option>
                                        <option value="my_campaigns" <?=$action=="my_campaigns"? "selected":""?>><?=Translate::sprint("My campaigns")?></option>
                                        <option value="pushed_campaigns" <?=$action=="pushed_campaigns"? "selected":""?>><?=Translate::sprint("Pushed campaigns")?></option>
                                        <option value="completed_campaigns" <?=$action=="completed_campaigns"? "selected":""?>><?=Translate::sprint("Completed campaigns")?></option>
                                        <option value="pending_campaigns" <?=$action=="pending_campaigns"? "selected":""?>><?=Translate::sprintf("Not approved campaigns (%s)",array($nbrCampaigns))?></option>
                                    </select>

                                <?php
                                        if($action != "" && $action!=="all_campaigns"){
                                            echo "<span class='pull-right badge bg-yellow' style='margin-right: 10px;'>".$title."&nbsp;&nbsp;<a style='color: #FFFFFF !important;' href='".admin_url("campaign/campaigns")."'>x</a></span>";
                                        }
                                    ?>

                                <?php endif; ?>

                                </th>
                            </tr>
                            </thead>
                            <tbody>

                        <?php if (count($list)) { ?>
                            <?php foreach ($list as $campaign) { ?>

                                    <tr>
                                        <td>
                                            <?= Text::output($campaign['name']) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-red"><?= Translate::sprint(ucfirst($campaign['module_name'])) ?></span>
                                        </td>

                                    <?php if (GroupAccess::isGranted('campaign', MANAGE_CAMPAIGNS)) : ?>
                                            <td>
                                                <a href="#"><u><?= ucfirst($this->mUserModel->getUserNameById($campaign['user_id'])) ?></u></a>
                                            </td>
                                    <?php endif; ?>

                                        <td>
                                        <?php


                                            if ($campaign['status'] == -1) {

                                                echo '<span class="badge bg-blue"><i class="mdi mdi-history"></i> &nbsp;' ?> <?= Translate::sprint("Not Approved") ?><?php echo '&nbsp;&nbsp;</span>';

                                            } else if ($campaign["estimation"] > 0 and $campaign["estimation"] == $campaign["received"] and $campaign["estimation"] > $campaign["seen"]) {

                                                echo '<span class="badge bg-blue"  data-toggle="tooltip" title="' . Translate::sprint("All notifications are pushed to closer users") . '"> <i class="mdi mdi-history"></i> &nbsp;' ?> <?= Translate::sprint("Pushed", "") ?><?php echo '&nbsp;&nbsp;</span><br>';

                                            } else if ($campaign["estimation"] > 0 and $campaign["estimation"] == $campaign["received"] and $campaign["estimation"] == $campaign["seen"]) {

                                                echo '<span class="badge bg-green"  data-toggle="tooltip" title="' . Translate::sprint("All notifications are seen by closer users") . '"> <i class="mdi mdi-history"></i> &nbsp;' ?> <?= Translate::sprint("Completed", "") ?><?php echo '&nbsp;&nbsp;</span>';

                                            } else if ($campaign["estimation"] > 0 and $campaign["received"] == 0 and $campaign["seen"] == 0) {

                                                echo '<span class="badge bg-yellow"  data-toggle="tooltip" title="' . Translate::sprint("The campaign is pushed to users") . '"> <i class="mdi mdi-history"></i> &nbsp;' ?> <?= Translate::sprint("Pending", "") ?><?php echo '&nbsp;&nbsp;</span>';

                                            }


                                            if ($campaign['received'] > 0) {
                                                if ($campaign['received'] > 0 && $campaign['received'] < $campaign['estimation']) {
                                                    echo '<span data-toggle="tooltip" title="' . $campaign['received'] . "/" . $campaign['estimation'] . " " . Translate::sprint("are received") . '" class="badge bg-light-blue"> <i class="mdi mdi-inbox-arrow-down"></i>&nbsp;&nbsp;' . $campaign['received'] . "/" . $campaign['estimation'] . '</span><br>';
                                                }
                                            }

                                            if ($campaign['seen'] > 0) {
                                                if ($campaign['seen'] > 0 && $campaign['seen'] < $campaign['estimation']) {
                                                    echo '<span data-toggle="tooltip" title="' . $campaign['seen'] . "/" . $campaign['estimation'] . " " . Translate::sprint("are seen") . '" class="badge bg-light-blue"> <i class="mdi mdi-eye"></i>&nbsp;&nbsp;' . $campaign['seen'] . "/" . $campaign['estimation'] . '</span><br>';
                                                }
                                            }


                                            ?>

                                        </td>


                                        <td align="right">

                                        <?php if(SessionManager::getData('manager') == 1 && ConfigManager::getValue('PUSH_CAMPAIGNS_WITH_CRON')):?>
                                                <a href="<?=admin_url("campaign/logs?id=".$campaign['id'])?>"><i class="mdi mdi-view-list"></i><?=_lang("Logs")?></a>&nbsp;&nbsp;|
                                        <?php endif; ?>



                                            <a href="<?=admin_url("campaign/report?id=".$campaign['id'])?>"><i class="mdi mdi-chart-line"></i>&nbsp;<?=_lang("Report")?></a>&nbsp;&nbsp;&nbsp;

                                        <?php if (GroupAccess::isGranted('campaign', MANAGE_CAMPAIGNS) and $campaign['status'] == -1) : ?>
                                                <a class="text-blue"
                                                   href="<?= admin_url("campaign/campaigns?push=" . $campaign['id']) ?>"
                                                   data-toggle="tooltip" title="<?= Translate::sprint("Push", "") ?>">
                                                    <i class=" fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;
                                                </a>
                                                <a href="#" id="delete-<?= $campaign['id'] ?>" data-toggle="tooltip"
                                                   title="<?= Translate::sprint("Delete", "") ?>">
                                                    <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;
                                                </a>
                                        <?php else: ?>
                                            <?php if ($this->mUserBrowser->getData("id_user") == $campaign['user_id']) : ?>
                                                    <a href="<?= site_url('ajax/campaign/archiveCampaign?id=' . $campaign['id']) ?>"
                                                       class="linkAccess" onclick="return false;" data-toggle="tooltip"
                                                       title="<?= Translate::sprint("Archive", "") ?>">
                                                        <span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;
                                                    </a>
                                                    <a href="<?= site_url('ajax/campaign/duplicateCampaign?id=' . $campaign['id']) ?>"
                                                       title="<?= Translate::sprint("Duplicate", "") ?> " class="hidden linkAccess"
                                                       onclick="return false;" data-toggle="tooltip"
                                                       title="<?= Translate::sprint("Duplicate") ?>">
                                                        <span class="glyphicon glyphicon-duplicate"></span>&nbsp;&nbsp;
                                                    </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        </td>


                                    </tr>

                            <?php } ?>


                        <?php } else { ?>
                                <tr>
                                    <td colspan="5" align="center">
                                        <?= Translate::sprint("No Campaigns") ?>
                                    </td>
                                </tr>
                        <?php } ?>


                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-5">
                                <div calass="dataTables_info" id="example2_info" role="status" aria-live="polite">

                                </div>

                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php

                                    echo $pagination->links(array(
                                        "action" => RequestInput::get("action"),
                                    ), admin_url("campaign/campaigns"));

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

        </div>
    </section>
</div>




<?php

$script = $this->load->view('campaign/backend/html/scripts/campaigns-script',NULL,TRUE);
AdminTemplateManager::addScript($script);

?>
