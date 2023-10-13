<div class="col-sm-5 messenger">
    <!-- DIRECT CHAT PRIMARY -->
    <div class="box box-solid direct-chat direct-chat-primary">
        <div class="box-header with-border">
            <div class="pull-left">
                <div class="image-container-40 margin-right" style="background-image: url('<?=ImageManagerUtils::getImage($userData['images'],ImageManagerUtils::IMAGE_SIZE_200)?>');">
                    <img class="direct-chat-img invisible" src="<?=ImageManagerUtils::getImage($userData['images'],ImageManagerUtils::IMAGE_SIZE_200)?>" alt="Message User Image">
                </div>
                <strong class="box-title"><?=(isset($userData) && !empty($userData))?($userData['name']):_lang("Messenger")?></strong><br>
                <span class="box-sub-title"><?=(isset($userData) && !empty($userData))?("@".$userData['username']):_lang("")?></span><br>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages">
                <!-- Message. Default to the left -->

            <?php

                if (isset($messages_pagination)) {
                    $messages_pagination = json_encode($messages_pagination);
                    $messages_pagination = json_decode($messages_pagination, JSON_OBJECT_AS_ARRAY);
                }

                ?>

            <?php if (isset($messages_pagination['nextpage']) and $messages_pagination['nextpage'] > 0): ?>

                    <a href="#" id="next-page" next-page="<?= $messages_pagination['nextpage'] ?>"
                       class="load-more"><u><?= Translate::sprint("Load More") ?></u></a>
                    <a href="#" id="messenger-loading" class="load-more hidden"><i
                                class="fa fa-refresh fa-spin"></i></a>

            <?php else: ?>

                    <a href="#" id="next-page" next-page="-1"
                       class="load-more hidden"><u><?= Translate::sprint("Load More") ?></u></a>
                    <a href="#" id="messenger-loading" class="load-more hidden"><i
                                class="fa fa-refresh fa-spin"></i></a>

            <?php endif; ?>


                <div class="html-message">

                <?php

                    if ($messages_views != "") {
                        echo $messages_views;
                    } else {
                        echo "<div class='no-message'>" . Translate::sprint("No Message") . "</div>";
                    }


                    ?>

                </div>


                <!-- /.direct-chat-msg -->
            </div>
            <!--/.direct-chat-messages-->

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <form action="#" method="post">
                <div class="input-group">
                    <input type="text" id="message-content" name="message"
                           placeholder="<?= Translate::sprint("Type Message ...") ?>" class="form-control">
                    <span class="input-group-btn">
                        <button type="submit" id="send-message"
                                class="btn btn-primary btn-flat"><?= Translate::sprint("Send") ?></button>
                    </span>
                </div>
            </form>
        </div>

    <?php if (isset($lastMessageId)): ?>
            <input type="hidden" id="last-id" value="<?= $lastMessageId ?>"/>
    <?php else: ?>
            <input type="hidden" id="last-id" value=""/>
    <?php endif; ?>
        <!-- /.box-footer-->
    </div>
    <!--/.direct-chat -->
</div>

<?php

$data = array();
if (isset($messengerData))
    $data['messengerData'] = $messengerData;

$data['userId'] = $userData['id_user'];
$data['username'] = $userData['username'];

$script = $this->load->view("messenger/backend/html/scripts/messenger-script", $data, TRUE);


AdminTemplateManager::addScript($script);