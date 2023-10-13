<?php

$reviews = $this->mStoreModel->getReviews(array(
    'limit' => 5
));
$reviews = $reviews['reviews'];


?>

<div class=" col-md-6">

<?php if (!empty($reviews)) { ?>

        <div class="box box-solid">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title"><b><?= Translate::sprint("Last_reviews") ?> </b></h3>

                <div class="box-tools pull-right" data-toggle="tooltip" title=""
                     data-original-title="Status">
                    <div class="btn-group" data-toggle="btn-toggle">

                    </div>
                </div>
            </div>

            <div class="box-body chat" id="chat-box">

                <table id="example2" class="table table-bordered table-hover">
                <?php foreach ($reviews AS $review) {
                        if ($review->review != "" AND isset($review->review)) {

                            if ($review->pseudo != "" AND isset($review->pseudo)) {

                                $user = $this->mUserModel->getUserByGuestId($review->guest_id);
                                $image = adminAssets("images/profile_placeholder.png");

                                if ($user != NULL and isset($user[Tags::RESULT][0])) {
                                    $user = $user[Tags::RESULT][0];
                                    if (isset($user['images'][0]['200_200']['url'])) {
                                        $image = $user['images'][0]['200_200']['url'];
                                    } else {
                                        $image = adminAssets("images/profile_placeholder.png");
                                    }
                                }

                                ?>
                                <!-- chat item -->
                                <tr>
                                    <td width="10%" valign="center">
                                        <div class="image-container-40"
                                             style="background-image: url('<?= $image ?>');">
                                            <img class="direct-chat-img invisible" src="<?= $image ?>"
                                                 alt="user image">
                                        </div>
                                    </td>
                                    <td width="50%" valign="center">
                                        <a class="name" onclick="return false;">
                                            <b><?= ucfirst(htmlspecialchars($review->pseudo)) ?></b>
                                        </a>
                                                <br><a href="<?= admin_url("store/reviews?id=" . $review->store_id) ?>">
                                            <span class=""><?= Text::echo_output(Text::substrwords($review->nameStr,40)); ?></span>
                                        </a>

                                        <br>
                                    <?php
                                        $reviewMod = Text::substrwords($review->review,100);
                                        echo htmlspecialchars($reviewMod);

                                        ?>

                                    </td>
                                    <td width="40%" align="right" valign="center">
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
                                </tr>
                                <!-- /.item -->


                        <?php }
                        }
                    } ?>
                </table>

            </div>


        </div>

<?php } ?>


</div>
