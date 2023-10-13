<?php if (ModulesChecker::isEnabled('pack')): ?>
    <?php


        $expired_date = $this->mUserBrowser->getData('will_expired');
        $days = MyDateUtils::getDays($expired_date);


        $grp_access_id = $this->mUserBrowser->getData('grp_access_id');
        $pack_id = $this->mUserBrowser->getData('pack_id');


        $trial_period_date = $this->mUserBrowser->getData('trial_period_date');

        if ($trial_period_date != NULL)
            $trial_period = MyDateUtils::getDays($trial_period_date);
        else
            $trial_period = 0;


        ?>

    <?php if (GroupAccess::isGranted('pack')): ?>

            <li class="messages-menu menu-subscription no-hover hidden-xs">
                <a class="no-hover font-size12px">
                    <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprint("Your are connected as admin") ?>
                    .
                </a>
            </li>

    <?php elseif ($pack_id > 0): ?>

        <?php
            $pack = $this->mPack->getPack($pack_id);
            ?>

        <?php if ($trial_period <= 0 && $pack != NULL): ?>
                <li class="messages-menu menu-subscription  no-hover hidden-xs">
                    <a class="no-hover font-size12px">
                    <?php if ($days > 7): ?>
                            <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                        <?php if ($this->mPack->canUpgrade()): ?>
                                <u class="text-blue cursor-pointer"
                                   onclick="location.href = '<?= site_url("/pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade") ?></u>
                        <?php endif; ?>
                    <?php elseif ($days < 7 && $days > 0): ?>
                            <i class="fa fa-warning text-yellow font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                            <u class="text-blue cursor-pointer"
                               onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                            &nbsp;<?=_lang("or")?>&nbsp;
                            <u class="text-blue cursor-pointer"
                               onclick="location.href = '<?= site_url("pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade") ?></u>

                        &nbsp;<?=_lang("or")?>&nbsp;
                            <u class="text-blue cursor-pointer"
                               onclick="location.href = '<?= site_url("pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade") ?></u>

                    <?php else: ?>
                            <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u> plan has been expired", array($pack->name)) ?>.
                            <u class="text-blue cursor-pointer"
                               onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                            &nbsp;<?=_lang("or")?>&nbsp;
                            <u class="text-blue cursor-pointer"
                               onclick="location.href = '<?= site_url("pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade") ?></u>
                    <?php endif; ?>
                    </a>
                </li>
        <?php else: ?>
                <li class="messages-menu menu-subscription  no-hover hidden-xs">
                    <a class="no-hover font-size12px">
                        <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array("Trial", PackHelper::getValidDur($trial_period_date))) ?>
                        .
                    </a>
                </li>
        <?php endif; ?>


    <?php elseif ($pack_id == 0): ?>
            <li class="messages-menu menu-subscription  no-hover hidden-xs">
                <a class="no-hover font-size12px">
                    <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprint("Your account is not for business") ?>
                    .&nbsp;&nbsp;
                    <u class="text-blue cursor-pointer"
                       onclick="location.href = '<?= site_url("pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade to business") ?></u>
                </a>
            </li>

    <?php endif; ?>
<?php endif; ?>
