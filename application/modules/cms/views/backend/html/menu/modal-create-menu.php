<div class="modal fade"
     id="modal-create-group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><?=_lang("Create new Menu")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Title")?></label>
                        <input type="text" class="form-control" id="title" placeholder="<?=_lang("Enter...")?>">
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Link")?></label>
                        <select class="select2 pv-options-selector">
                            <option value="0"><?=_lang("Select")?></option>
                            <option value="1"><?=_lang("Page")?></option>
                            <option value="2"><?=_lang("External url")?></option>
                        </select>
                    </div>

                    <div class="form-group pv-selector page-selector hidden">
                        <label><?=_lang("Page")?></label>
                        <select class="select2 pv-page-selector" id="page-id">
                            <option value="1"><?=_lang("Select Page")?></option>
                        <?php foreach ($pages as $page): ?>
                                <option value="<?=$page['id']?>"><?=$page['title']?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group pv-selector url-editor hidden">
                        <label><?=_lang("Url")?></label>
                        <input type="text" class="form-control ex_url" placeholder="<?=_lang("Enter...")?>">
                    </div>

                    <input type="hidden" id="parent_id" value="0" />

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
                <h4 class="modal-title"><b><?=_lang("Update Menu")?></b></h4>
            </div>
            <div class="modal-body">

                <div class="row margin">

                    <div class="form-group">
                        <label><?=_lang("Title")?></label>
                        <input type="text" class="form-control" id="title" placeholder="<?=_lang("Enter...")?>">
                    </div>
                    <div class="form-group">
                        <label><?=_lang("Link")?></label>
                        <select class="select2 pv-options-selector">
                            <option value="0"><?=_lang("Select")?></option>
                            <option value="1"><?=_lang("Page")?></option>
                            <option value="2"><?=_lang("External url")?></option>
                        </select>
                    </div>

                    <div class="form-group pv-selector page-selector hidden">
                        <label><?=_lang("Page")?></label>
                        <select class="select2 pv-page-selector" id="page-id">
                            <option value="1"><?=_lang("Select Page")?></option>
                        <?php foreach ($pages as $page): ?>
                                <option value="<?=$page['id']?>"><?=$page['title']?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group pv-selector url-editor hidden">
                        <label><?=_lang("Url")?></label>
                        <input type="text" class="form-control ex_url" placeholder="<?=_lang("Enter...")?>">
                    </div>

                    <input type="hidden" id="parent_id" class="parent_id" value="0" />

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
