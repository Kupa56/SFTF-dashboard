<?php

$ownerResult = $this->mBookingModel->getWidgetData();


?>
<div class="row dashboard">
    <div class="col-sm-8">
        <!-- LINE CHART -->
        <div class="box box-solid reservation-dashboard">
            <div class="box-header">
                <h3 class="box-title"><b><?=_lang("Reservations")?></b></h3>
                <div class="box-tools pull-right">
                   <select class="select2 dashboard-dropdown" data-label="<?=_lang("All reservation(s)")?>">
                <?php foreach ($ownerResult as $key => $value): ?>
                       <option value="<?=$key?>"><?=_lang("_filter_dashboard_".$key)?></option>
                <?php endforeach; ?>
                   </select>
                </div>
            </div>
            <div class="box-body chart-responsive">
                <div class="chart" id="line-chart" style="height: 290px;"></div>
            </div>
            <!-- /.box-body -->
            <div class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <div class="col-sm-4 counters">
        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner" data-dashboard-status="pending">
                <h3 class="text-orange" data-dashboard-count="true" >0</h3>
                <p><?=_lang("Pending reservation(s)")?></p>
            </div>
        </div>
        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner" data-dashboard-status="confirmed">
                <h3 class="text-green" data-dashboard-count="true">0</h3>
                <p><?=_lang("Confirmed reservation(s)")?></p>
            </div>
        </div>
        <div class="small-box" style="color: black !important; background-color: white;">
            <div class="inner" data-dashboard-status="canceled">
                <h3 class="text-red" data-dashboard-count="true">0</h3>
                <p><?=_lang("Canceled reservation(s)")?></p>
            </div>
        </div>
    </div>
</div>


<dashboard-analytics data-module="booking" dashboard-analytics-status="all" class="hidden">
<?php foreach ($ownerResult as $key => $value): ?>
        <item data-id="<?=$key?>" data-key="<?=$key?>" data-label="<?=_lang($key)?>"><?=json_encode($value['all'])?></item>
<?php endforeach; ?>
</dashboard-analytics>

<dashboard-analytics data-module="booking" dashboard-analytics-status="pending" class="hidden">
<?php foreach ($ownerResult as $key => $value): ?>
        <item data-id="<?=$key?>" data-key="<?=$key?>" data-label="<?=_lang($key)?>"><?=json_encode($value['pending'])?></item>
<?php endforeach; ?>
</dashboard-analytics>

<dashboard-analytics data-module="booking" dashboard-analytics-status="canceled" class="hidden">
<?php foreach ($ownerResult as $key => $value): ?>
        <item data-id="<?=$key?>" data-key="<?=$key?>" data-label="<?=_lang($key)?>"><?=json_encode($value['canceled'])?></item>
<?php endforeach; ?>
</dashboard-analytics>

<dashboard-analytics data-module="booking" dashboard-analytics-status="confirmed" class="hidden">
<?php foreach ($ownerResult as $key => $value): ?>
        <item data-id="<?=$key?>" data-key="<?=$key?>" data-label="<?=_lang($key)?>"><?=json_encode($value['confirmed'])?></item>
<?php endforeach; ?>
</dashboard-analytics>

<?php

$script = $this->load->view('booking/client-dashboard/client-dashboard-script',NULL,TRUE);
AdminTemplateManager::addScript($script);


