
<div class="modal fade" id="update-alert-modal" style="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b><?=_lang("New update!")?></b></h4>
            </div>
            <div class="modal-body">
                <p class="text-red message">
                    <strong><?=Translate::sprint("New update available")?> (<?=APP_VERSION?>)</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=Translate::sprint("Cancel")?></button>
                <button type="button" id="updateNow01SNS" class="btn btn-flat btn-primary"><?=Translate::sprint("Update now")?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
    <script>

        $("#update-alert-modal").modal('show');
        $("#updateNow01SNS").on('click',function () {
            $("#update-alert-modal").modal('hide');
            document.location.href = "<?=base_url("update?id=".CRYPTO_KEY)?>";
            return false;
        });

    </script>