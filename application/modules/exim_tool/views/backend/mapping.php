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

						<?php



						if($file_delimiter=="comma")
							$delimiter = ",";
						else
							$delimiter = ";";

						if(!in_array($file_encoding,Exim_tool::Encoding))
							$encoding = "UTF-8";


						$data['file_delimiter'] = $file_delimiter;
						$data['file_encoding'] = $file_encoding;


						$map = Exim_Importer::getImportedFields($module,$file['name'],$delimiter,$encoding);

						?>

						<div class="alert alert-success alert-dismissible">
							"<?=$file['file_name']?>" <?=_lang("uploaded successful!")?> & <?=Translate::sprintf("(%s) line(s) recorded",array($map['lines']))?>
						</div>

						<div class="row margin fields">
							<p><?=_lang("The best match to each field on the selected file have been auto-selected, You can adapt selected fields if needed.")?></p>
							<table class="table">

								<tr>
									<th width="20%"><?=_lang("Default Field")?></th>
									<th width="30%"><?=_lang("Imported Field Header")?></th>
									<th width="50%"></th>
								</tr>

								<?php foreach ($map['map'] as $key => $detected_field): ?>
									<tr>
										<td width="20%"><?=$key?></td>
										<td width="30%" class="field">
											<input type="hidden" class="default_field" value="<?=$key?>"/>
											<select class="select2 form-control imported_field">
												<option></option>
												<?php foreach ($map['fields'] as $k => $field): ?>
												<option value="<?=$field?>" <?=$field==$detected_field?"selected":""?>><?=_lang($field)?></option>
												<?php endforeach; ?>
											</select>
										</td>
										<td width="50%"></td>
									</tr>
								<?php endforeach; ?>

							</table>

						</div>

						<div class="errors-container">


						</div>
					</div>

					<div class="box-footer">
						<button id="btnApplyImport" type="button" class="btn btn-primary btn-flat pull-right">
							<i class="fas fa-check"></i>&nbsp;&nbsp;<?= _lang("Apply") ?>
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

$data['module'] = $module;
$data['file'] = $file['name'];

$script = $this->load->view("exim_tool/backend/mapping-script",$data,TRUE);
AdminTemplateManager::addScript($script);








