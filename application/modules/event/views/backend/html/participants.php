<?php

$participants = $data[Tags::RESULT];

$pagination = $data['pagination'];

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

                <div class="box  box-solid">
                    <div class="box-header" style="min-height: 54px;">
                        <div class="box-title" style="width : 100%;">
                            <div class="title-header ">
                                <b><?= Translate::sprint("Participants") ?></b>
                                <div class="pull-right">
                                    <button class="btn btn-flat bg-blue push_email hidden"><i class="mdi mdi-email-variant"></i>&nbsp;&nbsp;<?=Translate::sprintf("Remind <span id=\"estimated_users\">%s</span> user(s) via email",array(0))?></button>
                                    <button class="btn btn-flat bg-orange push_campaign hidden"><i class="mdi mdi-bullseye"></i>&nbsp;&nbsp;<?=Translate::sprintf("Push Campaign to <span id=\"estimated_guests\">%s</span> user(s)",array(0))?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body  table-responsive">
                        <div class="table-responsive participants">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <!--<th>ID</th>-->
                                    <th><label><input id="check_all" type="checkbox"></label></th>
                                    <th><strong>#<?= Translate::sprint("ID") ?></strong></th>
                                    <th><?= Translate::sprint("User") ?></th>
                                    <th><?= Translate::sprint("Event") ?></th>
                                    <th><?= Translate::sprint("Date") ?></th>
                                    <th><?= Translate::sprint("Status") ?></th>
                                    <th>
                                    <?php
                                        $limit = intval(RequestInput::get('limit'));
                                        ?>
                                        <select class="select2" id="limit">
                                            <option value="100" <?=$limit==100?"selected":""?>>100</option>
                                            <option value="200" <?=$limit==200?"selected":""?>>200</option>
                                            <option value="300" <?=$limit==300?"selected":""?>>300</option>
                                            <option value="400" <?=$limit==400?"selected":""?>>400</option>
                                            <option value="500" <?=$limit==500?"selected":""?>>500</option>
                                            <option value="600" <?=$limit==600?"selected":""?>>600</option>
                                        </select>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                            <?php if (!empty($participants)) { ?>

                                <?php foreach ($participants AS $participant) { ?>

                                        <tr>
                                            <td><label><input class="participant_check" data-agreement="<?=$participant['notification_agreement']?>" data-guest-id="<?=$participant['guest_id']?>" data-user-id="<?=$participant['user_id']?>" id="participant_check" type="checkbox"></label></td>
                                            <td><strong>#<?=$participant['id']?></strong></td>
                                            <td><b><?=$participant['user_name']?><?php if(PARTICIPANTS_FIELDS_EMAIL_SHOWN==TRUE):?></b><br><?=$participant['user_email']?><?php endif; ?></td>
                                            <td><?=$participant['event_name']?></td>
                                            <td><?=date("M d, Y h:i A",strtotime($participant['created_at']))?></td>
                                            <td colspan="2">
                                            <?php

                                                    if($participant['status'] == 0){
                                                        echo "<span class='badge bg-yellow'>"._lang("Not reminded")."</span>";
                                                    }else{
                                                        echo "<span class='badge bg-blue'>"._lang("Reminded")."</span>";
                                                    }

                                                ?>
                                            </td>
                                        </tr>


                                <?php } ?>
                            <?php } else { ?>
                                    <tr>
                                        <td colspan="6">
                                            <div
                                                style="text-align: center"><?= Translate::sprint("No user participated", "") ?></div>
                                        </td>
                                    </tr>

                            <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">

                                </div>

                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php

                                    echo $pagination->links(array(
                                        "event_id" => intval(RequestInput::get("event_id")),
                                        "limit" => intval(RequestInput::get("limit")),
                                    ), current_url());

                                    ?>
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
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php

$data['event_id'] = intval(RequestInput::get('event_id'));

    $script = $this->load->view('event/backend/html/scripts/participants-script', $data, TRUE);
    AdminTemplateManager::addScript($script);

?>
