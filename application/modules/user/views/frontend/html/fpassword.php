
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

                    <div class="msgSuccess alert alert-success alert-dismissible hidden">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> <?=Translate::sprint("Success","")?> !</h4>

                        <?=Translate::sprint("We've sent new password to your mailbox","")?>
                    </div>



                    <div class="form-group has-feedback">
                        <input type="text" id="login" class="form-control" placeholder="<?=Translate::sprint("Enter your email","")?>">
                        <i class="mdi mdi-mail-ru form-control-feedback"></i>
                    </div>
                    <div class="row">

                        <div class="col-xs-12">
                            <button type="submit"  class="btn btn-primary btn-block btn-flat" id="sendPasswordBtn">
                                <?=Translate::sprint("Send password")?></button>
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
<?php $this->load->view('user/frontend/html/box-right') ?>

</div>


<?php

$script = $this->load->view('user/frontend/scripts/fpassword-script',NULL,TRUE);
AdminTemplateManager::addScript($script);
