<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">


        .invoice-box{
            margin:auto;
            padding:30px;
            font-size:16px;
            line-height:24px;
            font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color:#555;
        }

    <?php if(isset($frame) and $frame==TRUE): ?>
        .invoice-box{
            margin:0 auto;
            width: 60%;
            border: 1px solid #EEEEEE;
        }
        .invoice-print{
            margin:0 auto;
            text-align: center;
        }

        .invoice-print button{
            margin: 30px;
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 25px;
            padding-right: 25px;
            font-size: 18px;
        }

    <?php endif; ?>


        .invoice-box table{
            width:100%;
            text-align:left;
            table-layout:fixed;
            word-break:break-all;
        }

        .invoice-box table td{
            padding:5px;
            vertical-align:top;
        }

        .invoice-box table tr.information td.address{
            padding:5px;
            vertical-align:top;
            table-layout:fixed;
            /*width:20px !important;*/
            overflow:hidden;
            word-wrap:break-word;
        }

        .invoice-box table tr td:nth-child(2){
            text-align:right;
        }

        .invoice-box table tr.top td{
            padding-bottom:20px;
        }

        .invoice-box table tr.top table td.title{
            font-size:45px;
            line-height:45px;
            color:#333;
        }

        .invoice-box table tr.information td{
            padding-bottom:40px;

        }


        .invoice-box table tr.heading td{
            background:#eee;
            binvoice-bottom:1px solid #ddd;
            font-weight:bold;
        }

        .invoice-box table tr.details td{
            padding-bottom:20px;
        }

        .invoice-box table tr.item td{
            binvoice-bottom:1px solid #eee;
        }

        .invoice-box table tr.item.last td{
            binvoice-bottom:none;
        }

        .invoice-box table tr.total td:nth-child(2){
            binvoice-top:2px solid #eee;
            font-weight:bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td{
                width:100%;
                display:block;
                text-align:center;
            }

            .invoice-box table tr.information table td{
                width:100%;
                display:block;
                text-align:center;
            }
        }

    </style>
</head>
<body>

<?php if(isset($frame) and $frame==TRUE): ?>
    <div class="invoice-print">
        <button onclick="document.location.href = '<?=$link?>'">Print</button>
    </div>
<?php else: ?>
    <script>
        window.print();
    </script>
<?php endif; ?>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2" align="left" class="title" width="50%">
                <img style="max-height: 130px;width: auto;max-width: 200px;" src="<?=$logo?>">
            </td>

            <td colspan="2" align="right" width="50%">
                <h1><?=$doc_name?></h1>
                #<span style="text-transform: uppercase"><?=$doc_name?></span>-<?=str_pad($no, 6, "0", STR_PAD_LEFT);
                ?><br>
                <?=Translate::sprint("Date")?>: <?=$created_at?><br><br>
            </td>
        </tr>

        <tr class="information">
            <td colspan="4" align="right">
                <table width="100%">
                    <tr>
                        <td align="right" width="40%" class="address"></td>
                        <td width="20%"></td>
                        <td align="left"  width="40%" class="address">
                            <strong><?=$client_name?></strong><br>
                            <?=$client_data?>

                        </td>
                    </tr>

                </table>
            </td>
        </tr>



    </table>



    <table>

        <tr class="heading">
            <td width="80%" align="left">
                <?=Translate::sprint("Item (s)")?>
            </td>
            <td align="right"  width="20%">
                <?=Translate::sprint("Amount")?>
            </td>
        </tr>



    <?php foreach($items as $item): ?>
            <tr class="item last">
                <td align="left">
                    <?=$item['label']?>
                </td>
                <td align="right">
                    <?=$item['amount']?>
                </td>
            </tr>
    <?php endforeach; ?>



        <tr>
            <td colspan="2"></td>
        </tr>


        <tr class="total">
            <td align="right">
                <?=Translate::sprint("SUBTOTAL")?>:
            </td>

            <td align="right">
                <?=$sub_amount?>
            </td>
        </tr>



    <?php
        if(isset($taxes_value) && count($taxes_value)> 0):
        for($i= 0 ;$i<count($taxes_value); $i++) {
        ?>
        <tr class="total">
            <td align="right">
                <?=$taxes_value[$i]['tax_name']?>:
            </td>
            <td align="right" class="heading">
                <?=$taxes_value[$i]['tax_value']?>
            </td>
        </tr>
    <?php } ?>
    <?php endif; ?>



    <?php if (isset($extras) and !empty($extras)) :
            foreach ($extras as $key => $extra): ?>

                <tr class="total">
                    <td align="right">
                        <?= str_replace('_', ' ', strtoupper($key))  ?>
                    </td>

                    <td align="right" class="heading">
                        <?= $extra ?>
                    </td>
                </tr>

        <?php endforeach;
        endif;
        ?>



        <tr class="total">
            <td align="right">
                <?=Translate::sprint("TOTAL")?>:
            </td>

            <td align="right" class="heading">
            <?php

                if(!isset($amount)){
                    echo $sub_amount;
                }else
                    echo $amount;

                ?>
            </td>
        </tr>


    </table>
</div>
</body>


</html>
