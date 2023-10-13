<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2" align="left" class="title" width="50%">
            <?php if(isset($companyLogo)){ ?>
                    <img style="max-height: 186px;width: auto;max-width: 200px;" src="<?=$companyLogo?>">
            <?php } ?>
            </td>

            <td colspan="2" align="right" width="50%">
                <h1><?=$name?></h1>
                #<?=$prefix.$id?> <br>
                <?=Translate::sprint("Date")?>: <?=$dateCreated?><br>
            </td>
        </tr>

        <tr class="information">
            <td colspan="4" align="right">
                <table width="100%">
                    <tr>
                        <td align="right" width="40%" class="address"></td>
                        <td width="20%"></td>
                        <td align="right"  width="40%" class="address">
                            <?=$company_data?>
                        </td>
                    </tr>
                <?php if(isset($client_data_bill_to)): ?>
                        <tr>
                            <td align="right" width="40%" class="address"></td>
                            <td width="20%"></td>
                            <td align="right"  width="40%" class="address">
                                <?=$client_data_bill_to?>
                            </td>
                        </tr>
                <?php endif;?>
                <?php if(isset($client_data_ship_to)): ?>
                        <tr>
                            <td align="right" width="40%" class="address"></td>
                            <td width="20%"></td>
                            <td align="right"  width="40%" class="address">
                                <?=$client_data_ship_to?>
                            </td>
                        </tr>
                <?php endif;?>
                </table>
            </td>
        </tr>

                <tr class="heading">
                    <td colspan="4">
                        <?=Translate::sprint("Payment")?>
                    </td>
                </tr>

                <tr class="details">
                    <td colspan="3">
                    <?php
                            echo Translate::sprint("Unpaid")
                        ?>
                    </td>
                    <td align="right">

                    </td>
                </tr>


    </table>



    <table>

        <tr class="heading">
            <td width="50%">
                <?=Translate::sprint("Item (s)")?>
            </td>
            <td  width="20%">
                <?=Translate::sprint("Price/Unit")?>
            </td>
            <td  width="10%">
                <?=Translate::sprint("Qty")?>
            </td>
            <td align="right"  width="20%">
                <?=Translate::sprint("Amount")?>
            </td>
        </tr>



    <?php

        $total_without_tax = 0;

        $amount = array();
        foreach ($items as $item){

            $p=NULL;
            if($item->type=="item"){
                $p = $item->item();
                $p->price = $p->price;
            }else{
                $p =  $item->service();
            }


            $amount[] = array(
                "type"    => "value",
                "value"   => $item->qty*$item->price
            );


            ?>

            <tr class="item last">
                <td>
                    <?=$p->name?>
                </td>
                <td>
                    <?=number_format($p->price, 2, ',', ' ')."/$p->unit"?>
                </td>
                <td>
                    <?="$item->qty"?>
                </td>
                <td align="right">
                    <?=number_format($item->amount, 2, ',', ' ')?>
                </td>
            </tr>
    <?php } ?>

        <tr>
            <td colspan="4"></td>
        </tr>

    <?php

        $sub_total = Currency::calculAmount($amount);
        $sub_total = $sub_total['sub-total'];

        ?>

        <tr class="total">
            <td colspan="3" align="right">
                <?=Translate::sprint("SUBTOTAL")?>:
            </td>

            <td align="right">
                <?=Currency::display($sub_total)?>
            </td>
        </tr>


    <?php if(isset($discount)): ?>

        <?php foreach ($discount as $disc): ?>

            <?php if($disc['value']!=0):?>
                    <tr>
                        <td colspan="3" align="right">
                            <?=Translate::sprint("Discount")?> "<?=$disc['name']?>"
                        </td>

                        <td align="right">
                            <?=$disc['value']?> %
                        </td>
                    </tr>

                <?php

                    $amount[] = array(
                        "type"    => "percent",
                        "value"   => $disc['value']
                    );

                    ?>
            <?php endif;?>

        <?php endforeach; ?>


    <?php endif;?>

    <?php if(isset($fields)): ?>

        <?php foreach ($fields as $field): ?>

            <?php if($field['value']!=0):?>
                    <tr>
                        <td colspan="3" align="right">
                            <?=Translate::sprint($field['key'])?>
                        </td>

                        <td align="right">
                            <?=Currency::display($field['value'])?>
                        </td>
                    </tr>

                <?php

                    $amount[] = array(
                        "type"    => "fees",
                        "value"   => $field['value']
                    );

                    ?>

            <?php endif;?>
        <?php endforeach; ?>


    <?php endif;?>

    <?php

        $total = Currency::calculAmount($amount);
        $total = $total['total'];

        ?>



        <tr class="total">
            <td colspan="3" align="right">
                <?=Translate::sprint("TOTAL")?>:
            </td>

            <td align="right" class="heading">

                <b><?=Currency::display($total)?></b>

            </td>
        </tr>
    </table>
</div>
</body>


</html>
