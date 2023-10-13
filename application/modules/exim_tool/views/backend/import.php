<?php

$map = Exim_Importer::getMap($module);


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


	<?=$this->template->messages()?>

	<!-- Main content -->
	<section class="content">


		<!-- Main row -->
		<div class="row">
			<!-- Left col -->

			<div class="col-md-12">
				<!-- TABLE: LATEST ORDERS -->
				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<strong style="text-transform: uppercase"><?= _lang("Import") ?></strong>
					</div>
					<!-- /.box-header -->

					<div class="box-body ">

						<div class="row margin fields">
							<div class="col-md-12">
								<p><i class="fas fa-info-circle"></i>&nbsp;&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing
									elit. Donec facilisis arcu ac justo eleifend semper. Proin congue,
									mauris eget lobortis molestie, nisl urna scelerisque nulla, eget congue arcu elit eu arcu. <a target="_blank" href="<?=$map["example"]?>">Download Example</a></p>
							</div>
							<div class="col-md-6">
								<div class="form-group required">
									<?php

									$upload_plug = $this->uploader->plug_files_uploader(array(
										"limit_key"     => "importFile",
										"token_key"     => "impYjES-4555",
										"limit"         => 1,
										"types"         => array("text/csv"),
										"template_html"         => "template/".$this->template->getTemplateName()."/uploader_templates/html",
										"template_style"        => "template/".$this->template->getTemplateName()."/uploader_templates/style",
									));


									echo $upload_plug['html'];
									AdminTemplateManager::addScript($upload_plug['script']);

									?>
								</div>
								<div class="form-group required">
									<label><?=_lang("Character Encoding")?></label>
									<select class="select2 form-control" id="file_encoding">
										<option value="utf-8"><?=_lang("UTF-8")?></option>
									</select>
								</div>
								<div class="form-group required">
									<label><?=_lang("File Delimiter")?></label>
									<select class="select2 form-control" id="file_delimiter">
										<option value="comma"><?=_lang("Comma (,)")?></option>
										<option value="semicolon"><?=_lang("Semi-colon (;)")?></option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<p><i class="fas fa-info-circle"></i>&nbsp;&nbsp;Tips</p>
							</div>
						</div>
					</div>

					<div class="box-footer">
						<button id="continue" type="button" class="btn btn-primary btn-flat pull-right">
							<i class="fas fa-upload"></i>&nbsp;&nbsp;<?= _lang("Import") ?>
						</button>
					</div>

					<!-- /.box-footer -->
				</div>
			</div>

		</div>

		<!-- /.col -->
		<!-- /.row -->
	</section>
	<!-- /.content -->
	<!-- /.content-wrapper -->
</div>
<!-- /.content-wrapper -->

<?php

$data['uploader_files_variable'] = $upload_plug['var'];
$script = $this->load->view("exim_tool/backend/import-script",$data,TRUE);
AdminTemplateManager::addScript($script);








