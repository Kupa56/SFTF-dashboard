<script>
    //scroll to bttom inbox
    scrollBottom();

    $("#next-page").on('click',function () {

        var nextpage = $("#next-page").attr("next-page");
        loadMessages(nextpage, "<?=$username?>");

        return false;
    });


    $("#send-message").on('click',function () {

        var selector = $("#send-message");
        var content = $("#message-content").val();

        if(content!=""){
            sendMessage(content,"<?=$username?>",selector);
        }

        return false;
    });

    function loadMessages(page,usrname) {

        var dataSet = {
            "page"          : page,
            "username"      : usrname,
            "date"          : "<?=time()?>",
        <?php if(isset($lastMessageId)): ?>
            "lastMessageId" : "<?=$lastMessageId?>"
        <?php endif; ?>
        };

        $.ajax({
            url:"<?=  site_url("ajax/messenger/loadMessages")?>",
            data:dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $("#next-page").addClass("hidden");
                $("#messenger-loading").removeClass("hidden");

            },error: function (request, status, error) {

                $("#next-page").removeClass("hidden");
                $("#messenger-loading").addClass("hidden");

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                $("#next-page").addClass("hidden");
                $("#messenger-loading").addClass("hidden");

                console.log(data);

                if(data.success===1){

                    scrollTop();

                    $(".html-message").prepend(data.result.messages_views);

                    $("#last-id").val(data.result.lastMessageId);

                    if(data.result.messages_pagination.nextpage>0){

                        $("#next-page").attr("next-page",data.result.messages_pagination.nextpage);
                        $("#next-page").removeClass("hidden");
                        $("#messenger-loading").addClass("hidden");

                    }

                }else if(data.success===0){

                }
            }
        });

    }

    function sendMessage(content,username,selector) {

    <?php
        $token = $this->mUserBrowser->setToken("sendMessageAJAX");
        ?>

        var dataSet = {
            "username"      : username,
            "content"       : content,
            "token"         : "<?=$token?>"
        };

        var nextpage = $("#next-page").attr("next-page");

        $.ajax({
            url:"<?=  site_url("ajax/messenger/sendMessage")?>",
            data:dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                selector.attr("disabled",true);

            },error: function (request, status, error) {

                selector.attr("disabled",false);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                selector.attr("disabled",false);
                console.log(data);

                if(data.success===1){

                    if(nextpage==-1)
                        $(".html-message .no-message").addClass("hidden");

                    $("#message-content").val("");


                    $(".html-message").append(data.result.message_view);
                    scrollBottom();

                    $("#last-id").val(data.result.lastMessageId);

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

    }



    function scrollBottom() {
        var objDiv = $(".direct-chat-messages");
        if (objDiv.length > 0){
            objDiv[0].scrollTop = objDiv[0].scrollHeight;
        }
    }

    function scrollTop() {

        var objDiv = $(".direct-chat-messages");
        if (objDiv.length > 0){
            objDiv[0].scrollTop = 0;
        }


    }

    markMessagesAsSeen();

    function markMessagesAsSeen() {

    <?php

        if(isset($messengerData[0]['discussion_id']))
            $diId = $messengerData[0]['discussion_id'];
        else
            $diId = 0;


        ?>

        $.ajax({
            url:"<?=  site_url("ajax/messenger/markMessagesAsSeen")?>",
            data:{
                "discussionId": <?=$diId?>
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {


            },error: function (request, status, error) {

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

            }
        });

    }


    function loadNewMessages(usrname) {

        var dataSet = {
            "page"          : 1,
            "username"      : usrname,
            "date"          : "<?=time()?>",
            "lastMessageId" : $("#last-id").val()
        };

        $.ajax({
            url:"<?=  site_url("ajax/loadNewMessages")?>",
            data:dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

            },error: function (request, status, error) {

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if(data.success===1){

                    $(".html-message").append(data.result.messages_views);
                    scrollBottom();

                    $("#last-id").val(data.result.lastMessageId);


                }else if(data.success===0){

                }
            }
        });

    }


    setInterval(function () {
    <?php
        if(Text::checkUsernameValidate($this->uri->segment(4))){
            echo 'loadNewMessages("'.$this->uri->segment(4).'");';
        }
        ?>
        loadDiscussion(currentPage,false);
    },15000);

</script>