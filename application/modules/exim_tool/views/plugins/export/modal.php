<div class="modal fade" id="<?=$modal_id?>">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?=_lang("Export Data")?></h4>
			</div>
			<div class="modal-body form-unit">

            <?php $this->load->view(AdminPanel::TemplatePath."/include/messages");?>

				<div class="form-group">
					<div class="row">


						<div class="form-group col-sm-12" style="margin-top: 10px">
							<label><?=_lang("Columns")?></label><br>
							<table class="table">
								<tr>
									<td  id="cols-contianer"></td>
								</tr>
							</table>

						</div>
						<div class="form-group col-sm-12" style="margin-top: 10px">
							<label><?=_lang("Format")?></label>
							<select class="select2" id="select-export-<?=$modal_id?>">
								<option value="csv" selected>CSV</option>
								<option value="xml">XML</option>
								<option value="json">JSON</option>
							</select>
						</div>

						<div class="form-group col-sm-12">
							<p><i class="fas fa-exclamation-circle"></i>&nbsp;&nbsp;<?=Translate::sprintf("Tip: If would specify your result, you can use filter option %s before start exporting data",array(
									"<a href='#' id='open-filter-".$modal_id."'>"._lang("here")."</a>"
								))?></p>
						</div>


					</div>

				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><?=_lang("Cancel")?></button>
				<button type="button" class="btn btn-primary btn-flat" id="export-<?=$modal_id?>"><i class="loading hidden fas fa-cog fa-spin"></i>&nbsp;&nbsp;<span><?=_lang("Export")?></span>&nbsp;&nbsp;</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
