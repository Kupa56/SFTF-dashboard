<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content campaign_config">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
            <?php $this->load->view(AdminPanel::TemplatePath . "/include/messages"); ?>
            </div>

        </div>

        <div class="box box-solid ">
            <div class="box-header with-border">
                <h3 class="box-title"><b><?= Translate::sprint("Campaign logs") ?></b></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-sm-12">

                    <table class="table">
                        <tr>
                            <td><?=_lang("Client")?></td>
                            <td><?=_lang("Status")?></td>
                            <td><?=_lang("Endpoint response (Firebase API)")?></td>
                        </tr>

                    <?php if(isset($result) && !empty($result)): ?>

                    <?php foreach ($result as $pc):?>
                            <tr>
                                <td>
                                    #<?=$pc['guest_id']?> (<?=$pc['platform']?>)
                                </td>
                                <td>
                                <?php if($pc['failed'] == 1):?>
                                        <span class="badge bg-red"><?=_lang("Failed")?></span>
                                <?php elseif($pc['failed'] == -1): ?>
                                        <span class="badge bg-green"><?=_lang("Success")?></span>
                                <?php endif;?>
                                </td>
                                <td><code><?=$pc['logs']?></code></td>
                            </tr>
                    <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                <?=_lang("No data loaded")?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </table>

                </div>
            </div>

        </div>


    </section>

</div>





