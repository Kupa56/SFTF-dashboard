<?php

if($this->session->has_userdata("latitude")){
    $lat = $this->session->userdata("latitude");
}else{
    $lat = MAP_DEFAULT_LATITUDE;
}

if($this->session->has_userdata("longitude")){
    $lng = $this->session->userdata("longitude");
}else{
    $lng = MAP_DEFAULT_LONGITUDE;
}

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <form id="form" role="form">
                <div class="col-md-6">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b><?= Translate::sprint("Create Event", "") ?></b></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <!-- text input -->

                            <div class="form-group required">

                            <?php

                                $upload_plug = $this->uploader->plugin(array(
                                    "limit_key" => "aEvFiles",
                                    "token_key" => "SzYUjEsS-4555",
                                    "limit" => MAX_OFFER_IMAGES,
                                ));

                                echo $upload_plug['html'];
                                AdminTemplateManager::addScript($upload_plug['script']);

                                ?>

                            </div>

                            <div class="form-group">
                                <label><?= Translate::sprint("Store", "") ?></label>
                                <select class="form-control select2 selectStore" style="width: 100%;">
                                    <option selected="selected"
                                            value="0"><?= Translate::sprint("Select store", "") ?></option>
                                <?php

                                    if (isset($myStores[Tags::RESULT])) {
                                        foreach ($myStores[Tags::RESULT] as $st) {
                                            echo '<option adr="' . $st['address'] . '" 
                                    lat="' . $st['latitude'] . '" lng="' . $st['longitude'] . '" 
                                    value="' . $st['id_store'] . '">' . $st['name'] . '</option>';
                                        }
                                    }

                                    ?>
                                </select>
                            </div>


                            <div class="form-group">
                                <label><?= Translate::sprint("Event name", "") ?> <sup>*</sup> </label>
                                <input  data-field-translator="true"  type="text" class="form-control"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="name" id="name">

                            </div>


                            <!-- textarea -->
                            <div class="form-group">
                                <label><?= Translate::sprint("Description", "") ?> :</label>
                                <textarea data-field-translator="true" name="editable-textarea" id="editable-textarea" class="form-control" style="height: 300px"></textarea>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6"><label><?= Translate::sprint("Date Begin") ?> <sup>*</sup>
                                        </label> <input class="form-control" data-provide="datepicker"
                                                        value="<?= date("Y-m-d", time()) ?>" placeholder="YYYY-MM-DD"
                                                        type="text" name="date_b" id="date_b"/></div>
                                    <div class="col-md-6"><label><?= Translate::sprint("Date End") ?> <sup>*</sup>
                                        </label> <input class="form-control" data-provide="datepicker" type="text"
                                                        placeholder="YYYY-MM-DD" name="date_e" id="date_e"/></div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label><?= Translate::sprint("Phone Number", "") ?>  </label>
                                <input type="text" class="form-control"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="tel" id="tel">
                            </div>


                            <div class="form-group">
                                <label><?= Translate::sprint("WebSite", "") ?></label>
                                <br>
                                <sup><span><?=Translate::sprint("Enter a valid URL with http or https")?></sup>
                                <input type="text" class="form-control"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="web" id="web">
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>


                </div>
                <div class="col-md-6">

                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <b><?= Translate::sprint("Drag the marker to get the exact position", "") ?> :</b></h3>
                        </div>

                        <div class="box-body">

                        <?php

                            $map = LocationPickerManager::plug_pick_location(array(
                                'lat'=>$lat,
                                'lng'=>$lng,
                                'address'=>'',
                                'custom_address'=>'',
                                'city'=> "",
                                'country'=> "",
                            ),array(
                                'lat'=>TRUE,
                                'lng'=>TRUE,
                                'address'=>TRUE,
                                'custom_address'=>TRUE,
                            ));

                            echo $map['html'];
                            AdminTemplateManager::addScript($map['script']);
                            $data['location_fields_id'] = $map['fields_id'];

                            ?>

                        </div>


                        <div class="box-footer">

                        <?php


                            $usr_id = $this->mUserBrowser->getData('id_user');
                            $nbr_events_monthly = UserSettingSubscribe::getUDBSetting($usr_id,KS_NBR_EVENTS_MONTHLY);


                            ?>

                        <?php if ($nbr_events_monthly > 0 or $nbr_events_monthly == -1): ?>
                                <button type="button" class="btn  btn-primary" id="btnCreate"><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Create", "") ?> </button>
                                
                        <?php else: ?>
                                <button type="button" class="btn  btn-primary" id="btnCreate" disabled><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Create", "") ?> </button>
                                &nbsp;&nbsp;
                                <span class="text-red font-size12px"><i
                                            class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_EVENTS) ?></span>
                        <?php endif; ?>
                        </div>


                    </div>

                </div>
            </form>
    </section>

</div>
<?php

$data['lat'] = $lat;
$data['lng'] = $lng;

$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('event/backend/html/scripts/create-script', $data, TRUE);
AdminTemplateManager::addScript($script);

?>

