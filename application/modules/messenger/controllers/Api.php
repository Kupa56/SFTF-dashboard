<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model","mUserModel");
        $this->load->model("messenger/messenger_model","mMessengerModel");

    }

    public function loadDiscussion(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
         * //////////////////////////////////////////////////////////////
         * ncrytation data developped by amine
         *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $sender_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $status = intval(Security::decrypt(RequestInput::post("status")));
        $page = intval(Security::decrypt(RequestInput::post("page")));

        $params = array(
            "sender_id"     =>$sender_id,
            "status"        =>$status,
            "page"          =>$page,
        );

        $data = $this->mMessengerModel->loadDiscussion($params);

        $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);

        echo json_encode($data,JSON_FORCE_OBJECT);

    }




    public function inboxLoaded(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
        * //////////////////////////////////////////////////////////////
        * ncrytation data developped by amine
        *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////



        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $messagesIds = Security::decrypt(RequestInput::post("messagesIds"));
        $status = Security::decrypt(RequestInput::post("status"));

        $this->load->model("Messenger/MessengerModel","mMessengerModel");

        $params = array(
            "user_id"  =>$user_id,
            "messagesIds"  =>$messagesIds,
            "status"=>$status
        );

        echo json_encode($this->mMessengerModel->inboxLoaded($params),JSON_FORCE_OBJECT);

    }

    public function sendMessage(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
        * //////////////////////////////////////////////////////////////
        * ncrytation data developped by amine
        *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $sender_id = intval(Security::decrypt(RequestInput::post("sender_id")));
        $receiver_id = intval(Security::decrypt(RequestInput::post("receiver_id")));
        $status = intval(Security::decrypt(RequestInput::post("type")));
        $content = Security::decrypt(RequestInput::post("content"));
        $messageId = Security::decrypt(RequestInput::post("messageId"));


        $params = array(
            "sender_id"    =>$sender_id,
            "receiver_id"  =>$receiver_id,
            "content"      =>$content,
            "type"         =>$status,
            "messageId"    => $messageId
        );




        echo json_encode($this->mMessengerModel->sendMessage($params),JSON_FORCE_OBJECT);

    }


    public function markMessagesAsSeen(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
        * //////////////////////////////////////////////////////////////
        * ncrytation data developped by amine
        *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $discussionId = intval(Security::decrypt(RequestInput::post("discussionId")));

        $this->load->model("Messenger/MessengerModel","mMessengerModel");

        $params = array(
            "user_id"    =>$user_id,
            "discussionId"  =>$discussionId
        );


        echo json_encode($this->mMessengerModel->markMessagesAsSeen($params),JSON_FORCE_OBJECT);

    }


    public function markMessagesAsLoaded(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
        * //////////////////////////////////////////////////////////////
        * ncrytation data developped by amine
        *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $discussionId = intval(Security::decrypt(RequestInput::post("discussionId")));

        $params = array(
            "user_id"    =>$user_id,
            "discussionId"  =>$discussionId
        );


        echo json_encode($this->mMessengerModel->markMessagesAsLoaded($params),JSON_FORCE_OBJECT);

    }



    public function loadMessages(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
         * //////////////////////////////////////////////////////////////
         * ncrytation data developped by amine
         *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////



        $discussion_id = intval(Security::decrypt(RequestInput::post("discussion_id")));
        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $receiver_id = intval(Security::decrypt(RequestInput::post("receiver_id")));

        $page = intval(Security::decrypt(RequestInput::post("page")));
        $status = intval(Security::decrypt(RequestInput::post("status")));
        $date = Security::decrypt(RequestInput::post("date"));
        $lastMessageId =intval(Security::decrypt(RequestInput::post("last_id")));


        $params = array(
            "discussion_id"  =>$discussion_id,
            "status"  =>$status,
            "page"  =>$page,
            "user_id"  =>$user_id,
            "receiver_id"  =>$receiver_id,
            "date"  => $date,
            "lastMessageId" => $lastMessageId
        );


        $data = $this->mMessengerModel->loadMessages($params);

        if($data[Tags::SUCCESS]==1){

            //decode text
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);

            echo json_encode(array(Tags::SUCCESS=>1,Tags::COUNT=>$data[Tags::COUNT],Tags::RESULT=>$data[Tags::RESULT]),JSON_FORCE_OBJECT);

        }else{

            echo json_encode($data);
        }


    }

    public function loadInbox(){

        $this->requireAuth();

        /*///////////////////////////////////////////////////////////////
         * //////////////////////////////////////////////////////////////
         * ncrytation data developped by amine
         *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $status = intval(Security::decrypt(RequestInput::post("status")));

        $params = array(
            "user_id"  =>$user_id,
            "status"  =>$status
        );

        echo json_encode($this->mMessengerModel->loadInbox($params),JSON_FORCE_OBJECT);

    }

    public function countMessages(){

        $user_id = intval(Security::decrypt(RequestInput::post("user_id")));
        $result = $this->mMessengerModel->countMessagesNoSeen($user_id);

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$result[Tags::COUNT]),JSON_FORCE_OBJECT);

    }



    public function register(){

        $name = Security::decrypt(RequestInput::post("name"));
        $email = Security::decrypt(RequestInput::post("email"));
        $regId = Security::decrypt(RequestInput::post("regId"));


        $params = array(
            "name"  =>$name,
            "email"  =>$email,
            "regId"  =>$regId
        );


        echo json_encode($this->mMessengerModel->register($params));

    }

    public function sendNotification(){


        /*///////////////////////////////////////////////////////////////
          * //////////////////////////////////////////////////////////////
          * ncrytation data developped by amine
          *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////


        $rId = Security::decrypt(RequestInput::post("registerId"));


        $params = array(
            "registerId"  =>$rId,
        );


        echo json_encode($this->mMessengerModel->send_notification($params));

    }

}