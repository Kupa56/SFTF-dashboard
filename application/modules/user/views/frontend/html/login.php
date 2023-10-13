<div class="box-holder">
    <div class="box-left">
        <div class="login-box">

            <div class="login-logo">

            <?php

                $logo = ImageManagerUtils::getImage(APP_LOGO);

                if ($logo != "")
                    echo '<img src="' . $logo . '"/>';
                else
                    echo '<img src="' . adminAssets("images/splash_backgound.png") . '"/>';


                ?>
            </div>

            <!-- /.login-logo -->
            <div class="login-box-body">


                <p class="login-box-msg">
                    <?= _lang("Login to start your session") ?>
                </p>

                <div class="login-notes">

                    <section class="message-success hidden margin-bottom10px">
                        <div class="alert alert-success alert-dismissible margin-bottom0px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p><?= _lang("Success!") ?></p>
                        </div>
                    </section>

                    <section class="message-error hidden margin-bottom10px">
                        <div class="alert alert-danger alert-dismissible margin-bottom0px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p class="messages"></p>
                        </div>
                    </section>
                </div>

                <form action="#" method="post">

                <?php
                    $languages = Translate::getLangsCodes();
                    $default_language = Translate::getDefaultLang();
                    ?>

                    <div class="form-group ">
                        <select class="select2" id="default-language">
                        <?php foreach ($languages as $key => $lng): ?>
                                <option value="<?= $key ?>" <?php if ($key == $default_language) echo 'selected' ?>><?= strtoupper($key) . ' - ' . $lng['name'] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="login" id="login"
                               placeholder="<?= _lang("Login or email") ?>">
                        <span class="form-control-feedback">
							<i class="mdi mdi-at"></i>
						</span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" name="password" id="password"
                               placeholder="<?= _lang("Password") ?>">
                        <span class=" form-control-feedback">
							<i class="mdi mdi-lock-outline"></i>
						</span>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat"
                                    id="btnLogin"><?= _lang("Login") ?></button>
                        </div>
                        <div class="col-xs-12">
                            <div class="checkbox icheck">
                                <label>
                                    <a href="<?= site_url("user/fpassword") ?>"><?= _lang("Forgot password ?") ?></a>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>

                <br><br>

            <?php if (USER_REGISTRATION): ?>
                    <p><?= _lang("Don't have an account yet?") ?>
                        <a class="" href="<?= site_url("user/signup") ?>"><?= _lang("Sign up") ?></a>
                    </p>
            <?php endif; ?>


            </div>
            <!-- /.login-box-body -->
        </div>
    </div>

<?php $this->load->view('user/frontend/html/box-right') ?>

</div>


<?php

$script = $this->load->view('user/frontend/scripts/login-script', NULL, TRUE);
AdminTemplateManager::addScript($script);
