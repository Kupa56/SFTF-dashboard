<?php

$this->load->library('user_agent');

?>
<!-- Content Wrapper. Contains page content -->




    <div class="box-holder">
        <div class="box-left">
            <div class="login-box">

                <div class="login-logo">

                <?php

                    $logo = ImageManagerUtils::getImage(APP_LOGO);
                    if($logo!="")
                        echo '<img src="'.$logo.'"/>';
                    else
                        echo '<img src="'.adminAssets("images/logo.png").'"/>';

                    ?>
                </div>
                <!-- /.login-logo -->
                <div class="login-box-body">

                    <div class="alert alert-success">
                        <h4><i class="fa fa-check-circle"
                               aria-hidden="true"></i>&nbsp;&nbsp;<?= Translate::sprint("Activated successfully") ?></h4>
                        <p><?= Translate::sprint("Your account has been activated successfully, you can now use your account by taking advantage of all available access.") ?></p>
                    </div>

                <?php

                    $platform = $this->agent->platform();
                    $platform = strtolower($platform);

                    ?>

                <?php if ($this->agent->is_mobile() and $platform == "ios"): ?>
                        <a href="<?= custom_protocol_url('user/login', 'jobz_app') ?>" class="btn btn-primary btn-block ">
                            <?= Translate::sprint("Open the app") ?>
                        </a>
                <?php else: ?>
                        <a href="<?= site_url('user/login') ?>" class="btn btn-primary btn-block ">
                            <?= Translate::sprint("Login") ?>
                        </a>
                <?php endif; ?>
                </div>
                <!-- /.login-box-body -->
            </div>
        </div>
    <?php $this->load->view('user/frontend/html/box-right') ?>

    </div>


<?php

$script = $this->load->view('user/frontend/scripts/fpassword-script',NULL,TRUE);
AdminTemplateManager::addScript($script);
