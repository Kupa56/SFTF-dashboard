<?php if(_APP_VERSION!=APP_VERSION): ?>
    <div class="callout callout-warning">
        <h4><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Update!</h4>
        <h5>The update for <?=APP_VERSION?> is ready</h5>
        <a href="<?=base_url("update?id=".CRYPTO_KEY)?>">Run the update</a>
    </div>
<?php endif;?>
