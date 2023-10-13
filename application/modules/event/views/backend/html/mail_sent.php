
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

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

                <div class="box  box-solid">
                    <div class="box-header" style="min-height: 54px;">
                        <div class="box-title" style="width : 100%;">
                            <div class="title-header ">
                                <b><?= Translate::sprint("Event Reminders") ?></b>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body  table-responsive">
                        <div class="success-container" style="padding: 15px;margin-bottom: 30px;text-align: center">
                            <div><i class="mdi mdi-check-circle text-green" style="font-size: 70px"></i></div>
                            <div><strong><?=Translate::sprintf("Reminders was sent to (%s) successfully",array(count($contacts)))?></strong></div>
                            <div><u><a href="<?=admin_url("event/my_events")?>"><?=_lang("Back My Events")?></a></u></div>
                        </div>
                    </div>
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

