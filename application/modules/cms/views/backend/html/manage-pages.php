
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- Message Error -->
                <div class="col-sm-12">
                <?php $this->load->view(AdminPanel::TemplatePath."/include/messages"); ?>
                </div>

            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-solid">
                        <div class="box-header">
                            <div class="box-title" style="width : 100%;">
                                <div class="row">
                                    <div class="pull-left col-md-8">
                                        <b><?= Translate::sprint("Pages") ?></b>
                                    </div>
                                    <div class="pull-right col-md-4">
                                        <a href="<?= admin_url("cms/addPage") ?>" class="btn btn-primary btn-sm pull-right">
                                            <span
                                                class="glyphicon glyphicon-plus"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="list" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><?= Translate::sprint("ID") ?></th>
                                        <th><?= Translate::sprint("Title") ?></th>
                                        <th><?= Translate::sprint("Slug") ?></th>
                                        <th><?= Translate::sprint("Template") ?></th>
                                        <th><?= Translate::sprint("Status") ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php foreach ($result['result'] as $page) : ?>
                                    <tr>
                                        <td>#<?=$page['id']?></td>
                                        <td><?=$page['title']?></td>
                                        <td><?=$page['slug']?></td>
                                        <td><?=$page['template']?></td>
                                        <td>
                                        <?php if($page['status'] == 1): ?>
                                                <span class="badge bg-green"><?=_lang("Published")?></span>
                                        <?php else : ?>
                                                <span class="badge bg-gray"><?=_lang("Unpublished")?></span>
                                        <?php endif; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm" href="<?=admin_url("cms/edit?id=".$page['id'])?>">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <a href="#"
                                               class="remove" data-id="<?=$page['id']?>">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>

                            <?php if(count($result['result'])==0): ?>
                                <tr>
                                    <td colspan="5"><?=_lang("No pages")?></td>
                                </tr>
                            <?php endif; ?>
                                </tbody>
                            </table>


                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">

                                    </div>

                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    <?php

                                        echo $result['pagination']->links(array(
                                            "q" => (RequestInput::get("q"))
                                        ), current_url());

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


<?php

$script = $this->load->view('cms/backend/scripts/manage-pages-script',NULL,TRUE);
AdminTemplateManager::addScript($script);






