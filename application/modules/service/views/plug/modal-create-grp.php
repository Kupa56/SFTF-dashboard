<div class="modal fade"
     id="modal-create-group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Create new Group")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Group")?></label>
                        <input type="text" class="form-control" id="label" placeholder="<?=_lang("Enter...")?>">
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Options type")?></label>
                        <select class="select2 pv-options-selector" id="option_type">
                        <?php foreach ($this->mService->type as $value): ?>
                                <option value="<?=$value?>"><?=_lang($value)?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                <button type="button" id="create"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Create") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade"
     id="modal-update-group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Update Group")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Group")?></label>
                        <input type="text" class="form-control" id="label" placeholder="<?=_lang("Enter...")?>">
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Options type")?></label>
                        <select class="select2 pv-options-selector" id="option_type">
                        <?php foreach ($this->mService->type as $value): ?>
                                <option value="<?=$value?>"><?=_lang($value)?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                <button type="button" id="update"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Update") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>