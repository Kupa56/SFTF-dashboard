<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<li class="<?php if ($uri_parent == "application") echo "active"; ?>">
    <a href="<?= admin_url("setting/application") ?>"><i class="mdi mdi-cog-outline"></i>
        &nbsp;<span> <?= Translate::sprint("Global config") ?></span></a>
</li>

<li class="<?php if ($uri_parent == "api_config") echo "active"; ?>">
    <a href="<?= admin_url("setting/api_config") ?>"><i class="mdi mdi-database"></i>
        &nbsp;<span> <?= Translate::sprint("API Config") ?></span></a>
</li>

<li class="<?php if ($uri_parent == "currencies") echo "active"; ?>">
    <a href="<?= admin_url("setting/currencies") ?>"><i class="mdi mdi-currency-eur"></i>
        &nbsp;<span> <?= Translate::sprint("Currencies") ?></span></a>
</li>
<li class="<?php if ($uri_parent == "deeplinking") echo "active"; ?>">
    <a href="<?= admin_url("setting/deeplinking") ?>"><i class="mdi mdi-link"></i>
        &nbsp;<span> <?= Translate::sprint("Deep Linking") ?></span></a>
</li>

<li class="<?php if ($uri_parent == "cronjob") echo "active"; ?>">
    <a href="<?= admin_url("setting/cronjob") ?>"><i class="mdi mdi-refresh"></i>
        &nbsp;<span> <?= Translate::sprint("Cronjob") ?></span></a>
</li>

