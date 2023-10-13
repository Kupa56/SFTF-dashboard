<?php if($this->mUserBrowser->isShadowing()): ?>
    <div class="callout callout-success">
        <h4><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;
            <?=Translate::sprintf("You are using shadowing mode, Now you are connected as \"%s\"",array($this->mUserBrowser->getData('username')))?></h4>
        <p>
            <a href="<?=admin_url("user/close_shadowing")?>">=><?=Translate::sprint("End shadow session and return to your session.")?></a>
        </p>
    </div>

<?php endif;?>





<?php

$userIsValid = FALSE;

if($this->mUserBrowser->getData("confirmed")==1){
    $userIsValid = TRUE;
}

?>

<?php if(!$userIsValid and EMAIL_VERIFICATION): ?>
    <div class="callout callout-warning">
        <h4><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Account without verification","")?>!</h4>
        <p><?=Translate::sprint("We've sent mail verification to your mailbox")?>. </p>
        <p><?=_lang("You didn't receive the confirmation email? ")?></p>
        <a class="linkAccess" href="<?= admin_url("user/resendClientMail?id=" . SessionManager::getData("id_user")) ?>"><?=_lang("Re-send the email")?></a>
    </div>
<?php endif;?>


<?php

if(ModulesChecker::isEnabled("pack")){

    $this->load->model("pack/pack_model");
    $pack_id = $this->mUserBrowser->getData('pack_id');
    $typeAuth = $this->mUserBrowser->getData('typeAuth');

    if($pack_id>0 and $typeAuth!="admin") {

        $pack = $this->pack_model->getPack($pack_id);
        $expired_date = $this->mUserBrowser->getData('will_expired');
        $days = MyDateUtils::getDays($expired_date);

        if ($days <= 0 and $pack!=NULL) {

            ?>

            <div class="callout callout-warning">
                <h4><?= Translate::sprint("Your pack \"" . $pack->name . "\" has been expired!") ?>!</h4>
                <p>
                <?php if ($pack->price > 0): ?>
                        <a href="<?= admin_url("pack/renew") ?>">=><?= Translate::sprint("Renew your account") ?></a>
                        <br>
                <?php endif; ?>
                <?php if ($this->mPack->canUpgrade()): ?>
                        <a href="<?= site_url("pack/pickpack?req=upgrade") ?>">=><?= Translate::sprint("Upgrade it") ?></a>
                <?php endif; ?>
                </p>
            </div>

        <?php

        }


    }
}


?>
