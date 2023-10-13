<?php if(installFolderFound()): ?>
    <div class="callout callout-danger">
        <h4><?php echo Translate::sprint("The installation folder represents a danger","The installation folder represents a danger"); ?> </h4>
        <p><?php echo Translate::sprint("Please remove install directory",""); ?> </p>
    </div>
    <br>
<?php endif;?>

<?php NotesManager::fetchAllNotes(); ?>







