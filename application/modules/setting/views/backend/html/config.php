<?php

//$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
//$languages = Translate::getLangsCodes();
$tabCounterMax = 0;
$tabcounter=1;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper ">

    <section class="content">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
            </div>

        </div>

        <div class="row application-setting">
            <div class="col-sm-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">

                    <?php foreach ($components as $id => $component): ?>

                        <?php foreach ($component['blocks'] as $block): ?>
                        
                                <li class="<?= (isset($block['config']['active']) ? 'active' : '') ?>">
                                    <a href="#<?= $id ?>-<?= $component['module'] ?>" class="title uppercase"
                                       data-toggle="tab"
                                       aria-expanded="false"><?= $block['config']['title'] ?></a>
                                </li>
                        
                        <?php endforeach; ?>

                    <?php endforeach; ?>


                    </ul>

                    <!-- loop modules --->

                    <div class="tab-content">

                    <?php $tabcounter=1; ?>
                    <?php foreach ($components as $id => $component): ?>

                        <?php foreach ($component['blocks'] as $block): ?>
                        
                                <div class="tab-pane <?= (isset($block['config']['active']) ? 'active' : '') ?>"
                                     id="<?= $id ?>-<?= $component['module'] ?>">

                                <?php
                                    if( $tabcounter > $tabCounterMax){
                                    $conf["config"] = $config;
                                     $this->load->view($block['path'], $conf);
                                    }
                                    $tabcounter++;
                                    ?>
                                </div>
                        

                        <?php endforeach; ?>

                    <?php endforeach; ?>


                    </div>

                </div>
            </div>
        </div>


    </section>

</div>


<?php

$data['config'] = $config;
$script = $this->load->view('setting/backend/html/scripts/config-script', FALSE, TRUE);
AdminTemplateManager::addScript($script);

?>






