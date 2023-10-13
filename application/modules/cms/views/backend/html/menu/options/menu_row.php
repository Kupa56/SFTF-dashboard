<div class="col-md-12 menu-<?=$menu['id']?> group" data-id="<?=$menu['id']?>">
    <table class="table">
        <thead>
        <tr class="bg-gray">
            <input type="hidden" class="menu-<?=$menu['id']?>-title" value="<?=$menu['title']?>" />

        <?php if(preg_match("#page::#",$menu['uri'])): ?>
            <?php

                    $page = explode("::",$menu['uri']);
                    $page = $this->mCMS->getPageBySlug($page[1]);

                ?>
                <input type="hidden" class="menu-<?=$menu['id']?>-option" value="1" />
                <input type="hidden" class="menu-<?=$menu['id']?>-page" value="<?=$page["id"]?>" />
        <?php else: ?>

            <?php

                    $page = explode("::",$menu['uri']);

                    if(isset($page[1]))
                        $value = $page[1];
                    else
                        $value = "#";

                ?>

                <input type="hidden" class="menu-<?=$menu['id']?>-option" value="2" />
                <input type="hidden" class="menu-<?=$menu['id']?>-ex_url" value="<?=$value?>" />

        <?php endif; ?>


            <th colspan="2"><i class="mdi mdi-menu cursor-pointer"></i>&nbsp;&nbsp;<?=$menu['title']?>&nbsp;&nbsp;
                <a href="#" data-id="<?=$menu['id']?>" class="pull-right add-sub-menu"><i class="mdi mdi-plus-box"></i> <?=_lang("Add sub menu")?></a>
                &nbsp;&nbsp;&nbsp;<a href="#" class="update-menu" data-id="<?=$menu['id']?>"><i class="mdi mdi-pencil text-red"></i>&nbsp;&nbsp;</a>
                &nbsp;<a href="#" data-id="<?=$menu['id']?>" class="remove-menu"><i class="mdi mdi-delete text-red"></i>&nbsp;&nbsp;</a>
            </th>
        </tr>
        </thead>

        <tbody>

    <?php

            if(!empty($menu['menus']))
            foreach ($menu['menus'] as $sub){
                $data['sub_menu'] = $sub;
                $this->load->view('cms/backend/html/menu/options/sub_menu_row',$data);
            }
        ?>

        </tbody>
    </table>
</div>
<div class="clearfix"></div>