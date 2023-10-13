<?php

$reviews = $data["reviews"];
$pagination = $data['pagination'];
$store = $store[Tags::RESULT][0];

?>


<div class="content-wrapper">

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
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?=Translate::sprint("Store detail")?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label><?= Translate::sprint("Select Store") ?></label>
                                    <select id="selectStore" class="form-control select2 selectStore" style="width: 100%;">
                                        <option selected="selected" value="0">
                                            <?= Translate::sprint("Select store", "") ?></option>
                                    </select>
                                    <br>
                                    <table cellpadding="10">
                                        <td style="padding-right: 10px">
                                        <?php

                                            try {


                                                if (!is_array($store['images']))
                                                    $images = json_decode($store['images'], JSON_OBJECT_AS_ARRAY);
                                                else
                                                    $images = $store['images'];


                                                if (isset($images[0])) {
                                                    $images = $images[0];
                                                    if (isset($images['100_100']['url'])) {
                                                        echo '<img src="' . $images['100_100']['url'] . '"width="80" height="80" alt="Product Image">';
                                                    } else {
                                                        echo '<img src="' . adminAssets("images/def_logo.png") . '"width="80" height="80" alt="Product Image">';
                                                    }
                                                } else {
                                                    echo '<img src="' . adminAssets("images/def_logo.png") . '"width="80" height="80" alt="Product Image">';
                                                }

                                            } catch (Exception $e) {
                                                $e->getMessage();
                                                echo '<img src="' . adminAssets("images/def_logo.png") . '"width="80" height="80" alt="Product Image">';
                                            }

                                            ?>
                                        </td>
                                        <td>
                                            <strong class="font-size18px"><?=$store['name']?></strong><br>
                                            <span><?=$store['address']?></span><br>
                                            <span style="font-size: 12px"><i class="fa fa-star text-yellow"></i>&nbsp;&nbsp;<?php if (!empty($store['votes'])) {
                                                    echo round($store['votes'], 2) . " /5";
                                                } else {
                                                    echo " 0 ";
                                                } ?> </span>
                                        </td>
                                    </table>

                                </div>
                            </div>
                            <div class="col-md-7" style="padding: 20px">

                                <table cellpadding="10">
                                    <tr>
                                        <td style="padding-right: 10px"> <span><?=$this->mStoreModel->nbr_reviews_per_rate($store["id_store"],5)?></span></td>
                                        <td>
                                            <div class="r5">
                                            <?php for ($i=1;$i<=5;$i++):?>
                                                    <i class="fa fa-star text-yellow"></i>
                                            <?php endfor; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding-right: 10px"><span><?=$this->mStoreModel->nbr_reviews_per_rate($store["id_store"],4)?></span></td>
                                        <td>
                                            <div class="r4">
                                            <?php for ($i=1;$i<=4;$i++):?>
                                                    <i class="fa fa-star text-yellow"></i>
                                            <?php endfor; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding-right: 10px"><span><?=$this->mStoreModel->nbr_reviews_per_rate($store["id_store"],3)?></span></td>
                                        <td>
                                            <div class="r3">
                                            <?php for ($i=1;$i<=3;$i++):?>
                                                    <i class="fa fa-star text-yellow"></i>
                                            <?php endfor; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding-right: 10px"><span><?=$this->mStoreModel->nbr_reviews_per_rate($store["id_store"],2)?></span></td>
                                        <td>
                                            <div class="r2">
                                            <?php for ($i=1;$i<=2;$i++):?>
                                                    <i class="fa fa-star text-yellow"></i>
                                            <?php endfor; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding-right: 10px"><span><?=$this->mStoreModel->nbr_reviews_per_rate($store["id_store"],1)?></span></td>
                                        <td>
                                            <div class="r1">
                                            <?php for ($i=1;$i<=1;$i++):?>
                                                    <i class="fa fa-star text-yellow"></i>
                                            <?php endfor; ?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>


                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>


            </div>
            <div class="col-md-12">

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?=Translate::sprint("Reviews")?> :</b></h3>
                    </div>

                    <div class="box-body chat" id="chat-box">

                        <table id="example2" class="table table-bordered table-hover">
                        <?php foreach ($reviews AS $review):


                                $image = adminAssets("images/profile_placeholder.png");

                                if($review->user_id>0){

                                    $user =  $this->mUserModel->syncUser(array(
                                        'user_id'=>$review->user_id
                                    ));

                                    if($user!=NULL and isset($user[Tags::RESULT][0])){
                                        $user = $user[Tags::RESULT][0];
                                        if(isset($user['images'][0]['200_200']['url'])){
                                            $image = $user['images'][0]['200_200']['url'];
                                        }else{

                                        }
                                    }
                                }


                                ?>
                                <!-- chat item -->
                                <tr>
                                <td width="10%"  valign="center">
                                    <div class="image-container-40"  style="background-image: url('<?= $image ?>');">
                                        <img  class="direct-chat-img invisible" src="<?= $image ?>" alt="user image">
                                    </div>
                                </td>
                                <td width="60%" valign="center">
                                    <b><?=$review->pseudo!=""?ucfirst( htmlspecialchars($review->pseudo)):_lang("Guest") ?></b><br>
                                    <?=$review->review!=""?htmlspecialchars($review->review):_lang("**No comment**")?>
                                </td>
                                <td width="30%" align="right"  valign="center">
                                    <small class="text-muted pull-right">

                                    <?php

                                        $rate = ceil($review->rate);

                                        for ($i = 1; $i <= $rate; $i++) { ?>
                                            <span class="mdi mdi-star"
                                                  style="color: #db8b0b;font-size: 15px;"></span>
                                        <?php


                                            if ($i == $rate) {

                                                for ($j = $i; $j < 5; $j++) {
                                                    echo ' <span class="mdi mdi-star-outline"style="color: #db8b0b;font-size: 15px;"></span>';
                                                }
                                                break;
                                            }
                                        }

                                        ?>


                                    </small>
                                </td>
                            <?php if ($store['status'] == 1){ ?>
                                <td>
                                    <a href="#" data-toggle="modal"
                                       data-target="#modal-default-<?= md5($review->id_rate) ?>">
                                        <button type="button" class="btn btn-sm"><span
                                                    class="glyphicon glyphicon-trash"></span></button>
                                </td>
                                </tr>

                                <!-- Popup to delete the reviews-->
                                <div class="modal fade" id="modal-default-<?= md5($review->id_rate) ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"></h4>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">

                                                    <div style="text-align: center">
                                                        <h3 class="text-red"><?= Translate::sprint("Are you sure you want to delete") ?> <strong> <?=$review->review . " ?" ?> </strong> </h3>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default pull-left"
                                                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                                                <button type="button" id="_deleteRev"
                                                        data="<?= ($review->id_rate) ?>"
                                                        class="btn btn-flat btn-primary"><?= Translate::sprint("Delete", "Delete") ?></button>
                                            </div>
                                        </div>

                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>

                        <?php } ?>
                                <!-- /.item -->


                        <?php endforeach; ?>
                        </table>
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                        <?php

                            echo $pagination->links(array(
                                "id"         => intval(RequestInput::get("id")),
                            ),admin_url("store/reviews"));

                            ?>
                        </div>

                    <?php

                        if(count($reviews)==0){
                            echo Translate::sprint("No reviews");
                        }

                        ?>
                    </div>

                </div>

            </div>



    </section>

</div>

<?php

$data['store'] = $store;

$script = $this->load->view('store/backend/html/scripts/reviews-script',$data,TRUE);
AdminTemplateManager::addScript($script);

?>

