<script>

    let global_value = $('#offer_coupon_config_limit').val();

    $('#offer_coupon_config_type').on('change',function () {
        let type = $(this).val();
        if(type === "disabled"){
            $("#offer_coupon_config_limit").attr("disabled",true);
            $("#offer_coupon_code").attr("disabled",true);
        }else if(type === "unlimited"){
            $("#offer_coupon_config_limit").attr("disabled",true).val( -1);
            $("#offer_coupon_code").attr("disabled",false);
        }else  if(type === "limited"){
            $("#offer_coupon_config_limit").attr("disabled",false).val(  (global_value > 0?global_value:0) );
            $("#offer_coupon_code").attr("disabled",false);
        }
    });

</script>