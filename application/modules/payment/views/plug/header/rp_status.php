<?php if (ModulesChecker::isEnabled('pack')): ?>

<?php


    $pack_id = $this->mUserBrowser->getData('pack_id');

    ?>

<?php if ($pack_id == 0 and $papp->status == 0): ?>

        <li class="messages-menu no-hover">
            <a class="no-hover font-size12px">
                <i class="fa fa-spin fa-refresh font-size12px"></i> <?= Translate::sprintf("Subscription Processing...") ?>
            </a>
        </li>

<?php elseif($pack_id > 0 and $papp->status == 0): ?>

        <li class="messages-menu no-hover">
            <a class="no-hover font-size12px">
                <i class="fa fa-warning text-red font-size18px"></i>&nbsp;&nbsp;

                <u class="text-blue cursor-pointer"
                   onclick="location.href = '<?= admin_url("payment/subscribe") ?>';"><?= Translate::sprint("Update Payment") ?></u>
            </a>
        </li>

<?php endif; ?>


<?php endif; ?>







