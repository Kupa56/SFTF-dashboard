

<li>
    <a href="<?=admin_url("business_manager/booking?id=".$object['id'])?>" data-id="<?=$object['id']?>" data-id="<?=$object['id']?>" class="item item-booking">
        <div class="item-b">

            <div class="item-inner">
                <div class="item-title"><?="#" . str_pad($object['id'], 6, 0, STR_PAD_LEFT)?></div>
            <?php
                if (isset($object['status']) && $object['status'] != "") {
                    $statusParser = explode(";", $object['status']);
                    echo "<div class='item-subtitle badge' style='background:" . $statusParser[1] . "'>" . $statusParser[0] . "</div>";
                }
                ?>


                <div class="item-subtitle bottom-subtitle"><i class="mdi mdi-map-marker"></i>
                <?php
                        $store = $this->mBookingModel->getStore($object['store_id']);
                        echo  ucfirst($store['name']);
                    ?>
                </div>
            </div>
        </div>
    </a>
</li>

