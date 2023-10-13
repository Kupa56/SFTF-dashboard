<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<li class="<?php if ($uri_parent == "mailConfig") echo "active"; ?>">
    <a href="<?= admin_url("simple_mailer/mailConfig") ?>"><i class="mdi mdi-email"></i>
        &nbsp;<span> <?= Translate::sprint("Mail config") ?></span></a>
</li>
