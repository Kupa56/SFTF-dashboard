<div class="row store-service">
    <!-- text input -->
    <div class="col-sm-12 service-list">



        <h3 class="box-title">
        <?php if(isset($title)): ?>
                <b><?= ($title) ?></b>
        <?php else :?>
                <b><?= Translate::sprint("Product Services") ?></b>
        <?php endif; ?>
        </h3>

        <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
            <?=_lang('Services are grouped together, a group can have one choice or multiple choices, you can attach each service with a small description if necessary ')?>
        </sup>
        <br>




        <button type="button" class="btn  btn-default create-new-grp-service">
            <i class="mdi mdi-playlist-check"></i>
            <?=_lang("Create new services group")?>
        </button>


        <div class="clearfix"></div><br/>


        <div class="row">
            <div class="col-md-6">
                <div class="row" id="grp-service-container">

                <?php

                    $groups = $this->mService->laodServices($id);

                    foreach ($groups as $grp){
                        $data['grp'] = $grp;
                        $this->load->view('service/plug/options/group_row',$data);
                    }


                    ?>

                </div>
            </div>
        </div>



    </div>

</div>

<?php

$modal1 = $this->load->view("service/plug/modal-create-grp",NULL,TRUE);
$modal2 = $this->load->view("service/plug/modal-create-option",NULL,TRUE);
AdminTemplateManager::addHtml($modal1);
AdminTemplateManager::addHtml($modal2);
