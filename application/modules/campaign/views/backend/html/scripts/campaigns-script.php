
<script>



    $("#campaigns_actions").on('change',function () {

        let status = $(this).val();
        document.location.href = "<?=admin_url("campaign/campaigns?action=")?>"+status;

        return false;
    });

</script>