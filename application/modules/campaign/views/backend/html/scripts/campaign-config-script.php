<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>
<script>

    $('.campaign_config select.select2').select2();

    $(".campaign_config .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/campaign/saveCampaignConfig")?>",
            dataType: 'json',
            data:{
                'RADUIS_TRAGET': $('.campaign_config #RADUIS_TRAGET').val(),
                'LIMIT_PUSHED_GUESTS_PER_CAMPAIGN': $('.campaign_config #LIMIT_PUSHED_GUESTS_PER_CAMPAIGN').val(),
                'PUSH_CAMPAIGNS_WITH_CRON': $('.campaign_config #PUSH_CAMPAIGNS_WITH_CRON').val(),
                'NBR_PUSHS_FOR_EVERY_TIME': $('.campaign_config #NBR_PUSHS_FOR_EVERY_TIME').val(),
                '_NOTIFICATION_AGREEMENT_USE': $('.campaign_config #_NOTIFICATION_AGREEMENT_USE').val(),
            },
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
               console.log(request);

                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.success = selector;

                if (data.success === 1) {
                    document.location.reload()
                } else if (data.success === 0) {
                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "\n";
                    }
                    if (errorMsg !== "") {
                        alert(errorMsg);
                    }
                }
            }

        });

        return false;
    });


</script>