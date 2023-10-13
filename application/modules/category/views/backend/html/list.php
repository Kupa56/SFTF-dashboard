<?php

$categories = $data['cats'];


foreach ($categories as $key => $job){

    $categories[$key]['name'] = Text::output($categories[$key]['name']);
    $categories[$key]['name'] = Translate::sprint($categories[$key]['name'],$categories[$key]['name']);

}

usort($categories,function($first, $second){
    return strtolower($first['_order']) > strtolower($second['_order']);
});


?>
<div class="col-sm-6">
    <div class="box  box-solid">
        <div class="box-header">
            <div class="box-title"><b><?= Translate::sprint("Categories") ?></b></div>
        </div>
        <!-- /.box-header -->
        <div class="box-body  table-bordered ">
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th width="5%"></th>
                        <th width="10%"><?= Translate::sprint("Image") ?></th>
                        <th width="10%"><?= Translate::sprint("Icon") ?></th>
                        <th><?= Translate::sprint("Category") ?></th>
                        <th><?= Translate::sprint("Stores") ?></th>

                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="dd">



                <?php if (!empty($categories)) { ?>

                    <?php foreach ($categories AS $category) { ?>
                            <tr class="first_line line line_<?=$category['id_category']?>" data-id="<?=$category['id_category']?>">
                                <td><span class="cursor-pointer" style="font-size: 22px"><i class="mdi mdi-menu text-gray"></i></span></td>
                                <td align="right">
                                <?php

                                    if (isset($category["image"])) {
                                        $images = _openDir($category["image"]);
                                        if (isset($images['200_200']['url'])) {
                                            echo '<img style="    height: 45px;width: 45px;    border: 1px solid #eeeeee;
                                                    padding: 2px;" src="' . $images['200_200']['url'] . '"/>';
                                        }else{
                                            echo '<img style="    height: 45px;width: 45px;    border: 1px solid #eeeeee;
                                                    padding: 2px;" src="' . adminAssets("images/def_logo.png") . '"/>';
                                        }

                                    }
                                    ?>
                                </td>
                                <td align="right">
                                <?php

                                    if (isset($category["icon"])) {
                                        $icon = _openDir($category["icon"]);
                                        if (isset($icon['200_200']['url'])) {
                                            echo '<img style="    height: 45px;width: 45px;    border: 1px solid #eeeeee;background-color:#eeeeee;
                                                    padding: 2px;" src="' . $icon['200_200']['url'] . '"/>';
                                        }

                                    }
                                    ?>
                                </td>
                                <td>
                                    <span
                                            style="font-size: 12px"><?= Translate::sprint(Text::echo_output($category["name"])) ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 12px"><?= Text::output($category["nbrStore"]) ?></span>
                                </td>

                                <td align="right">
                                <?php if (GroupAccess::isGranted('category', DELETE_CATEGORY)): ?>
                                        <a href="#" class="delete" data-id="<?= $category["id_category"]?>">
                                            <button type="button" title="Delete" class="btn btn-sm"><span
                                                        class="glyphicon glyphicon-trash"></span></button>
                                        </a>
                                <?php endif; ?>
                                <?php if (GroupAccess::isGranted('category', EDIT_CATEGORY)): ?>
                                        <a href="<?= admin_url("category/edit?id=" . $category["id_category"]) ?>">
                                            <button type="button" title="Update" class="btn btn-sm"><span
                                                        class="glyphicon glyphicon-edit"></span></button>
                                        </a>
                                <?php endif; ?>
                                </td>
                            </tr>
                    <?php } ?>
                <?php } else { ?>
                        <tr>
                            <td colspan="4">
                                <div style="text-align: center"> <?= Translate::sprint("No data found", "") ?> !!</div>
                            </td>
                        </tr>

                <?php } ?>
                    </tbody>
                    <!-- <tfoot>
                     <tr>
                       <th>Rendering engine</th>
                       <th>Browser</th>
                       <th>Platform(s)</th>
                       <th>Engine version</th>
                       <th>CSS grade</th>
                     </tr>
                     </tfoot>-->
                </table>
            </div>

            <div class="row">
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                    </div>

                </div>

            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->


    <!-- /.box -->
</div>
<?php


$script = $this->load->view('category/backend/html/scripts/list-script',NULL,TRUE);
AdminTemplateManager::addScript($script);


