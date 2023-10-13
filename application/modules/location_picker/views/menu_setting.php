<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<li class="<?php if ($uri_m == "location_picker") echo "active"; ?>">
    <a href="<?= admin_url("location_picker/config") ?>"><i class="mdi mdi-map-marker"></i>
        &nbsp;<span> <?= Translate::sprint("Google Maps Api") ?></span></a>
</li>