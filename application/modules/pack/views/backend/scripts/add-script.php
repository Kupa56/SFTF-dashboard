<script src="<?=  adminAssets("plugins/select2/select2.full.min.js")?>"></script>
<script src="<?=  adminAssets("plugins/iCheck/icheck.min.js")?>"></script>

<script>


    //iCheck
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });

    var _free = 0;
    var _recommended = 0;
    var _display = 1;
    var price_per_month = 0;
    var price_per_year = 0;
    var _discount = 0;




    $('#is_free').iCheck('uncheck');

    //purchase check box
    $('#is_free').on('ifChecked', function(event){
        _free = 1;

        $("#price_per_month").attr('disabled',true);
        $("#price_per_year").attr('disabled',true);
        $("#trial_period").attr('disabled',false);
    });

    $('#is_free').on('ifUnchecked', function(event){
        _free = 0;

        $("#price_per_month").attr('disabled',false);
        $("#price_per_year").attr('disabled',false);
        $("#trial_period").attr('disabled',false);
    });


    $('#_display').iCheck('check');

    //purchase check box
    $('#_display').on('ifChecked', function(event){
        _display = 1;
    });

    $('#_display').on('ifUnchecked', function(event){
        _display = 0;
    });


    $('#auto_push_campaign').select2();


    $('#recommended').iCheck('uncheck');

    //purchase check box
    $('#recommended').on('ifChecked', function(event){
        _recommended = 1;
    });

    $('#recommended').on('ifUnchecked', function(event){
        _recommended = 0;
    });

    $("#btnCreate").on('click',function(){

        var selector = $(this);

        var dataSet = {
            "name":$("#name").val(),
            "price":$("#price_per_month").val(),
            "order":$("#order").val(),
            "recommended":_recommended,
            "display":_display,
            "price_yearly":$("#price_per_year").val(),
            "free":_free,
            "description": $("#description").val(),
            "trial_period": $("#trial_period").val(),
            "group_access": $("#group_access").val()
        };


        var user_subscribe = {};
    <?php foreach ($user_subscribe_fields as $field): ?>
        user_subscribe.<?=$field['field_name']?> =  $("#pack_<?=$field['config_key']?>").val();
    <?php endforeach; ?>
        dataSet.user_subscribe =  user_subscribe;


        $.ajax({
            url:"<?=  site_url("ajax/pack/add")?>",
            data:dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                selector.attr("disabled",true);

            },error: function (request, status, error) {
                alert(request.responseText);
                selector.attr("disabled",false);
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                selector.attr("disabled",false);
                if(data.success===1){
                    document.location.href="<?=admin_url("pack/pack_manager")?>";
                }else if(data.success===0){
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


    $('.form-group .form-control.select2').select2();


</script>


