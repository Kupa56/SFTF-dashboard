
<div class="box-holder">
    <div class="box-center">
        <div class="login-box">

            <div class="login-logo">

            <?php

                $logo = ImageManagerUtils::getImage(APP_LOGO);
                if($logo!="")
                    echo '<img src="'.$logo.'"/>';
                else
                    // echo '<img src="'.adminAssets("images/splash_backgound.png").'"/>';
                    echo '<img src="' . adminAssets("images/splash_backgound.png") . '" style="width: 192px;"/>';

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
                        <input type="password" id="password" class="form-control" placeholder="<?=_lang("New password")?>">
                        <i class="mdi mdi-key  form-control-feedback"></i>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="confirm" class="form-control" placeholder="<?=_lang("Confirm")?>">
                        <i class="mdi mdi-check form-control-feedback"></i>
                    </div>


                    <div class="row">

                        <div class="col-xs-12">
                            <button type="submit"  class="btn btn-primary btn-block btn-flat" id="savePasswordBtn">
                                <?=Translate::sprint("Save password")?></button>
                        </div><!-- /.col -->


                        <div class="col-xs-12">
                            <div class="checkbox icheck">
                                <label>
                                    <a href="<?=site_url("user/login")?>"><?=Translate::sprint("Log In")?></a>
                                </label>
                            </div>
                        </div><!-- /.col -->
                    </div>


                </form>

            </div>
            <!-- /.login-box-body -->
        </div>
    </div>
<?php 
// $this->load->view('user/frontend/html/box-right') 
?>

</div>


<?php

$script = $this->load->view('user/frontend/scripts/rpassword-script',NULL,TRUE);
AdminTemplateManager::addScript($script);
