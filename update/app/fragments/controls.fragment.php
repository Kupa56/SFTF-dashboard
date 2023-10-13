<?php

    $purchase_id = "";
    $platform = "";



?>


        <form action="<?=APPURL."/update/update.php"?>" id="controls" class="step">

            <div id="welcome">
                <h1 class="title">Update - <?=INSTALL_PROJECT_NAME?></h1>
                <p>Enter your informations </p>
            </div>


            <div class="form-errors color-red">
                
            </div>

            <div class="inner-wrapper">
                <div class="subsection">
                    <div class="section-title">Licenses</div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Purchase Code </label>
                            <div class="input-tip">
                                Please include your purchase code (<?=INSTALL_PROJECT_ID?>).
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input required" name="pid"  value="<?=$purchase_id?>">
                        </div>
                    </div>

                </div>
            </div>


            <div class="inner-wrapper">
                <div class="subsection">
                    <div class="section-title">Versions:</div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Upgrade to</label>
                            <div class="input-tip">
                                Your app will upgrade to
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input required" name="key-ios" value="<?=APP_VERSION?>" disabled="disabled">
                        </div>
                    </div>


                </div>
            </div>



                <div class="gotonext mt-40">
                    <div class="clearfix">
                        <div class="col s12 m6 offset-m3 m-last l4 offset-l4 l-last">
                            <input type="submit" value="Update it now" class=" fluid button">
                        </div>
                    </div>
                </div>
        </form>