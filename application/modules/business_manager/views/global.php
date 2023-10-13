<div id="app" class="framework7-root">



    <div id="home-page" class="views tabs">
        <!-- Main view -->

    <?php

        foreach ($views as $view){
            $data['view'] = $view;
            $this->load->view('business_manager/'.$view.'/list',$data);
        }

        $data['views'] = $views;
        $this->load->view("business_manager/toolbar",$data);
        ?>

    </div>

    <div id="loaded-components" class="page-content no-padding-top">
        <?=AdminTemplateManager::loadHTML()?>
    </div>

</div>

<?php


foreach ($views as $view){
    if (ModulesChecker::isEnabled($view)){
        $data['view'] = $view;
        $data['active_tab'] = $active_tab;
        $script = $this->load->view('business_manager/'.$view.'/scripts/list-script',NULL,TRUE);
        AdminTemplateManager::addScript($script);
    }
}

$script = $this->load->view('business_manager/global-script',NULL,TRUE);
AdminTemplateManager::addScript($script);




?>