<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

?>

<?php if(GroupAccess::isGranted('user')): ?>
<li class="<?php if ($uri_parent == "media") echo "active"; ?>">
    <a href="<?= admin_url("uploader/media") ?>"><i class="mdi mdi-folder-multiple-image"></i>
        &nbsp;<span> <?= Translate::sprint("Media") ?></span></a>
</li>
<?php endif; ?>
