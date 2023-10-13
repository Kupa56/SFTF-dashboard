<strong class="uppercase"><?=_lang("Booking Item(s)")?></strong><br/>

<?php

$cart = json_decode($reservation['cart'], JSON_OBJECT_AS_ARRAY);
$sub_total = 0;
$currency = DEFAULT_CURRENCY;


?>

<?php if (count($cart) > 0): ?>
    <table class="table">
    <?php foreach ($cart as $item): ?>
            <tr>
                <td>
                <?php


                    if (empty($item))
                        continue;

                    $callback = NSModuleLinkers::find($item['module'], 'getData');

                    if ($callback != NULL) {

                        $params = array(
                            'id' => $item['module_id']
                        );

                        $result = call_user_func($callback, $params);

                        echo $result['label'];

                        if (isset($item['options']))
                            echo BookingHelper::optionsBuilderString($item['options']);

                    }


                    ?>
                </td>
                <td align="right" valign="top">
                <?php
                    echo Currency::parseCurrencyFormat($item['amount'] * $item['qty'], DEFAULT_CURRENCY);
                    $sub_total = $sub_total + $item['amount'] * $item['qty'];
                    ?>
                </td>

            </tr>

    <?php endforeach; ?>

        <tr>
            <td>
                <strong><?=_lang("Total")?></strong>
            </td>
            <td align="right" valign="top">
                <?=Currency::parseCurrencyFormat($sub_total, DEFAULT_CURRENCY)?>
            </td>
        </tr>


        <tr>
            <td colspan="2">
                <strong><?=_lang("Booking Payment")?>: </strong>
            <?php

                $invoice = $this->mBookingModel->getInvoice($reservation['id']);

                $pcode = $reservation['payment_status'];
                $payments = Booking_payment::PAYMENT_STATUS;
                if (isset($payments[$pcode])) {
                    if($pcode == "cod"){
                        echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>"._lang("Cash payment")."</span><br>";
                    }else if($pcode == "cod_paid"){
                        echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>"._lang("Paid with cash")."</span><br>";
                    }else{
                        echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>" . ucfirst(_lang($payments[$pcode]['label'])) .( ($invoice->method != "" && $pcode=="paid")?" (".$invoice->method.") ":""). "</span><br>";
                    }
                } else if ($pcode == "cod_paid") {
                    echo "<span class='badge bg-green'>" . ucfirst(_lang("Paid with cash")) . "</span><br>";
                }

                ?>
            </td>
        </tr>


    </table>
<?php else: ?>

    <tr>
        <td><?= _lang("No services") ?></td>
    </tr>

<?php endif; ?>





<strong class="uppercase"><?=_lang("Booking Details")?></strong><br/>

<?php


$cf_id = intval($reservation['cf_id']);
$reservation['cf_data'] = json_decode($reservation['cf_data'], JSON_OBJECT_AS_ARRAY);
if (isset($reservation['cf_data'])) {

    $cf_object = CFManagerHelper::getByID($cf_id);
    $fields = json_decode($cf_object['fields'], JSON_OBJECT_AS_ARRAY);

    foreach ($fields as $k => $field) {

        if (!isset($reservation['cf_data'][$field['label']])) {
            continue;
        }

        $data = $reservation['cf_data'][$field['label']];

        if ($data == "")
            $data = "--";


        if ($field['type'] == "input.location") {

            if ($k == "") {
                echo "<span><strong>" . $field['label'] . "</strong>: -- </span><br>";
            } else {

                if (preg_match("#;#", $data)) {
                    $l = explode(";", $data);
                    echo "<span><strong>" . $field['label'] . "</strong>: <a class='loc-detail' href='#' data-address='$l[0]' data-lat='$l[1]' data-lng='$l[2]'><i class='mdi mdi-map-marker'></i>&nbsp;&nbsp;$l[0]</a> </span><br>";
                } else {
                    echo "<span><strong>" . $field['label'] . "</strong>: $data </span><br>";
                }

            }
        } else
            echo "<span><strong>" . $field['label'] . "</strong>: $data</span><br>";

    }
}

?>