
<div class="box-holder">
    <div class="box-left">
        <div class="login-box">

            <div class="login-logo">

            <?php

                $logo = ImageManagerUtils::getImage(APP_LOGO);
                if($logo!="")
                    echo '<img src="'.$logo.'"/>';
                else
                    echo '<img src="'.adminAssets("images/splash_backgound.png").'"/>';

                ?>
            </div>

            <!-- /.login-logo -->
            <div class="login-box-body">



                <div class="login-notes">

                    <section class="message-success hidden margin-bottom10px">
                        <div class="alert alert-success alert-dismissible margin-bottom0px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p><?=_lang("Success!")?></p>
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

                    <div class="form-group has-feedback">
                        <i class="mdi mdi-account form-control-feedback"></i>
                        <input type="text" id="name" class="form-control" placeholder="<?= Translate::sprint("Full name") ?>"
                               value="">
                    </div>
                    <div class="form-group has-feedback">
                        <i class="mdi mdi-mail-ru form-control-feedback"></i>
                        <input type="email" id="email" class="form-control" placeholder="<?= Translate::sprint("Email", "Email") ?>"
                               value="">
                    </div>
                    
                    <div class="form-group has-feedback">
                        <i class="mdi mdi-mail-ru form-control-feedback"></i>
                        <input type="text" id="phone" class="form-control" placeholder="<?= Translate::sprint("Phone", "Phone") ?>"
                               value="">
                    </div>
                    
                    <!--<div class="form-group">-->
                    <!--                            <label><?= Translate::sprint("Phone") ?> :</label>-->
                    <!--                            <input type="text" class="form-control" id="phone" placeholder="Enter ..."-->
                    <!--                                   value="<?= textClear($user->telephone) ?>">-->
                    <!--                        </div>-->
                    <div class="form-group has-feedback">
                        <i class="mdi mdi-account-key form-control-feedback"></i>
                        <input type="text" id="username" class="form-control"
                               placeholder="<?= Translate::sprint("Username", "Username") ?>" value="">
                    </div>
                    <div class="form-group has-feedback">
                        <i class="mdi mdi-key form-control-feedback"></i>
                        <input type="password" id="password" class="form-control"
                               placeholder="<?= Translate::sprint("Password", "Password") ?>" value="">
                    </div>


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

                    <div class="row">

                        <div class="col-xs-12">
                            <button type="submit"
                                    class="btn btn-primary btn-block btn-flat" id="signUpBtn"><?= Translate::sprint("signup", "Sign up") ?></button>
                        </div><!-- /.col -->

                        <div class="col-xs-12">
                            <div class="checkbox icheck">
                                <label>
                                    <a href="<?= site_url("user/login") ?>"><?= Translate::sprint("have_already_account", "Have already account ?") ?></a>
                                </label>
                            </div>
                        </div><!-- /.col -->

                    </div>
                </form>


            </div>
            <!-- /.login-box-body -->
        </div>
    </div>
<?php $this->load->view('user/frontend/html/box-right') ?>

</div>


<?php

$script = $this->load->view('user/frontend/scripts/signup-script',NULL,TRUE);
AdminTemplateManager::addScript($script);
