<?php

    $image = "";

    if(isset($user['images'][0]['200_200']['url'])){
        $image = $user['images'][0]['200_200']['url'];
    }else{
        $image= adminAssets("images/profile_placeholder.png");
    }



?>

<!-- Message to the right -->
<div class="direct-chat-msg right">
    <div class="direct-chat-text">
            <?=Text::echo_output($object['content'])?>
    </div>
</div>
<div class="direct-chat-date right">
        <span>
      <?php

          $current = date("Y-m-d",time());
          $chatDate = date("Y-m-d",strtotime($object['created_at']));
          if($current != $chatDate){
              echo MyDateUtils::convert($object['created_at'],"UTC",TimeZoneManager::getTimeZone(),"Y-m-d H:i");
          }else{
              echo MyDateUtils::convert($object['created_at'],"UTC",TimeZoneManager::getTimeZone(),"H:i");
          }

          ?>
        </span>
</div>