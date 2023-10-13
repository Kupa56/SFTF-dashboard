<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);



$pendingCountOwner = $this->mBookingModel->countPending(TRUE);
$pendingCountOwner = isset($pendingCountOwner[Tags::COUNT]) ? $pendingCountOwner[Tags::COUNT] : 0;

$pendingCountAdmin = $this->mBookingModel->countPending();
$pendingCountAdmin = isset($pendingCountAdmin[Tags::COUNT]) ? $pendingCountAdmin[Tags::COUNT] : 0;

$all = $pendingCountAdmin+$pendingCountOwner;


?>


<?php  if(ModulesChecker::isEnabled("booking"))
    if (GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING) &&  GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING_CONFIG) ) :

    ?>

        <li class="treeview <?php if ($uri_m == "booking") echo "active"; ?>">
            <a href="<?= admin_url("booking/all_reservations") ?>"><i class="mdi  mdi-calendar-clock"></i>
                &nbsp;<span> <?= Translate::sprint("Reservations") ?></span>

            <?php if ( ($all) > 0): ?>
                    <small class="badge pull-right bg-yellow"><?= ($all) ?></small>
            <?php endif; ?>

            </a>

            <ul class="treeview-menu">

            <?php if (GroupAccess::isGranted('booking', GRP_MANAGE_BOOKING_CONFIG)): ?>
                    <li class="<?php if ($uri_m == "booking" && $uri_parent == "all_reservation") echo "active"; ?>">
                        <a href="<?= admin_url("booking/all_reservations") ?>"><i class="mdi mdi-cart-outline"></i>
                            &nbsp;<?= Translate::sprint("All reservations") ?>
                        <?php if ($pendingCountAdmin > 0): ?>
                                <small class="badge pull-right bg-yellow"><?= $pendingCountAdmin ?></small>
                        <?php endif; ?>
                        </a>
                    </li>
            <?php endif; ?>


                <li class="<?php if ($uri_m == "booking" && $uri_parent == "my_reservations") echo "active"; ?>">
                    <a href="<?= admin_url("booking/my_reservations") ?>"><i class="mdi mdi-cart-outline"></i>
                        &nbsp;<?= Translate::sprint("My reservations") ?>
                    <?php if ($pendingCountOwner > 0): ?>
                            <small class="badge pull-right bg-yellow"><?= $pendingCountOwner ?></small>
                    <?php endif; ?>
                    </a></li>


            </ul>

        </li>


<?php elseif(GroupAccess::isGranted('booking', GRP_MANAGE_BOOKING)): ?>

    <li class=" <?php if ($uri_m == "booking") echo "active"; ?>">
        <a href="<?= admin_url("booking/my_reservations") ?>"><i class="mdi  mdi-calendar-clock"></i>
            &nbsp;<span> <?= Translate::sprint("Reservations") ?></span>
        <?php if ( ($pendingCountOwner) > 0): ?>
                <small class="badge pull-right bg-yellow"><?= ($pendingCountOwner) ?></small>
        <?php endif; ?>

        </a>
    </li>

<?php endif; ?>

<?php  if(ModulesChecker::isEnabled("booking")
    && version_compare(ModulesChecker::getField("cms","version_name"), '2.0.2', '>=')): ?>
<li class=" <?php if ($uri_m == "booking") echo "active"; ?>">
    <a href="<?= admin_url("booking/client_bookings") ?>"><i class="mdi  mdi-calendar-clock"></i>
        &nbsp;<span> <?= Translate::sprint("My bookings") ?></span>
    </a>
</li>
<?php endif; ?>
