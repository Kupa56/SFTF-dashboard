<?php
$languages = Translate::getLangsCodes();
$langName = "";
foreach ($languages as $key => $lng) {
    if (Translate::getDefaultLang() == $key){

        if(!isMobile()){
            $langName = strtoupper($key) . "-" . $lng['name'];
        }else{
            $langName = strtoupper($key);
        }

    }

}
?>


<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
        <i class="mdi mdi-flag"></i> &nbsp; <?= $langName ?>
    </a>

    <ul class="dropdown-menu">
    <?php

        foreach ($languages as $key => $lng) {
            echo ' <li>
                    <ul class="menu">
                      <li>
                        <a href="' . site_url("setting/language") . "?lang=" . $key . '">' . strtoupper($key) . '-' . $lng['name'] . ' </a>
                      </li>
                    </ul>
                  </li>';
        }
        ?>

    </ul>

</li>

