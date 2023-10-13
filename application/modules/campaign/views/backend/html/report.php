


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

            <!-- /.col (LEFT) -->
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?=Translate::sprint("Campaign Report Last 24 Hours")?></b></h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <p class="text-center">

                            <span class="label" style="background-color:#0999ed"><?=_lang("Notifications delivered")?></span>
                            <span class="label" style="background-color:#ff7701"><?=_lang("Notifications viewed")?></span>

                        </p>
                        <div class="chart">
                            <canvas id="areaChartLast24h" style="height:250px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col -->

            <!-- /.col (LEFT) -->
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?=Translate::sprint("Campaign Report Last Week")?></b></h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="areaChartLastWeek" style="height:250px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col -->

            <!-- /.col (LEFT) -->
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?=Translate::sprint("Campaign Report Last 15 days")?></b></h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="areaChartLastMonth" style="height:250px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col -->

        </div>



    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<?php

$data['report_last_week'] = $report_last_week;
$data['last_week'] = $last_week;

$data['report_last_month'] = $report_last_month;
$data['last_month'] = $last_month;

$data['report_last_24h'] = $report_last_24h;
$data['last_24h'] = $last_24h;


$script = $this->load->view('campaign/backend/html/scripts/report-script',$data,TRUE);
AdminTemplateManager::addScript($script);
