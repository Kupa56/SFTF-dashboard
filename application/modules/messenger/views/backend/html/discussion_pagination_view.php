<div class="pull-right">

<?php


        $username = RequestInput::get("u");

        echo $pagination->links(array(
            "u"    => $username,
        ),admin_url("messenger/messages"));


    ?>

</div>




