<?php
$reservations = $data[Tags::RESULT];
$pagination = $data['pagination'];


?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath . "/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header" style="width : 100%;">
                        <div class=" row ">
                            <div class="pull-left col-md-8 box-title">
                                <b><?= Translate::sprint("My bookings") ?></b>
                            </div>
                            <div class="pull-right col-md-4"></div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <div class="row">
                            <div class="list_general">
                            <?php if (!empty($reservations)) : ?>
                                <ul>
                                <?php foreach ($reservations as $key => $reservation): ?>
                                        <?php

                                            $storeData = $this->mStoreModel->getStoreData($reservation['store_id']);
                                            $ownerData = $this->mUserModel->getUserData($storeData['user_id']);
                                            $clientData = $this->mUserModel->getUserData($reservation['user_id']);
                                            $image = ImageManagerUtils::getFirstImage($storeData['images'], ImageManagerUtils::IMAGE_SIZE_200);

                                            ?>
                                            <li>
                                                <figure><img src="<?= $image ?>" alt=""></figure>
                                                <h4>
                                                    <?= $reservation['store_name'] ?>
                                                    #<?= str_pad($reservation['id'], 7, "0", STR_PAD_LEFT) ?>
                                                <?php
                                                    if (isset($reservation['status']) && $reservation['status'] != "") {
                                                        $statusParser = explode(";", $reservation['status']);
                                                        echo "<span class=badge style='background:" . $statusParser[1] . "'>" . $statusParser[0] . "</span>";
                                                    }
                                                    ?>
                                                </h4>
                                                <ul class="booking_list">
                                                    <li><strong><?= _lang("Booking ID") ?>:</strong>
                                                        #<?= $reservation['id'] ?></li>
                                                    <li><strong><?= _lang("Booking date") ?>
                                                            :</strong> <?= date("d M Y", strtotime($reservation['created_at'])) ?>
                                                    </li>
                                                    <li><strong><?= _lang("Booking details") ?>:</strong> <a
                                                                class="bookingDetail" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="bookingDetailed-<?= $reservation['id'] ?>"
                                                                href="#bookingDetailed-<?= $reservation['id'] ?>"><?= _lang("Show Details") ?></a>

                                                        <div class="collapse booking-detailed" id="bookingDetailed-<?= $reservation['id'] ?>">
                                                        <?php
                                                            $this->load->view('booking/backend/booking_popup_detail', array(
                                                                'reservation' => $reservation
                                                            ));
                                                            ?>
                                                        </div>

                                                    </li>
                                                </ul>

                                                <p>
                                                <?php if ($storeData['telephone'] != ""): ?>
                                                        <a href="tel:<?= $storeData['telephone'] ?>"
                                                           class="btn btn-default gray"><i
                                                                    class="fa fa-fw fa-phone"></i> <?= _lang("Call") ?>
                                                        </a>
                                                <?php endif; ?>
                                                <?php if ($storeData['canChat'] == 1): ?>
                                                        <a href="#" data-id="<?= $reservation['id'] ?>"
                                                           class="btn btn-default gray send-message"><i
                                                                    class="fa fa-fw fa-envelope"></i> <?= _lang("Send Message") ?>
                                                        </a>
                                                <?php endif; ?>
                                                </p>
                                            <?php if ($reservation['status_id'] != -1): ?>
                                                    <ul class="buttons">
                                                        <li><a href="#" class="btn btn-default cancel"
                                                               data-id="<?= $reservation['id'] ?>"><i
                                                                        class="fa fa-fw fa-times-circle-o"></i> <?= _lang("Cancel") ?>
                                                            </a></li>
                                                    </ul>
                                            <?php endif; ?>
                                            </li>

                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="result-not-found">
                                    <?=_lang("No booking!")?>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                <?php
                                    echo $pagination->links(array(
                                        "status" => intval(RequestInput::get("status")),
                                    ), $pagination_url);

                                    ?>
                                </div>
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
        <!-- /.row -->
</div>


<?php
$script = $this->load->view('booking/backend/scripts/client-bookings-script', NULL, TRUE);
AdminTemplateManager::addScript($script);
?>
