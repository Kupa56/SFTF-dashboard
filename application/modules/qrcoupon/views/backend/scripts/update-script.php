<script>


    //instance select2
    $('.select2').select2();

    //open updater of coupon status
    $("a[data-update-status]").on('click',function (){

        $('#update-coupon-modal').modal("show");

        let $data_id = $(this).attr("data-id");
        let $data_offer_id = $(this).attr("data-offer-id");


        $('#update-coupon-modal #data-id').val($data_id);
        $('#update-coupon-modal #data-offer-id').val($data_offer_id);


        return false;
    });


    $('#update-coupon-modal #saveChange').on('click',function (){

        let selector = $(this);

        let $data_id = $('#update-coupon-modal #data-id').val();
        let $data_offer_id = $('#update-coupon-modal #data-offer-id').val();
        let $status = $('#update-coupon-modal #select_coupon_status').val();

        $.ajax({
            url: "<?=  site_url("ajax/qrcoupon/updateStatus")?>",
            data: {
                "coupon_id": $data_id,
                "offer_id": $data_offer_id,
                "status": $status,
            },
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

                    $('#update-coupon-modal').modal("hide");

                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();


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
    })




</script>