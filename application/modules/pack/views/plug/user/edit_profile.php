<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title"><strong><?= Translate::sprint("Subscription Pack") ?></strong>
        </h3>
    </div>

    <div class="box-body">

        <div class="form-group margin">

        <?php if ($this->mUserBrowser->getData("manager") == 0): ?>

            <?php

                $this->load->model("pack/pack_model");
                $pack = $this->pack_model->getAccountPack();


                if ($pack != NULL) {

                    echo '<label>' . Translate::sprint("Pack name") . '</label>';
                    echo "<br><span class='badge bg-yellow'>" . $pack->name . "</span>";

                    if ($this->mPack->canUpgrade()) {
                        echo "&nbsp;&nbsp;-&nbsp;&nbsp;";
                        echo "<u><a href='" . site_url("pack/pickpack?req=upgrade") . "'>" . Translate::sprint("Upgrade") . "</a></u>";
                    }


                    if (!$this->mPack->isRenewal()) {

                        $expired_date = $this->mUserBrowser->getData('will_expired');
                        $days = MyDateUtils::getDays($expired_date);

                        if ($days < 7) {
                            echo Translate::sprintf("Your pack will be expired soon after %s days", array($days));
                            echo '&nbsp;<a href="' . admin_url("pack/renew") . '">' . Translate::sprint("Renew") . '</a>';
                        } else {
                            echo "&nbsp;&nbsp;" . Translate::sprintf("Will be expired at ( %s )", array(
                                    MyDateUtils::convert($user->will_expired, "UTC", TimeZoneManager::getTimeZone(), "d, M Y")
                                ));
                        }

                    } else {
                        echo "<strong class='text-red'>" . Translate::sprint("Your account was expired") . "</strong>&nbsp;&nbsp;&nbsp;";
                        echo "<strong class='text-red'><u><a href='" . admin_url("pack/renew") . "'>" . Translate::sprint("Renew now") . "</a></u></strong>";
                    }

                } else {

                    echo '<label><i class="text-orange mdi mdi-alert"></i>&nbsp;&nbsp;' . Translate::sprint("Don't have pack") . '</label>';
                    echo '<br><a href="' . site_url("pack/pickpack") . '"><u>' . Translate::sprint("Select a pack") . '</u></a>';

                }

                ?>


        <?php elseif ($this->mUserBrowser->getData("manager") == 1): ?>

                <label><?= Translate::sprint("Pack name") ?></label>:

            <?php

                $this->load->model("pack/pack_model");
                $packs = $this->pack_model->getPacks();

                echo '<br><select class="select2 select_pack" id="select_pack">';
                echo '<option value="0">' . Translate::sprint("Select pack") . '</option>';
                foreach ($packs as $value) {

                    if ($value->id == $user->pack_id)
                        echo '<option value="' . $value->id . '" selected>' . $value->name . '</option>';
                    else
                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                }
                echo '</select>';

                ?>


                <br><br>

                <div class="form-group">
                    <strong>
                    <?php
                        echo Translate::sprintf("The subscription will be expired at ( %s )", array(
                            MyDateUtils::convert($user->will_expired, "UTC", TimeZoneManager::getTimeZone(), "d, M Y H:i:s")
                        ));
                        ?>
                    </strong>
                </div>


        <?php endif; ?>

        </div>


    </div>

</div>


<?php

$script = $this->load->view('pack/plug/user/edit-profile-script',array("user"=>$user),TRUE);
AdminTemplateManager::addScript($script);


