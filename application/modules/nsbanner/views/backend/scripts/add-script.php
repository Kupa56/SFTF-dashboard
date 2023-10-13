<script src="<?= adminAssets("plugins/datepicker/bootstrap-datepicker.js") ?>"></script>
<script src="<?= adminAssets("plugins/select2/select2.full.min.js") ?>"></script>

<script>

    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });


<?php
    $token = $this->mUserBrowser->setToken("SU74aQ55");
    ?>

    $("#btnCreate").on('click', function () {

        var selector = $(this);

        if(module_name === "link"){
            module_id = $('#form #link').val();
        }

        var title = $("#form #title").val();
        var description = $("#form #description").val();

        var is_can_expire = 0;
        var date_begin = "";
        var date_end = "";

        if( $("#enable_scheduling").is(':checked') ){
            is_can_expire =  1;
            date_begin =  $("#form #date_b").val();
            date_end =  $("#form #date_e").val();
        }else{
            is_can_expire =  0;
        }

        var dataSet0 = {

            "token": "<?=$token?>",
            "module": module_name,
            "module_id": module_id,
            "date_begin": date_begin,
            "date_end": date_end,
            "is_can_expire": is_can_expire,

            "title": title,
            "description": description,

            "images": <?=$uploader_variable?>
        };


        $.ajax({
            url: "<?=  site_url("ajax/nsbanner/add")?>",
            data: dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("nsbanner/all")?>";
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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
<script>


    var module_name = "";
    var module_id = "";

    $('#select_module').select2();

    $('#select_module').on('change', function () {

        var value = $(this).val();

        $('.drop-box').addClass('hidden');
        $('.drop-box-'+value).removeClass('hidden');

        if(value === "link"){
            module_name = "link"
        }


        return true;
    });


<?php  foreach (CampaignManager::load() as $key => $module): ?>
    $('.drop-box-<?=$key?> .select-<?=$key?>').select2({
        ajax: {
            url: '<?=$module['api']?>',
            dataType: 'json',
            delay: 250,
            type: 'GET',
            data: function (params) {

                console.log(params);

                var query = {
                    all: 1,
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
                console.log(data);

                params.page = params.page || 1;

                return {
                    results: data.results
                }

            },
            cache: true
        }
    });

    //select event
    $('.drop-box-<?=$key?> .select-<?=$key?>').on('select2:select', function (e) {
        // Do something
        var data = e.params.data;
        var id = data.id;

        $(".custom-parameter").addClass("hidden");

        if(id>0){

            module_name = "<?=$key?>";
            module_id = id;

        }

    });


<?php endforeach; ?>


    $("#enable_scheduling").on('click',function () {

        if($(this).is(':checked')){

            $('.scheduling_date_begin').removeClass('hidden');
            $('.scheduling_date_end').removeClass('hidden');

        }else{

            $('.scheduling_date_begin').addClass('hidden');
            $('.scheduling_date_end').addClass('hidden');

        }

    });


</script>


