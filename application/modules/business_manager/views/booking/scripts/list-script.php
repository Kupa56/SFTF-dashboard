<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>

    load_booking(1,"");

    var loaded_booking = 0;
    function load_booking(page, q) {

        $.ajax({
            url: "<?=  site_url("business_manager/ajax/my_booking")?>",
            data: {
                "page": page,
                "q": q,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $('#view-booking .loading').removeClass('hidden');
                $("#view-booking .preloader").removeClass('hidden');


                $("#view-booking .load-more").addClass('hidden');
                $("#view-booking .no-result").addClass('hidden');
            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);

                $('#view-events .loading').addClass('hidden');
                $("#view-events .preloader").addClass('hidden');

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                $('#view-booking .loading').addClass('hidden');
                $("#view-booking .preloader").addClass('hidden');


                if(data.success===1){

                    if(page === 1){
                        loaded_booking = 0;
                        $('#view-booking ul.post-list').html(data.html);
                    }else {
                        $('#view-booking ul.post-list').append(data.html);
                    }

                    loaded_booking = loaded_booking+data.loaded_items;

                    if(loaded_booking >= data.pagination.count){
                        $("#view-booking .load-more").addClass('hidden');
                    }else {
                        $("#view-booking .load-more").attr("data-page",data.pagination.nextpage);
                        $("#view-booking .load-more").removeClass('hidden');
                    }

                    if(data.pagination.count === 0){
                        $("#view-booking .no-result").removeClass('hidden');
                    }

                    booking_click_handle();
                }
            }
        });

    }


    $("#view-booking .load-more").on('click',function () {
        let page = parseInt($(this).attr("data-page"));
        load_booking(page,"");
        return false;
    });

    $("#view-booking #search").keyup(function (event) {
        let q = $(this).val();
        if(q.length>3){
            load_booking(1,q);
        }else if(q === ""){
            load_booking(1,"");
        }
    });


    function booking_click_handle() {

        $("#view-booking .item-booking").click(function (event) {
            let href = $(this).attr("href");
            document.location.href = href;
            return false;
        });

    }





</script>

<script>



</script>