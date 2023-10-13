<script>


    var currentPage  = <?=intval(RequestInput::get("page"))?>;
    loadDiscussion(<?=intval(RequestInput::get("page"))?>,true);

    function loadDiscussion(page,refreshing) {

        currentPage = page;

        var dataSet = {
            "page" : page
        <?php
            if(Text::checkUsernameValidate(RequestInput::get("u"))){
                echo ',"username":"'.urlencode(RequestInput::get("u")).'"';
            }
            ?>
        };
        $.ajax({
            url:"<?=  site_url("ajax/messenger/loadInbox")?>",
            data:dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                if(refreshing==true)
                    $(".inbox-loading").removeClass("hidden");

            },error: function (request, status, error) {

                console.log(request);

                $(".inbox-loading").addClass("hidden");

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                $(".inbox-loading").addClass("hidden");

                if(data.success===1){
                    if(page==1 || page==0)
                        $("#discussion-list").html("");

                    $("#discussion-list").append(data.result.discussions_view);
                    $("#pagination").html(data.result.pagination_view);

                    doPagin();
                }

            }
        });


    }

    setInterval(function () {

    },1500);

    $("#reload-inbox").on('click',function () {
        loadDiscussion(1,true);
        return false;
    });

    function doPagin() {
        $("#messages-module #pagination a[href]").on('click',function () {
            return false;
        });
    }

</script>