<div class="modal fade"
     id="modal-create-option">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Create new Service")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Service title")?></label>
                        <input type="text" class="form-control" id="option_name" placeholder="<?=_lang("Enter...")?>">
                    </div>

                    <div class="form-group">
                        <label><?=_lang("Description")?></label>
                        <textarea  class="form-control"  id="option_description" placeholder="<?=_lang("Enter...")?>"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Price")?></label>
                        <input type="number" class="form-control"  id="option_price" placeholder="<?=_lang("Enter...")?>">
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                <button type="button" id="create"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Add") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade"
     id="modal-update-option">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Update service")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Service title")?></label>
                        <input type="text" class="form-control" id="option_name" placeholder="<?=_lang("Enter...")?>">
                    </div>

                    <div class="form-group">
                        <label><?=_lang("Description")?></label>
                        <textarea  class="form-control"  id="option_description" placeholder="<?=_lang("Enter...")?>"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Price")?></label>
                        <input type="number" class="form-control"  id="option_price" placeholder="<?=_lang("Enter...")?>">
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