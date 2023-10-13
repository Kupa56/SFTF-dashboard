<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>

    load_stores(1,"");

    var loaded_stores = 0;
    function load_stores(page,q) {

        $.ajax({
            url: "<?=  site_url("business_manager/ajax/my_stores")?>",
            data: {
                "page": page,
                "q": q,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $('#view-stores .loading').removeClass('hidden');
                $("#view-stores .preloader").removeClass('hidden');


                $("#view-stores .load-more").addClass('hidden');
                $("#view-stores .no-result").addClass('hidden');
            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);

                $('#view-events .loading').addClass('hidden');
                $("#view-events .preloader").addClass('hidden');

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                $('#view-stores .loading').addClass('hidden');
                $("#view-stores .preloader").addClass('hidden');


                if(data.success===1){

                    if(page === 1){
                        loaded_stores = 0;
                        $('#view-stores ul.post-list').html(data.html);
                    }else {
                        $('#view-stores ul.post-list').append(data.html);
                    }

                    loaded_stores = loaded_stores+data.loaded_items;

                    if(loaded_stores >= data.pagination.count){
                        $("#view-stores .load-more").addClass('hidden');
                    }else {
                        $("#view-stores .load-more").attr("data-page",data.pagination.nextpage);
                        $("#view-stores .load-more").removeClass('hidden');
                    }

                    if(data.pagination.count === 0){
                        $("#view-stores .no-result").removeClass('hidden');
                    }

                    store_click_handle();
                }
            }
        });

    }


    $("#view-stores .load-more").on('click',function () {
        let page = parseInt($(this).attr("data-page"));
        load_stores(page,"");
        return false;
    });

    $("#view-stores #search").keyup(function (event) {
        let q = $(this).val();
        if(q.length>3){
            load_stores(1,q);
        }else if(q === ""){
            load_stores(1,"");
        }
    });


    function store_click_handle() {

        $("#view-stores .item-store").click(function (event) {
            let href = $(this).attr("href");
            document.location.href = href;
            return false;
        });

    }





</script>

<script>



</script>