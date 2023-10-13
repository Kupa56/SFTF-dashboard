<?php

$offer = $offer[Tags::RESULT][0];


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

                            <h1 class="create-title"><?=_lang("Create Offer")?></h1>

                            <ul>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input id="autocomplete-stores" type="text" placeholder="<?=_lang("Select Business")?> (*)" class="" value="<?=$offer['store_name']?>">
                                            <span class="input-clear-button"></span>
                                            <input type="hidden" id="store_id" value="<?=$offer['store_id']?>">
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

                            <h1 class="create-title"><?=_lang("Offer detail")?></h1>

                            <ul>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="name" placeholder="<?=_lang("Offer title")?> (*)" class="" value="<?=$offer['name']?>">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <textarea class="resizable" id="description" placeholder="<?=_lang("Description")?> (*)"><?=$offer['description']?></textarea>
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>

                            </ul>

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

                            <h1 class="create-title"><?=_lang("Pricing & Scheduling")?></h1>

                            <ul>

                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <select id="offer_type">
                                                <option value="0">-- <?= Translate::sprint('Select Type') ?></option>
                                                <option value="1"><?= Translate::sprint('Price') ?></option>
                                                <option value="2"><?= Translate::sprint('Percent') ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </li>

                                <li class="item-input price-form hidden">
                                    <div class="row">

                                        <div class="col-50">
                                            <div class="item-content ">
                                                <div class="item-inner">
                                                    <div class="item-input-wrap">
                                                        <input type="number" id="offer_price" placeholder="<?=_lang("Price")?>" class="" value="<?php if($offer['value_type']=="price") echo $offer['offer_value']?>">
                                                        <span class="input-clear-button"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-50">
                                            <div class="item-content ">
                                                <div class="item-inner">
                                                    <div class="item-input-wrap">
                                                    <?php

                                                        $currencies = $this->mCurrencyModel->getAllCurrencies();

                                                        ?>
                                                        <select id="offer_currency">
                                                            <option selected="selected"
                                                                    value="0"> <?= Translate::sprint("Select") ?></option>
                                                        <?php

                                                            foreach ($currencies as $key => $value) {
                                                                if ($value['code'] == DEFAULT_CURRENCY)
                                                                    echo '<option selected="selected" value="' . $value['code'] . '">' . $value['name'] . ' (' . $value['code'] . ')</option>';
                                                                else
                                                                    echo '<option value="' . $value['code'] . '">' . $value['name'] . ' (' . $value['code'] . ')</option>';

                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="item-content item-input percent-form hidden">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="number" id="offer_percent" placeholder="<?=_lang("Percent")?> Ex: 20%,30%..." class="" value="<?php if($offer['value_type']=="percent") echo $offer['offer_value']?>">
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
                                            <input type="text" id="date_b" placeholder="<?=_lang("Date begin")?>" class="" value="<?=date("Y-m-d",strtotime($offer['date_start']))?>">
                                            <span class="input-clear-button"></span>
                                        </div>
                                    </div>
                                </li>

                                <li class="item-content item-input">
                                    <div class="item-inner">
                                        <div class="item-input-wrap">
                                            <input type="text" id="date_e" placeholder="<?=_lang("Date end")?>" class="" value="<?=date("Y-m-d",strtotime($offer['date_end']))?>">
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

                            <h1 class="create-title"><?=_lang("Offer Photos")?></h1>

                            <div style="width: 100%;display: block;clear: both">
                                <h3><i class="mdi mdi-camera"></i>&nbsp;<?=_lang("Upload Photos")?></h3>
                            <?php


                                $images = $offer['images'];
                                if ($images != "" AND !is_array($images)) {
                                    $images = json_decode($images);
                                }


                                $upload_plug = $this->uploader->plugin(array(
                                    "limit_key"     => "publishFiles",
                                    "token_key"     => "SzYjES-4555",
                                    "limit"         => MAX_OFFER_IMAGES,
                                    "cache"         => $images
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

$data['offer_object'] = $offer;
$data['offer_id'] = $offer['id_offer'];
$script = $this->load->view('business_manager/offer/scripts/edit-script', $data, TRUE);
AdminTemplateManager::addScript($script);


?>