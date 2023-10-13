<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>

<?php if (GroupAccess::isGranted('event')) : ?>
<li class="treeview <?php if ($uri_m == "event") echo "active"; ?>">
    <a href="<?= admin_url("event/events") ?>"><i class="mdi mdi-calendar-text "></i> &nbsp;
        <span><?= Translate::sprint("Manage Events", "") ?> </span>
        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
    </a>

    <ul class="treeview-menu">

    <?php   if (GroupAccess::isGranted('event',MANAGE_EVENTS)): ?>
            <li class="<?php if ($uri_parent == "all_events") echo "active"; ?>">
                <a href="<?= admin_url("event/all_events") ?>"><i class="mdi mdi-format-list-bulleted"></i> &nbsp;<span>
                                <?= Translate::sprint("All Events", "") ?></span></a>
            </li>
    <?php endif; ?>

        <li>
            <a href="<?= admin_url("event/my_events") ?>"><i class="mdi mdi-format-list-bulleted"></i>
                &nbsp;<span>
                                <?= Translate::sprint("My events", "") ?></span></a>
        </li>
    <?php if (GroupAccess::isGranted('event',ADD_EVENT)) : ?>
        <li class="<?php if ($uri_parent == "create") echo "active"; ?>">
            <a href="<?= admin_url("event/create") ?>"><i class="mdi mdi-plus-box "></i> &nbsp;<span>
                                 <?= Translate::sprint("Add new", "") ?></span></a>
        </li>
    <?php endif; ?>

    <?php if (GroupAccess::isGranted('event',MANAGE_EVENTS_PARTICIPANTS)) : ?>
          <!--  <li class="<?php if ($uri_parent == "create") echo "active"; ?>">
                <a href="<?= admin_url("event/participants") ?>"><i class="mdi mdi mdi-account-multiple-outline"></i> &nbsp;<span>
                                 <?= Translate::sprint("Participants") ?></span></a>
            </li>-->
    <?php endif; ?>

    </ul>

</li>
<?php endif; ?>
