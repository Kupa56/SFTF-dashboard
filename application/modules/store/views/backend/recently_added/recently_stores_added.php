<?php

$user_id = 0;

if(!GroupAccess::isGranted("store",MANAGE_STORES))
    $user_id = $this->mUserBrowser->getData("id_user");

$stores = $this->mStoreModel->recentlyAdd($user_id);
$stores = $stores['stores'];

?>
<div class=" col-md-6">
<?php if (!empty($stores)) { ?>

        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><b><?= Translate::sprint("Recently_Added", "") ?> </b></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <ul class="products-list product-list-in-box">

                <?php foreach ($stores AS $store) { ?>
                        <li class="item">
                            <div class="product-img">

                                <img src="<?=ImageManagerUtils::getFirstImage($store['images'],ImageManagerUtils::IMAGE_SIZE_200)?>"/>

                            </div>
                            <div class="product-info">
                                <a href="<?= admin_url("store/edit?id=" . $store['id_store']) ?>"
                                   class="product-title"><?= Text::echo_output($store['name']) ?>
                                    <span class="badge bg-green pull-right" style="color:<?=$store['category_color']?>"><?= (ucfirst(Text::echo_output($store['category_name']))) ?></span></a>
                                <span class="product-description">
                                                      <?= Text::echo_output($store['address']) ?>
                                                    </span>
                            </div>
                        </li>
                <?php } ?>
                    <!-- /.item -->
                </ul>
            </div>
            <!-- /.box-body -->
        <?php if (count($stores) > 4) { ?>
                <div class="box-footer text-center">
                    <a href="<?= admin_url("store/all_stores") ?>"
                       class="uppercase"><?= Translate::sprint("view more") ?> </a>
                </div>
        <?php } ?>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->

<?php } ?>
</div>

