<div class="modal fade" id="<?=$modal_id?>">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<?php if(isset($modal_title) && $modal_title!=""): ?>
					<h4 class="modal-title"><?=Translate::sprintf("Import %s",array($modal_title))?></h4>
				<?php else: ?>
					<h4 class="modal-title"><?=Translate::sprintf("Import %s data",array(_lang($module)))?></h4>
				<?php endif; ?>

			</div>
			<div class="modal-body form-unit">

				<?=$this->template->messages()?>

				<div class="form-group">




				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><?=_lang("Cancel")?></button>
				<button type="button" class="btn btn-primary btn-flat" id="export2-<?=$modal_id?>"><i class="loading hidden fas fa-cog fa-spin"></i>&nbsp;&nbsp;<span><?=_lang("Import")?></span>&nbsp;&nbsp;</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
