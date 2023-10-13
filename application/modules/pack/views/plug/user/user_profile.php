<?php if(!GroupAccess::isGranted("pack")): ?>
<div class="col-md-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><strong><?= Translate::sprint("Subscription & Plan") ?></strong>
            </h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body margin">


        <?php


            $expired_date = $this->mUserBrowser->getData('will_expired');
            $days = MyDateUtils::getDays($expired_date);


            $grp_access_id = $this->mUserBrowser->getData('grp_access_id');
            $pack_id = $this->mUserBrowser->getData('pack_id');


            $pack = $this->mPack->getPack($pack_id);


            $trial_period_date = $this->mUserBrowser->getData('trial_period_date');

            if ($trial_period_date != NULL)
                $trial_period = MyDateUtils::getDays($trial_period_date);
            else
                $trial_period = 0;


            ?>


        <?php if ($pack_id > 0): ?>`

            <?php if ($trial_period <= 0): ?>

                <?php if ($days > 7): ?>
                        <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                <?php elseif ($days < 7 && $days > 0): ?>
                        <i class="fa fa-warning text-yellow font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                        <u class="text-blue cursor-pointer"
                           onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                <?php else: ?>
                        <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u> plan has been expired", array($pack->name)) ?>.
                        <u class="text-blue cursor-pointer"
                           onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                <?php endif; ?>

                    &nbsp;&nbsp;<u class="text-blue cursor-pointer"
                                   onclick="location.href = '<?= site_url("/pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Update running plan") ?></u>


            <?php else: ?>

                    <i class="mdi mdi-account font-size18px"></i>  <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array("Trial", PackHelper::getValidDur($trial_period_date))) ?>.

            <?php endif; ?>


        <?php else: ?>

                <u class="text-blue cursor-pointer"
                   onclick="location.href = '<?= site_url("/pack/pickpack?req=upgrade") ?>';"><?= Translate::sprint("Upgrade your account") ?></u>


        <?php endif; ?>


            <br/>
            <br/>

        </div>

    </div>
    <!-- /.box-body -->
</div>
<?php endif; ?>