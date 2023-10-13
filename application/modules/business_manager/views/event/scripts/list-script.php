<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>

    load_events(1,"");

    var loaded_events = 0;
    function load_events(page,q) {

        $.ajax({
            url: "<?=  site_url("business_manager/ajax/my_events")?>",
            data: {
                "page": page,
                "q": q,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $('#view-events .loading').removeClass('hidden');
                $("#view-events .preloader").removeClass('hidden');

                $("#view-events .load-more").addClass('hidden');
                $("#view-events .no-result").addClass('hidden');


            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);

                $('#view-events .loading').addClass('hidden');
                $("#view-events .preloader").addClass('hidden');

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                $('#view-events .loading').addClass('hidden');
                $("#view-events .preloader").addClass('hidden');

                if(data.success===1){


                    if(page === 1){
                        loaded_events = 0;
                        $('#view-events ul.post-list').html(data.html);
                    }else {
                        $('#view-events ul.post-list').append(data.html);
                    }

                    loaded_events = loaded_events+data.loaded_items;

                    if(loaded_events >= data.pagination.count){
                        $("#view-events .load-more").addClass('hidden');
                    }else {
                        $("#view-events .load-more").attr("data-page",data.pagination.nextpage);
                        $("#view-events .load-more").removeClass('hidden');
                    }

                    if(data.pagination.count === 0){
                        $("#view-events .no-result").removeClass('hidden');
                    }

                    event_click_handle();
                }
            }
        });

    }


    $("#view-events .load-more").on('click',function () {
        let page = parseInt($(this).attr("data-page"));
        load_events(page,"");
        return false;
    });

    $("#view-events #search").keyup(function (event) {
        let q = $(this).val();
        if(q.length>3){
            load_events(1,q);
        }else if(q === ""){
            load_events(1,"");
        }
    });


    function event_click_handle() {

        $("#view-events .item-event").click(function (event) {
            let href = $(this).attr("href");
            document.location.href = href;
            return false;
        });

    }





</script>

<script>



</script>