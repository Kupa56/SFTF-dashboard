<?php if (GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)) { ?>
<?php

    $this->load->model("campaign/campaign_model");
    $nbrCampaigns = 0;
    $nbrCampaigns = $this->campaign_model->getPendingCampaigns();

    if ($nbrCampaigns > 0) {
        ?>
        <li class=" messages-menu">
            <a href="<?= admin_url("campaign/campaigns?status=-1") ?>">
                <i class="fa fa-paper-plane"></i>
                <span class="label label-warning"><?= $nbrCampaigns ?></span>
            </a>
        </li>
<?php } ?>

<?php } ?>
