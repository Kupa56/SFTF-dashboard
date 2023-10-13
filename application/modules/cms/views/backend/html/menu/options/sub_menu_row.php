<tr class="menu-<?=$sub_menu['id']?> menu sub-menu" data-id="<?=$sub_menu['id']?>">
    <td><i class="mdi mdi-menu text-gray cursor-pointer"></i>&nbsp;&nbsp;<span><?=$sub_menu['title']?></span> <i class="mdi mdi-link"></i> <?=$sub_menu['uri']?></td>
    <td align="right">

        <input type="hidden" class="menu-<?=$sub_menu['id']?>-title" value="<?=$sub_menu['title']?>" />
        <input type="hidden" class="menu-<?=$sub_menu['id']?>-parent_id" value="<?=$sub_menu['parent_id']?>" />


    <?php if(preg_match("#page::#",$sub_menu['uri'])): ?>
        <?php

            $page = explode("::",$sub_menu['uri']);
            $page = $this->mCMS->getPageBySlug($page[1]);

            ?>
            <input type="hidden" class="menu-<?=$sub_menu['id']?>-option" value="1" />
            <input type="hidden" class="menu-<?=$sub_menu['id']?>-page" value="<?=$page["id"]?>" />
    <?php else: ?>

        <?php
            $page = explode("::",$sub_menu['uri']);
            ?>

            <input type="hidden" class="menu-<?=$sub_menu['id']?>-option" value="2" />
            <input type="hidden" class="menu-<?=$sub_menu['id']?>-ex_url" value="<?=$page[1]?>" />

    <?php endif; ?>


        <a href="#" class="update-menu" data-id="<?=$sub_menu['id']?>"><i class="mdi mdi-pencil text-red"></i>&nbsp;&nbsp;</a>
        &nbsp;&nbsp;&nbsp;<a href="#" class="remove-sub-menu" data-id="<?=$sub_menu['id']?>"><i class="mdi mdi-delete text-red"></i>&nbsp;&nbsp;</a>
    </td>
</tr>