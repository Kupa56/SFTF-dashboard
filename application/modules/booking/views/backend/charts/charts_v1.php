<div class="row">



    <!-- /.col (LEFT) -->
    <div class="col-md-8">
        <!-- AREA CHART -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><b><?=Translate::sprint("Sales today")?></b></h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="areaChartOrders" style="height:280px"></canvas>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

    </div>
    <!-- /.col -->
    <div class="col-md-4">

        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner">
                <h3><?=Currency::parseCurrencyFormat($result_today['total'], DEFAULT_CURRENCY)?></h3>
                <p><?=Translate::sprintf("%s sale(s) today", array($result_today['count']));?></p>
            </div>
        </div>
        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner">
                <h3><?=Currency::parseCurrencyFormat($result_this_month['total'], DEFAULT_CURRENCY)?></h3>
                <p><?=Translate::sprintf("%s sale(s) in %s", array($result_this_month['count'],date("F, Y", time())));?></p>
            </div>
        </div>

        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner">
                <h3><?=Currency::parseCurrencyFormat($result_this_year['total'], DEFAULT_CURRENCY)?></h3>
                <p><?=Translate::sprintf("%s sale(s) in %s", array($result_this_year['count'],date("Y", time())));?></p>
            </div>
        </div>
    </div>

</div>
<?php

$data['result'] = $result;
$script = $this->load->view('booking/backend/charts/script',$data,TRUE);
AdminTemplateManager::addScript($script);

