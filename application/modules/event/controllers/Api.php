<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of event_webservice
 *
 * @author idriss
 */
class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("event/event_model", "mEventModel");

    }


    public function saveEventBK()
    {


        /*///////////////////////////////////////////////////////////////
          * //////////////////////////////////////////////////////////////
          * ncrytation data developped by amine
          *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $user_id = trim(RequestInput::post("user_id"));
        $event_id = Security::decrypt(RequestInput::post("event_id"));


        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $event_id,
            'module' => "event",
        );


        if($user_id>0){
            $params['guest_id'] = $this->mUserModel->getGuestIDByUserId($user_id);
        }


        if (!BookmarkManager::exist($params))
            $data["first_time"] = 1;
        else {
            $data["first_time"] = 0;
        }

        $data = BookmarkManager::add($params);

        echo json_encode($data);


    }


    public function removeEventBK()
    {

        $user_id = trim(RequestInput::post("user_id"));
        $event_id = Security::decrypt(RequestInput::post("event_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $event_id,
            'module' => "event",
        );


        $data = BookmarkManager::remove($params);

        echo json_encode($data);

    }


    public function getEvents()
    {


        /*///////////////////////////////////////////////////////////////
       * //////////////////////////////////////////////////////////////
       * ncrytation data developped by amine
       *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $order_by = RequestInput::post("order_by");
        $category_id = RequestInput::post("category_id");

        $latitude = doubleval(RequestInput::post("latitude"));
        $longitude = doubleval(RequestInput::post("longitude"));

        $event_id = intval(RequestInput::post("event_id"));
        $search = RequestInput::post("search");
        $mac_adr = RequestInput::post("mac_adr");
        $event_ids = Security::decrypt(RequestInput::post("event_ids"));
        $radius = RequestInput::post("radius");
        $date = RequestInput::post("date");
        $timezone = RequestInput::post("timezone");

        $is_featured = intval(Security::decrypt(RequestInput::post("is_featured")));

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "event_id" => $event_id,
            "event_ids" => $event_ids,
            "search" => $search,
            "status" => 1,
            "mac_adr" => $mac_adr,
            "order_by" => $order_by,
            "radius" => $radius,
            "date_end" => $date,
            "is_featured" => $is_featured,
            "category_id" => $category_id,
        );

        $data = $this->mEventModel->getEvents($params, NULL, function ($params) {
            //HIDE Expired events
            if ((!empty($params['device_date']) && $params['device_date'] != "") && (!empty($params['device_timzone']) && $params['device_timzone'] != "")) {

                $device_date = $params['device_date'];
                $device_timzone = $params['device_timzone'];
                $device_date_to_utc = MyDateUtils::convert($device_date, $device_timzone, "UTC", "Y-m-d H:i:s");
                $this->db->where("event.date_e >=", $device_date_to_utc);

            }
        });


        if ($data[Tags::SUCCESS] == 1) {

            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));
        } else {

            echo json_encode($data);
        }

    }


}
