<?php if (ModulesChecker::isEnabled('pack')): ?>
<?php

    $expired_date = $this->mUserBrowser->getData('will_expired');
    $days = MyDateUtils::getDays($expired_date);


    $grp_access_id = $this->mUserBrowser->getData('grp_access_id');
    $pack_id = $this->mUserBrowser->getData('pack_id');

    ?>

<?php if ($grp_access_id!=1 AND $grp_access_id!=2): ?>
        <li class="messages-menu menu-subscription no-hover">
            <a class="no-hover font-size12px">
            <?php if ($days > 7): ?>
                    <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your pack will expired after %s days", array($days)) ?>.
                <?php if ($this->mPack->canUpgrade()): ?>
                        <u class="text-blue cursor-pointer"
                           onclick="location.href = '<?= site_url("/pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade") ?></u>
                <?php endif; ?>
            <?php elseif ($days < 7 && $days > 0): ?>
                    <i class="fa fa-warning text-yellow font-size18px"></i> <?= Translate::sprintf("Your pack will expired after %s days", array($days)) ?>.
                    <u class="text-blue cursor-pointer"
                       onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
            <?php else: ?>
                    <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprint("Your pack has been expired") ?>.
                    <u class="text-blue cursor-pointer"
                       onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
            <?php endif; ?>
            </a>
        </li>
<?php elseif ($grp_access_id!=1 AND $grp_access_id!=2 AND  $pack_id == 0): ?>
        <li class="messages-menu menu-subscription no-hover">
            <a class="no-hover font-size12px">
                <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprint("Your account is not for business") ?>
                .&nbsp;&nbsp;
                <u class="text-blue cursor-pointer"
                   onclick="location.href = '<?= site_url("pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade to business") ?></u>
            </a>
        </li>
<?php elseif ($grp_access_id==1 ) :?>
        <li class="messages-menu menu-subscription no-hover">
            <a class="no-hover font-size12px">
                <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your are connected as admin") ?>
                .
            </a>
        </li>
<?php endif; ?>
<?php endif; ?>
