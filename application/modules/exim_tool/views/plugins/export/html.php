<div style="text-align: right">

	<?php if(!isset($import) OR (isset($import) && $import==TRUE)): ?>
		<?php if(Exim_Importer::isRegistered($module)):?>
			<a class="import-btn render-path-ajax" href="<?=admin_url("exim_tool/import?module=".$module)?>"><i class="fas fa-file-upload text-red"></i> <u><?=_lang("Import")?></u></a>
			&nbsp;&nbsp;/&nbsp;&nbsp;
		<?php endif; ?>
	<?php endif; ?>

	<?php if(!isset($export) OR (isset($export) && $export==TRUE)): ?>
		<a class="export-btn" id="export-<?=$unique_id?>" href="#"><i class="fas fa-file-download"></i> <u><?=_lang("Export")?></u></a>
	<?php endif; ?>

</div>

<?php

	$data['modal_id'] = "modal-exim-export-".$unique_id;

	AdminTemplateManager::addHtml(
		$this->load->view('exim_tool/plugins/export/modal',$data,TRUE)
	);

?>
<!-- /.modal -->


