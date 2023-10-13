<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {



    public function __construct(){
        parent::__construct();
        //load model


        $this->load->model("nshistoric/notification_history_model","mHistoric");

    }

    public function turnOff(){


        $id = intval(RequestInput::post("id"));

        //id & status
        $data =  $this->mHistoric->remove($id);

        echo json_encode($data);return;

    }


    public function remove(){


        $id = intval(RequestInput::post("id"));

        //id & status
        $data =  $this->mHistoric->remove($id);

        echo json_encode($data);return;

    }

    public function changeStatus(){


        $id = intval(RequestInput::post("id"));
        $status = intval(RequestInput::post("status"));

        //id & status

        $data =  $this->mHistoric->changeStatus($id,$status);

        if($status == 1){

            $notification = $this->mHistoric->getNotification($id);
            if($notification != NULL){
                ActionsManager::add_action('nshistoric','read_notification',$notification);
            }

        }


        echo json_encode($data);return;

    }

    public function getCount(){

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));

        $device_date = RequestInput::post("date");
        $device_timzone = RequestInput::post("timezone");


        $auth_type = RequestInput::post("auth_type");
        $auth_id = RequestInput::post("auth_id");
        $status = RequestInput::post("status");


        $user_id = RequestInput::post("user_id");
        $guest_id = RequestInput::post("guest_id");

        $params = array(

            //single user guest or logged user
            "auth_type"         =>      $auth_type,
            "auth_id"           =>      $auth_id,

            //both users
            "user_id"           =>      $user_id,
            "guest_id"           =>     $guest_id,

            "status"            =>      $status,
            "limit"             =>      $limit,
            "page"              =>      $page,
            "device_date"       =>      $device_date,
            "device_timezone"   =>      $device_timzone,
        );

        $data =  $this->mHistoric->getCount($params);

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data[Tags::COUNT],"dd"=>$data[Tags::RESULT]));return;

    }


    public function countUnseenNotification(){



        $auth_type = RequestInput::post("auth_type");
        $auth_id = RequestInput::post("auth_id");

        $params = array(
            "auth_type"         =>      $auth_type,
            "auth_id"           =>      $auth_id
        );

        $data =  $this->mHistoric->countUnseenNotification($params);

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data));return;

    }

    private function register_modules(){


        NSModuleLinkers::newInstance('store','getData',function ($args){

            $params = array(
                "store_id" => $args['id'],
                "limit" => 1,
            );

            $stores =  $this->mStoreModel->getStores($params);

            if(isset($stores[Tags::RESULT][0])){

                return array(
                    'label' => strip_tags(Text::output($stores[Tags::RESULT][0]['name'])),
                    'label_description' => strip_tags(Text::output($stores[Tags::RESULT][0]['detail'])),
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });

        //event
        NSModuleLinkers::newInstance('event','getData',function ($args){

            $params = array(
                "event_id" => $args['id'],
                "limit" => 1,
            );

            $stores =  $this->mEventModel->getEvents($params);

            if(isset($stores[Tags::RESULT][0])){

                return array(
                    'label' =>   strip_tags(Text::output($stores[Tags::RESULT][0]['name'])),
                    'label_description' =>  strip_tags(Text::output($stores[Tags::RESULT][0]['description'])),
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });

        //offer
        NSModuleLinkers::newInstance('offer','getData',function ($args){

            $params = array(
                "offer_id" => $args['id'],
                "limit" => 1,
            );

            $stores =  $this->mOfferModel->getOffers($params);

            if(isset($stores[Tags::RESULT][0])){

                return array(
                    'label' =>   strip_tags(Text::output($stores[Tags::RESULT][0]['name'])),
                    'label_description' =>  strip_tags(Text::output($stores[Tags::RESULT][0]['description'])),
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });

        //booking
        NSModuleLinkers::newInstance('booking','getData',function ($args){

            $params = array(
                "id" => $args['id'],
                "limit" => 1,
            );

            $results =  $this->mBookingModel->getBookings($params);


            if(isset($results[Tags::RESULT][0])){

                return array(
                    'image' => array(_openDir($results[Tags::RESULT][0]['store_images'])),
                );
            }

            return NULL;
        });
    }


    public function getNotifications(){

        $this->register_modules();

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));

        $auth_type = RequestInput::post("auth_type");
        $auth_id = RequestInput::post("auth_id");

        $device_date = RequestInput::post("date");
        $device_timzone = RequestInput::post("timezone");

        $user_id = RequestInput::post("user_id");
        $guest_id = RequestInput::post("guest_id");


        $params = array(
            "auth_type"         =>     $auth_type,
            "auth_id"           =>      $auth_id,

            //both users
            "user_id"           =>      $user_id,
            "guest_id"           =>      $guest_id,


            "limit"             =>      $limit,
            "page"              =>      $page,
            "device_date"       =>      $device_date,
            "device_timezone"   =>      $device_timzone,
        );


        $data =  $this->mHistoric->getNotifications($params);


        if($data[Tags::SUCCESS]==1 && $data[Tags::COUNT]>0){
            foreach ($data[Tags::RESULT] as $k => $obj){


                if($obj['image'] == null
                    or $obj['image']==""
                    or empty($obj['image'])){


                    $callback = NSModuleLinkers::find($obj['module'],'getData');

                    if($callback != NULL){

                        $params = array(
                            'id' => $obj['module_id']
                        );

                        $result = call_user_func($callback,$params);

                        if($obj != NULL){
                            $data[Tags::RESULT][$k]['image'] = $result['image'];
                        }else{
                            $data[Tags::RESULT][$k]['image'] = "";
                        }

                    }else{
                        $data[Tags::RESULT][$k]['image'] = "";
                    }

                }


            }
        }


        if($data[Tags::SUCCESS]==1){
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array(Tags::COUNT=>$data[Tags::COUNT]));
        }else{

            echo json_encode($data);
        }

    }


}