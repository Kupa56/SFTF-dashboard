<?php
$token = $this->mUserBrowser->setToken("SUSz74aQ55");
$token2 = $this->mUserBrowser->setToken("SU1Sz74aQ55");
?>
<!-- page script -->
<script src="<?=  adminAssets("plugins/datepicker/bootstrap-datepicker.js")?>"></script>
<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>
<script>

    //select type of campaign
    $('.selectCType').select2();
    $('.selectCType').on('select2:select', function (e) {


        $(".custom-parameter").addClass("hidden");

        $(".drop-box").addClass("hidden");
        $(".box-estimation").addClass("hidden");
        $(".campaign_fields").addClass("hidden");

        // Do something
        var data = e.params.data;
        var type = data.id;
        module_name = type;

        if(module_name !== 0){

            $("div .drop-box").addClass("hidden");
            $(".drop-box-"+module_name).removeClass("hidden");

        }

    });


    var results_list = null;

<?php  foreach (CampaignManager::load() as $key => $module): ?>
    $('.drop-box-<?=$key?> .select-<?=$key?>').select2();
    $('.drop-box-<?=$key?> .select-<?=$key?>').select2({
        ajax: {
            url: '<?=$module['api']?>',
            dataType: 'json',
            delay: 250,
            type: 'GET',
            data: function (params) {

                console.log(params);

                var query = {
                    search: params.term,
                    page: params.page || 1
                };
                // Query parameters will be ?search=[term]&page=[page]
                return query;
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                console.log("result");
                console.log(data);

                results_list = data;

                params.page = params.page || 1;

                return {
                    results: data.results
                }

            },
            escapeMarkup: function(markup) {
                return markup;
            },
            cache: false
        }
    });

    //select event
    $('.drop-box-<?=$key?> .select-<?=$key?>').on('change', function (e) {
        // Do something
      //  var data = e.params.data;
        var id = $(this).val();

        $(".custom-parameter").addClass("hidden");

        if(id>0){

            module_name = "<?=$key?>";
            module_id = id;

        <?php if(isset($module['custom_parameters'])): ?>
            $(".custom-parameter-<?=$key?>").removeClass("hidden");
            calculateEstimation("<?=$key?>",id,<?=$module['custom_parameters']['var']?>);
        <?php else: ?>
            calculateEstimation("<?=$key?>",id);
        <?php endif; ?>


            update_device_previews(id);

        }else{

            $(".box-estimation").addClass("hidden");
            $(".campaign_fields").addClass("hidden");

        }

    });

    var custom_parameters = {};
    function callback_campaign_<?=$key?>(parameters) {

        custom_parameters = parameters;
        var module_id = $('.drop-box-<?=$key?> .select-<?=$key?>').val();
        var module = "<?=$key?>";

        calculateEstimation(module,module_id,parameters);

    }


<?php endforeach; ?>


    function update_device_previews(id) {

        for (let key in results_list.results){
            console.log(results_list.results);

            var id1 = parseInt(id);
            var id2 = parseInt(results_list.results[key].id);

            if(id1 === id2){

                $(".previews-container > .device-preview .notification-content .title").text(results_list.results[key].title);
                $(".previews-container > .device-preview .notification-content .text").text(results_list.results[key].description);

                if(results_list.results[key].image !==""){
                    $(".previews-container > .device-preview .notification-image").attr("src",results_list.results[key].image);
                }else {
                    let dimg = $(".previews-container > .device-preview .notification-image").attr("data-placeholder",results_list.results[key].description);
                    $(".previews-container > .device-preview .notification-image").attr("src",dimg);
                }

                $(".campaign_fields #campaign_name").val(results_list.results[key].title);
                $(".campaign_fields #campaign_text").val(results_list.results[key].description);

                break;
            }
        }

        $(".create_campaign #campaign_name").on('keyup',function () {
            let name = $(this).val();
            if(name !== ""){
                $(".previews-container > .device-preview .notification-content .title").text(name);
            }else {
                let placeholder = $(".previews-container > .device-preview .notification-content .title").attr('data-placeholder');
                $(".previews-container > .device-preview .notification-content .title").text(placeholder);
            }

            return false;
        });


        $(".create_campaign #campaign_text").on('keyup',function () {
            let text = $(this).val();
            if(text !== ""){
                $(".previews-container > .device-preview .notification-content .text").text(text);
            }else {
                let placeholder = $(".previews-container > .device-preview .notification-content .text").attr('data-placeholder');
                $(".previews-container > .device-preview .notification-content .titexttle").text(placeholder);
            }

            return false;
        });
    }

    //calculate estimation
    function  calculateEstimation(module_name,module_id,custom_parameters) {

        this.custom_parameters = custom_parameters;
        console.log(custom_parameters);

        $.ajax({
            url:"<?=  site_url("ajax/campaign/getEstimation")?>",
            data:{
                "token":"<?=$token2?>",
                "module_id":module_id,
                "module_name":module_name,
                "custom_parameters": custom_parameters
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $(".box-estimation .target_value").html('&nbsp;<i class="fa fa-refresh fa-spin"></i>&nbsp;');
                $(".box-estimation").removeClass("hidden");
                $(".create_campaign #btnCreate").attr('disabled',true);


            },error: function (request, status, error) {

                $(".box-estimation").addClass("hidden");
                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                $(".box-estimation .target_value").html('&nbsp;0&nbsp;');

                if(data.success===1){
                    t = data.result;

                    if(data.result > 0){
                        $(".box-estimation .target_value").html('&nbsp;+'+data.result+'&nbsp;');
                        $(".box-estimation").removeClass("hidden");
                        $(".create_campaign #btnCreate").attr('disabled',false);
                    }else {
                        $(".box-estimation .target_value").html('&nbsp;0&nbsp;');
                    }

                }

                $(".campaign_fields").removeClass("hidden");

            }
        });


    }
</script>
<script>

    var module_name = "";
    var module_id = 0;
    var t=0;
    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });


    $("#btnCreate").on('click',function(){

        let selector = $(this);

        console.log("custom_parameters");
        console.log(custom_parameters);

        var dataSet0 = {
            "token":"<?=$token?>",
            "campaign_name":$("#campaign_name").val(),
            "campaign_text":$("#campaign_text").val(),
            "module_id":module_id,
            "t":t,
            "module_name":module_name,
            "custom_parameters":JSON.stringify(custom_parameters)
        };

        $.ajax({
            url:"<?=  site_url("ajax/campaign/createCampaign")?>",
            data:dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            },error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if(data.success===1){
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href="<?=admin_url("campaign/campaigns")?>";
                }else if(data.success===0){
                    NSTemplateUIAnimation.button.default = selector;
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }

        });

        return false;

    });



</script>
<?php if(GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)): ?>
    <script>

        $('.selectTestCType').select2();

        $(".selectTestCType").on('select2:select', function (e) {
            // Do something
            var data = e.params.data;
            var id = data.id;

            if(id == 0){
                $("#btnTest").attr("disabled",true);
            }else{
                $("#btnTest").attr("disabled",false);
            }

        });

        $("#btnTest").attr("disabled",true);

        $("#btnTest").on('click',function () {

            $.ajax({
                url:"<?=  site_url("ajax/campaign/testPush")?>",
                data:{
                    "token":"<?=$token2?>",
                    "module_name":$(".selectTestCType").val(),
                    "guest_ids":$("#gids").val()
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {


                    $("#btnTest").attr("disabled",true);

                },error: function (request, status, error) {

                    $("#btnTest").attr("disabled",false);
                    console.log(request);

                },
                success: function (data, textStatus, jqXHR) {

                    $("#btnTest").attr("disabled",false);

                    if(data.success===1){
                        alert(data.result);
                    }

                }
            });
            return true;
        });


    </script>
<?php endif; ?>
<script>



</script>
