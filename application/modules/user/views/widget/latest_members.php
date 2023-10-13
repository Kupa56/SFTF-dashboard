<?php

$params = array(
    "page" => 1,
    'limit' => 8,
    "is_super" => TRUE,
    "confirmed" => 0,
    "typeAuth" => "DeliveryBoy",
    "user_id" => $this->mUserBrowser->getData("id_user")
);


$users[Tags::RESULT] = $this->mUserModel->getUsers($params);
$users = $users[Tags::RESULT][Tags::RESULT];

//echo "<pre>";print_r($users); die();
?>

<div class="col-md-6">
</div>
<div class="col-md-6">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>

            <div class="box-tools pull-right">
                <span class="label label-danger">8 New Members</span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
        <?php if (!empty($users)) { ?>
                <ul class="users-list clearfix">
                <?php foreach ($users as $user) { ?>
                        <li>
                        <?php
                            $image = "";
                            if (isset($user['images'][0]['200_200']['url'])) {
                                $image = $user['images'][0]['200_200']['url'];
                            } else {
                                $image = adminAssets("images/profile_placeholder.png");
                            }

                            ?>
                            <img src="<?= $image ?>" alt="<?= $user["name"] ?>" width="70" height="70">
                            <span class="label label-info"><?= $user["typeAuth"] ?> </span>
                            <a class="users-list-name"
                               href="<?= admin_url("user/edit?id=" . $user['id_user']) ?>"><?= $user["name"] ?></a>
                            <span class="users-list-date">  <?= date('D', strtotime($user['dateLogin'])) ?>  </span>
                        </li>
                <?php } ?>

                </ul>
        <?php } ?>
            <!-- /.users-list -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer text-center">
            <a href="<?= admin_url("user/users") ?>" class="uppercase"><?= _lang("View All Users") ?></a>
        </div>
        <!-- /.box-footer -->
    </div>
</div>


