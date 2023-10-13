<?php


$categories = $categories[Tags::RESULT];


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <form id="form" role="form">

               <div class="col-sm-12">
                   <div class="nav-tabs-custom">
                       <ul class="nav nav-tabs">
                           <li class="<?=(input_get("tab")=="")?"active":""?>">
                               <a href="#detail" class="title uppercase" data-toggle="tab"
                                  aria-expanded="false"><?= Translate::sprint("Detail") ?></a></li>

                           <li class=" <?=(input_get("tab")=="images")?"active":""?>">
                               <a href="#images" class="title uppercase" data-toggle="tab"
                                  aria-expanded="false"><?= Translate::sprint("Images") ?></a></li>

                           <li class="<?=(input_get("tab")=="location")?"active":""?>">
                               <a href="#location" class="title uppercase" data-toggle="tab"
                                  aria-expanded="false"><?= Translate::sprint("Location") ?></a></li>

                           <li class="<?=(input_get("tab")=="more")?"active":""?>">
                               <a href="#more" class="title uppercase" data-toggle="tab"
                                  aria-expanded="false"><?= Translate::sprint("More") ?></a></li>
                       </ul>

                       <div class="tab-content">
                           <div class="tab-pane <?=(input_get("tab")=="")?"active":""?>" id="detail">
                               <div class="box-body">
                                   <div class="row">
                                       <div class="col-sm-6">
                                           <div class="form-group">
                                               <label><?= Translate::sprint("Name", "") ?> : </label>
                                               <input type="text" class="form-control" data-field-translator="true"
                                                      placeholder="<?= Translate::sprint("Enter") ?> ..." name="name" id="name">
                                           </div>
                                           <div class="form-group">
                                               <label><?= Translate::sprint("Category", "") ?> :</label>

                                               <div class="row">
                                                   <div class="col-lg-12">
                                                       <select id="cat" name="cat" class="form-control selectCat select2">
                                                       <?php if (!empty($categories)) { ?>

                                                           <?php foreach ($categories AS $cat) { ?>
                                                                   <option value="<?= $cat['id_category'] ?>"><?= $cat['name'] ?></option>
                                                           <?php } ?>
                                                       <?php } ?>

                                                       </select>
                                                   </div>
                                                   <div class="col-lg-3">
                                                   </div>
                                               </div>

                                           </div>
                                           <div class="form-group">
                                               <label><?= Translate::sprint("Detail", "") ?> :</label>
                                               <textarea data-field-translator="true" name="editable-textarea" id="editable-textarea" class="form-control" style="height: 300px"></textarea>
                                           </div>
                                       </div>
                                       <div class="col-sm-6">
                                           <div class="form-group">
                                               <label><?= Translate::sprint("Phone Number", "") ?> :</label>
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


                                           <div class="form-group">
                                               <label><?= Translate::sprint("Video URL ", "") ?></label> <br>
                                               <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                                   <?=_lang("the field only accepts links from YouTube videos, please make sure to copy the link from youtube as following : https://www.youtube.com/watch?v=IS1FNtggVTc  ")?>
                                               </sup>
                                               <br>
                                               <input type="text" class="form-control"
                                                      placeholder="<?= Translate::sprint("Enter a valid youtube URL") ?> ..."
                                                      name="video_url" id="video_url">
                                           </div>


                                       </div>
                                   </div>

                               </div>
                           </div>
                           <div class="tab-pane <?=(input_get("tab")=="images")?"active":""?>" id="images">
                               <div class="box-body">

                                   <div class="row">
                                       <div class="col-sm-6">
                                           <div class="box box-solid">
                                               <div class="box-header with-border">
                                                   <h3 class="box-title"><b> <?= Translate::sprint("Store photos") ?></b></h3>
                                               </div>
                                               <!-- /.box-header -->
                                               <div class="box-body">
                                                   <div class="form-group required">


                                                   <?php

                                                       $upload_plug = $this->uploader->plugin(array(
                                                           "limit_key"     => "publishFiles",
                                                           "token_key"     => "SzYjES-4555",
                                                           "limit"         => MAX_STORE_IMAGES,
                                                       ));

                                                       echo $upload_plug['html'];
                                                       AdminTemplateManager::addScript($upload_plug['script']);

                                                       ?>


                                                   </div>
                                               </div>
                                               <!-- /.box-body -->
                                           </div>

                                       </div>
                                       <div class="col-sm-6">
                                       <?php

                                           if(ModulesChecker::isRegistred("gallery")){
                                               //load view
                                               $gallery_variable = $this->mGalleryModel->setup("store-gallery");
                                               $data['gallery_variable'] = $gallery_variable;
                                           }
                                           ?>
                                       </div>
                                   </div>


                               </div>
                           </div>
                           <div class="tab-pane <?=(input_get("tab")=="location")?"active":""?>" id="location">
                               <div class="box-body">

                                   <div class="row">
                                       <div class="col-sm-6">
                                           <div class="box box-solid">
                                               <div class="box-header with-border">
                                                   <h3 class="box-title">
                                                       <b><?= Translate::sprint("Drag the marker to get the exact position") ?></b>
                                                   </h3>
                                               </div>
                                               <div class="box-body">
                                                   <div class="form-group hidden">
                                                       <label> <?= Translate::sprint("Search", "") ?></label>
                                                       <input type="text" class="form-control"
                                                              placeholder="<?= Translate::sprint("Search") ?> ..." name="places" id="places"/>
                                                   </div>
                                               <?php
                                                   $map = LocationPickerManager::plug_pick_location(array(
                                                       'lat'=>ConfigManager::getValue("MAP_DEFAULT_LATITUDE"),
                                                       'lng'=>ConfigManager::getValue("MAP_DEFAULT_LONGITUDE"),
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
                                           </div>
                                       </div>
                                   </div>

                               </div>
                           </div>
                           <div class="tab-pane <?=(input_get("tab")=="more")?"active":""?>" id="more">
                               <div class="box-body">
                                   <div class="row">
                                       <div class="col-sm-6">
                                       <?php if(OPENING_TIME_ENABLED): ?>
                                               <div class="box box-solid ">
                                                   <div class="box-header with-border">
                                                       <h3 class="box-title"><b><i class="mdi mdi-calendar-clock"></i>&nbsp;&nbsp;<?= Translate::sprint("Opening time") ?></b></h3>
                                                       <div class="box-tools pull-right">
                                                           <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                       </div>
                                                   </div>
                                                   <!-- /.box-header -->
                                                   <div class="box-body">
                                                   <?php
                                                       $days = array(
                                                           'monday',
                                                           'tuesday',
                                                           'wednesday',
                                                           'thursday',
                                                           'friday',
                                                           'saturday',
                                                           'sunday',
                                                       );
                                                       ?>

                                                       <!-- text input -->
                                                       <div class="form-group">
                                                           <label id="opening_time">
                                                               <input type="checkbox" id="_opening_time" class="minimal">
                                                               &nbsp;&nbsp;<strong><?=Translate::sprint("Enable")?></strong>
                                                               &nbsp;&nbsp;
                                                           </label>

                                                           <div id="_h" class="hidden margin-top15px">

                                                           <?php foreach ($days as $key => $day): ?>
                                                                   <div class="form-group">
                                                                       <label><?=ucfirst(Translate::sprint($day))?></label>
                                                                       <div class="row">
                                                                           <div class="col-sm-6">
                                                                               <div class="input-group">
                                                                                   <div class="input-group-addon">
                                                                                       <input type="checkbox" data-key="<?=$day?>" class="_checked_d_<?=$key?>" id="_checked_d_<?=$day?>" />
                                                                                   </div>
                                                                                   <input placeholder="<?=Translate::sprint("Opening time")?>" type="text" class="form-control date-picker _o_d_<?=$key?>" id="_o_d_<?=$day?>" disabled/>
                                                                               </div>
                                                                           </div>
                                                                           <div class="col-sm-6">
                                                                               <input placeholder="<?=Translate::sprint("Closing time")?>" type="text" class="form-control date-picker _c_d_<?=$key?>" id="_c_d_<?=$day?>" disabled/>
                                                                           </div>

                                                                       </div>

                                                                   </div>
                                                           <?php endforeach; ?>

                                                           </div>


                                                       <?php

                                                           $data['days'] = $days;
                                                           $ot_script = $this->load->view('store/backend/html/scripts/create-opening-time-script',$data,TRUE);
                                                           AdminTemplateManager::addScript($ot_script);

                                                           ?>
                                                       </div>

                                                   </div>
                                                   <!-- /.box-body -->
                                               </div>
                                       <?php endif; ?>
                                           <div class="box box-solid">

                                               <div class="box-header with-border">
                                                   <h3 class="box-title">
                                                       <b><?= Translate::sprint("Store Options") ?></b></h3>
                                               </div>

                                               <div class="box-body">

                                               <?php if(ModulesChecker::isEnabled("messenger")): ?>
                                                   <div class="form-group">
                                                       <label> <?php echo Translate::sprint("Enable chat feature for this store"); ?> </label>
                                                       <br>
                                                      <label>
                                                          <input class="form-check-input" name="canChat"
                                                                 type="checkbox"  checked
                                                                 id="canChat"/>
                                                          <?= Translate::sprint("Enable chat") ?>
                                                      </label>
                                                   </div>
                                               <?php endif; ?>

                                               <?php if(ModulesChecker::isEnabled("booking")): ?>
                                                   <div class="form-group">
                                                       <label> <?php echo Translate::sprint("Enable booking feature for this store"); ?> </label>
                                                       <br>
                                                       <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                                           <?=_lang("After creating the store, you can edit the store detail to add the more services  ")?>
                                                       </sup>
                                                       <br>
                                                       <label>
                                                           <input class="form-check-input" name="book"
                                                                  type="checkbox"  checked
                                                                  id="book"/>
                                                           <?= Translate::sprint("Enable booking") ?>
                                                       </label>
                                                   </div>
                                               <?php endif; ?>

                                                   <div class="form-group">
                                                       <label> <?php echo Translate::sprint("Enable Affiliate link for this store"); ?> </label>
                                                       <input class="form-control" name="affiliate"
                                                              type="text"  placeholder="<?=_lang("Enter external link")?>"
                                                              id="affiliate"/>
                                                   </div>

                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="box-footer">

                           <?php

                               $usr_id = $this->mUserBrowser->getData('id_user');
                               $nbr_stores = UserSettingSubscribe::getUDBSetting($usr_id,KS_NBR_STORES);


                               ?>

                           <?php if($nbr_stores>0 or $nbr_stores==-1): ?>
                                   <button type="button" class="btn  btn-primary" id="btnCreate"><span
                                               class="glyphicon glyphicon-check"></span>
                                       <?= Translate::sprint("Create", "") ?> </button>
                                   <button type="reset" class="btn  btn-default"><span
                                               class="glyphicon glyphicon-remove"></span>
                                       <?= Translate::sprint("Clear", "") ?></button>
                           <?php else: ?>
                                   <button type="button" class="btn  btn-primary" id="btnCreate" disabled><span
                                               class="glyphicon glyphicon-check"></span>
                                       <?= Translate::sprint("Create", "") ?> </button>
                                   &nbsp;&nbsp;
                                   <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?=Translate::sprint(Messages::EXCEEDED_MAX_NBR_STORES)?></span>
                           <?php endif; ?>
                           </div>
                       </div>
                   </div>
               </div>

            </form>

    </section>

</div>
<?php

    $data['uploader_variable'] = $upload_plug['var'];

    $script = $this->load->view('store/backend/html/scripts/create-script',$data,TRUE);
    AdminTemplateManager::addScript($script);

?>