<div class="modal fade" id="modal-order-multi-language">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b><?=_lang("Customize in all languages")?></b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">


                    <?php foreach (Translate::getLangsCodes() as $key => $lng):?>
                        <?php
                            $langName = strtoupper($key) . "-" . $lng['name'];
                            ?>
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control order-button order-button-<?=$key?>" lang-data="<?=$key?>" type="text"  placeholder="<?=_lang("Enter...")?>">
                                <div class="input-group-addon"><?=$langName?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel","Cancel")?></button>
                <button type="button" id="apply_confirm" class="btn btn-flat btn-primary" data-dismiss="modal"><?=Translate::sprint("Apply")?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

