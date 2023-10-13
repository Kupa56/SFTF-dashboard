<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>

    load_offers(1,"");

    var loaded_offers = 0;
    function load_offers(page,q) {

        $.ajax({
            url: "<?=  site_url("business_manager/ajax/my_offers")?>",
            data: {
                "page": page,
                "q": q,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $('#view-offers .loading').removeClass('hidden');
                $("#view-offers .preloader").removeClass('hidden');

                $("#view-offers .load-more").addClass('hidden');
                $("#view-offers .no-result").addClass('hidden');


            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);

                $('#view-offers .loading').addClass('hidden');
                $("#view-offers .preloader").addClass('hidden');

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                $('#view-offers .loading').addClass('hidden');
                $("#view-offers .preloader").addClass('hidden');

                if(data.success===1){


                    if(page === 1){
                        loaded_offers = 0;
                        $('#view-offers ul.post-list').html(data.html);
                    }else {
                        $('#view-offers ul.post-list').append(data.html);
                    }

                    loaded_offers = loaded_offers+data.loaded_items;

                    if(loaded_offers >= data.pagination.count){
                        $("#view-offers .load-more").addClass('hidden');
                    }else {
                        $("#view-offers .load-more").attr("data-page",data.pagination.nextpage);
                        $("#view-offers .load-more").removeClass('hidden');
                    }

                    if(data.pagination.count === 0){
                        $("#view-offers .no-result").removeClass('hidden');
                    }

                    offer_click_handle();
                }
            }
        });

    }


    $("#view-offers .load-more").on('click',function () {
        let page = parseInt($(this).attr("data-page"));
        load_offers(page,"");
        return false;
    });

    $("#view-offers #search").keyup(function (offer) {
        let q = $(this).val();
        if(q.length>3){
            load_offers(1,q);
        }else if(q === ""){
            load_offers(1,"");
        }
    });


    function offer_click_handle() {

        $("#view-offers .item-offer").click(function (offer) {
            let href = $(this).attr("href");
            document.location.href = href;
            return false;
        });

    }





</script>

<script>



</script>