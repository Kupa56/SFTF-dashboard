<table class="table" style="background-color: #EEEEEE">

	<?php
	$e = 0;
	?>
	<?php foreach ($errors as $line => $error): $e++; ?>
	<tr>
		<td class="text-blue"><span class="open-error cursor-pointer" data-line="<?=$line?>"><i class="fas fa-arrow-right"></i>&nbsp;&nbsp;<?=Translate::sprintf("Error ar line (%s)",array(intval($line+1)))?></span></td>
	</tr>
		<tr style="display: none" class="message message-<?=$line?>">
			<td>
				<table class="table">
					<?php foreach ($error as $k => $message):?>
					<tr>
						<td><?=$k?></td>
						<td class="text-red"><?=$message?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</td>

		</tr>

	<?php if($e==5) break; endforeach; ?>
</table>




<script>
    $('.table .open-error').on('click',function() {
        let line = $(this).attr('data-line');
        $('.table .message').fadeOut(100);
        $('.message-'+line).delay(100).fadeIn(200);
        return false;
    });
</script>


