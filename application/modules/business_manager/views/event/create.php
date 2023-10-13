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


    <div id="app" class="framework7-root">
        <div id="create-business" class="page-content no-padding-top" data-item="">
            <!--<a href="#" class="link back close-button">
                <i class="mdi mdi-close"></i>
            </a>-->
            <div class="block">
                <div class="form-container">

                    <div class="list no-hairlines custom-form">

                        <div class="step-0-form">

                            <h1 class="create-title"><?=_lang("Create Event")?></h1>

                           <ul>
                               <li class="item-content item-input">
                                   <div class="item-inner">
                                       <div class="item-input-wrap">
                                           <input id="autocomplete-stores" type="text" placeholder="<?=_lang("Select Business")?> (*)" class="">
                                           <span class="input-clear-button"></span>
                                           <input type="hidden" id="store_id">
                                       </div>
                                   </div>
                               </li>
                           </ul>

                            <div class="row">
                                <div class="col-50">
                                    <a class="big-button button" id="cancel"> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-right":"mdi-arrow-left"?>"></i>&nbsp;<?= _lang("Cancel") ?></a>
                                </div>
                                <div class="col-50">
                                    <a class="big-button button button-fill link" id="go-step-1"><?= _lang("Continue") ?> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-left":"mdi-arrow-right"?>"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="step-1-form hidden">

                            <h1 class="create-title"><?=_lang("Business Location")?></h1>

                        <?php

                            $map = LocationPickerManager::plug_pick_location(array(
                                'lat'=>$lat,
                                'lng'=>$lng,
                                'address'=>'',
                                'custom_address'=>'',
                                'city'=> "",
                                'country'=> "",

                                'size_height'=> "300px",
                                'size_width'=> "100%",

                            ),array(
                                'lat'=>FALSE,
                                'lng'=>FALSE,
                                'address'=>TRUE,
                                'custom_address'=>TRUE,
                            ),array(
                                'address'=>_lang("Enter address")." (*)",
                            ));

                            echo $map['html'];
                            AdminTemplateManager::addScript($map['script']);
                            $data['location_fields_id'] = $map['fields_id'];

                            ?>

                            <div class="row">
                                <div class="col-50">
                                    <a class="big-button button" id="back-step-0"> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-right":"mdi-arrow-left"?>"></i>&nbsp;<?= _lang("Back") ?></a>
                                </div>
                                <div class="col-50">
                                    <a class="big-button button button-fill link" id="go-step-2"><?= _lang("Continue") ?> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-left":"mdi-arrow-right"?>"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="step-2-form hidden">

                            <h1 class="create-title"><?=_lang("Event Detail")?></h1>

                            <ul>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="name" placeholder="<?=_lang("Event title")?> (*)" class="">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <textarea class="resizable" id="description" placeholder="<?=_lang("Description")?> (*)"></textarea>
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="phone" placeholder="<?=_lang("Phone Number Ex: +1 000-000-000")?>" class="">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="website" placeholder="<?=_lang("Website Ex: http://google.com")?>" class="">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>

                                <li class="item-content item-input" style="visibility: hidden">
                                    <div class="item-inner">

                                    </div>
                                </li>

                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="date_b" placeholder="<?=_lang("Date begin")?>" class="">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>

                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="date_e" placeholder="<?=_lang("Date end")?>" class="">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="row">
                                <div class="col-50">
                                    <a class="big-button button" id="back-step-1"> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-right":"mdi-arrow-left"?>"></i>&nbsp;<?= _lang("Back") ?></a>
                                </div>
                                <div class="col-50">
                                    <a class="big-button button button-fill link" id="go-step-3"><?= _lang("Continue") ?> <i
                                                class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-left":"mdi-arrow-right"?>"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="step-3-form hidden">

                            <h1 class="create-title"><?=_lang("Event Photos")?></h1>

                            <div style="width: 100%;display: block;clear: both">
                                <h3><i class="mdi mdi-camera"></i>&nbsp;<?=_lang("Upload Photos")?></h3>
                            <?php

                                $upload_plug = $this->uploader->plugin(array(
                                    "limit_key"     => "publishFiles",
                                    "token_key"     => "SzYjES-4555",
                                    "limit"         => MAX_STORE_IMAGES,
                                ));

                                echo $upload_plug['html'];
                                AdminTemplateManager::addScript($upload_plug['script']);
                                $data['uploader_variable'] = $upload_plug['var'];

                                ?>

                                <div style="clear: both;margin-bottom: 35px;"></div>
                            </div>

                            <div style="width: 100%;display: block;clear: both">
                                <div class="row">
                                    <div class="col-50">
                                        <a class="big-button button" id="back-step-2"> <i
                                                    class="mdi <?=Translate::getDir()=="rtl"?"mdi-arrow-right":"mdi-arrow-left"?>"></i>&nbsp;<?= _lang("Back") ?></a>
                                    </div>
                                    <div class="col-50">
                                        <a class="big-button button button-fill link" id="save-business"> <i
                                                    class="mdi mdi-check"></i>&nbsp;<?= _lang("Save") ?></a>
                                    </div>
                                </div>
                            </div>

                        </div>




                    </div>

                </div>
            </div>
        </div>
    </div>



<?php

$script = $this->load->view('business_manager/event/scripts/create-script',$data,TRUE);
AdminTemplateManager::addScript($script);


?>