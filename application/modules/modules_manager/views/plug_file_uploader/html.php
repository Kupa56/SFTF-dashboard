<?php

    $tag_id = "";

    if (isset($tag)) {
        $tag_id = "-" . $tag;
    }


?>

<div id="dt_uploader">

    <label class="nsup-fileuploadlabel nsup-file-upload<?= $tag_id ?>" for="nsup-photogallery">
         <span id="fileuploadbtn<?= $tag_id ?>" class="nsup-btn cursor-pointer"><strong><?= Translate::sprint("Select File") ?></strong></span>
        <span class="desc"><?= Translate::sprintf("Maximum upload file size is %s", array(MAX_FILE_UPLOAD." MB")) ?></span>
    <?php if(isset($types)): ?>
            <span class="desc supported_ext"><?= Translate::sprintf("the authorized file format (%s) ", array(implode(",",$types))) ?></span>
    <?php endif; ?>
        <input id="fileuploadinput<?= $tag_id ?>" class="nsup-fileinput" type="file"
               name="file">
    </label>
    <div class="clearfix"></div>
    <div id="progress<?= $tag_id ?>" class="progress hidden">
        <div class="percent" style="width: 0%"></div>
    </div>
<?php if (!isset($cache)): ?>
        <div id="file-previews<?=$tag_id?>" class="file-previews hidden">

        </div>
<?php else: ?>
        <div id="file-previews<?= $tag_id ?>" class="file-previews">

        <?php if (!empty($cache)) { ?>

            <?php foreach ($cache as $key => $value) { ?>

                <?php


                    if(!isset($value['name']))
                        continue;

                    $name = $value['name'];
                    $item = "item_" . $name;

                    $filesData = $value;

                    ?>

                    <div data-id="<?= $name ?>" class="file-uploaded cursor-draggable file-uploaded<?= $tag_id ?>  item_<?= $name ?>">
                        <a id="file-preview">
                            <img src="<?= $filesData['200_200']['url'] ?>" alt="">
                        </a>
                        <div class="clear"></div>
                    <?php if(isset($undeletable) and $undeletable==TRUE): ?>
                            <a id="undeletable"><i
                                        class="fa fa-trash"></i>&nbsp;&nbsp;<?= Translate::sprint("Delete", "") ?>
                            </a>
                    <?php else: ?>
                            <a href="#" data="<?= $name ?>" id="delete"><i
                                        class="fa fa-trash"></i>&nbsp;&nbsp;<?= Translate::sprint("Delete", "") ?>
                            </a>
                    <?php endif; ?>

                    </div>


            <?php } ?>
        <?php } ?>
        </div>
<?php endif; ?>


</div>


