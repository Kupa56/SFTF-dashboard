<div class="modal fade" id="<?=$modal_id?>">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<?php if(isset($modal_title) && $modal_title!=""): ?>
					<h4 class="modal-title"><?=Translate::sprintf("Export %s",array($modal_title))?></h4>
				<?php else: ?>
					<h4 class="modal-title"><?=Translate::sprintf("Export %s data",array(_lang($module)))?></h4>
				<?php endif; ?>

			</div>
			<div class="modal-body form-unit">

				<?=$this->template->messages()?>

				<div class="form-group">

					<div class="row">
						<div class="form-group col-sm-12" style="margin-top: 10px">
							<label><?=_lang("Columns")?></label><br>
							<table class="table" style="margin-bottom: 0px">
								<tr>
									<td  id="cols-contianer"><?=$cols_html?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6" style="margin-top: 10px">
							<input type="radio" id="all" name="export_request" value="all" checked="checked">&nbsp;&nbsp;
							<label for="all"><?=_lang("All lines")?></label><br>
							<input type="radio" id="specific" name="export_request" value="specific">&nbsp;&nbsp;
							<label for="specific"><?=_lang("Specific Period")?></label><br>

							<sub class="text-red"><?=_lang("Maximum 500 lines")?></sub>
						</div>
					</div>



					<div class="row period_form hidden">
						<div class="form-group col-sm-6">
							<label><?=_lang("From")?></label>
							<input type="text" class="form-control datepicker" id="date_from" name="date_a" value="" placeholder="yyyy-mm-dd">
						</div>

						<div class="form-group col-sm-6">
							<label><?=_lang("To")?></label>
							<input type="text" class="form-control datepicker" id="date_to" name="date_a" value="" placeholder="yyyy-mm-dd">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6" style="margin-top: 10px">
							<label><?=_lang("Format")?></label>
							<select class="select2 form-control" id="select-export-<?=$modal_id?>">
								<option value="csv" selected>CSV</option>
								<option value="xml">XML</option>
								<option value="json">JSON</option>
							</select>
						</div>
					</div>



				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><?=_lang("Cancel")?></button>
				<button type="button" class="btn btn-primary btn-flat" id="export2-<?=$modal_id?>"><i class="loading hidden fas fa-cog fa-spin"></i>&nbsp;&nbsp;<span><?=_lang("Export")?></span>&nbsp;&nbsp;</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
