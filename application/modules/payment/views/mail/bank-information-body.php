<?=_lang("Here is the bank information to complete the payment:")?>
<table>

    <tr>
        <td><strong><?=_lang("Order ID")?></strong></td>
        <td>#<?=$invoice->module_id?></td>
    </tr>
    <tr>
        <td><strong><?=_lang("Amount to pay")?></strong></td>
        <td><?=$invoice->amount?> <?=$invoice->currency?></td>
    </tr>

    <tr>
        <td colspan="2">
            <strong><?=_lang("Bank information")?></strong>
        </td>
    </tr>

    <tr>
        <td><strong><?=_lang("Name")?></strong></td>
        <td><?=ConfigManager::getValue('TRANSFER_BANK_NAME')?></td>
    </tr>
    <tr>
        <td><strong><?=_lang("SWIFT / BIC code")?></strong></td>
        <td><?=ConfigManager::getValue('TRANSFER_BANK_SWIFT')?></td>
    </tr>
    <tr>
        <td><strong><?=_lang("IBAN / Account Number")?></strong></td>
        <td><?=ConfigManager::getValue('TRANSFER_BANK_IBAN')?></td>
    </tr>
    <tr>
        <td><strong><?=_lang("Additional information")?></strong></td>
        <td><?=ConfigManager::getValue('TRANSFER_BANK_DETAILS')?></td>
    </tr>


</table>
<p>
    <?=_lang("in order to verify your payment, please reply on this email with including your transaction id or payment receipt")?>
</p>


