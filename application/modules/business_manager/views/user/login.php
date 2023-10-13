


    <div id="app" class="framework7-root">
        <div id="login-business" class="page-content no-padding-top" data-item="">
            <!--<a href="#" class="link back close-button">
                <i class="mdi mdi-close"></i>
            </a>-->
            <div class="block">
                <div class="form-container">

                    <div class="list no-hairlines custom-form">

                        <h1 class="create-title"><?=_lang("Login")?></h1>

                        <ul>
                            <li class="item-content item-input">
                                <i class="mdi mdi-account"></i>&nbsp;&nbsp;
                                <div class="item-inner">
                                    <div class="item-input-wrap">
                                        <input type="text" id="email" placeholder="<?=_lang("Username or Email")?>" class="" value="<?=$session?>">
                                        <span class="input-clear-button"></span>
                                    </div>
                                </div>
                            </li>

                            <li class="item-content item-input">
                                <i class="mdi mdi-key"></i>&nbsp;&nbsp;
                                <div class="item-inner">
                                    <div class="item-input-wrap">
                                        <input type="password" id="password" placeholder="<?=_lang("Password")?>" class="">
                                        <span class="input-clear-button"></span>
                                    </div>
                                </div>
                            </li>
                        </ul>



                        <div class="row">
                            <div class="col-100">
                                <a class="big-button button button-fill link" id="do-login"> <i
                                        class="mdi mdi-check"></i>&nbsp;<?= _lang("Login") ?></a>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-100" style="padding: 10px">
                                <a class="link" href="<?= site_url("user/signup") ?>"> <i
                                            class="mdi mdi-check"></i>&nbsp;<?= _lang("You don't have an account?") ?></a>
                            </div>
                        </div>



                    </div>

                </div>
            </div>
        </div>
    </div>



<?php

$script = $this->load->view('business_manager/user/scripts/login-script',NULL,TRUE);
AdminTemplateManager::addScript($script);


?>