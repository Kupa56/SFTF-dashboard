<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("offer/offer_model", "mOfferModel");


    }


    public function removeBookmarkOffer()
    {

        $this->requireAuth();

        $user_id = trim(RequestInput::post("user_id"));
        $offer_id = Security::decrypt(RequestInput::post("offer_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $offer_id,
            'module' => "offer",
        );


        $data = BookmarkManager::remove($params);

        echo json_encode($data);

    }

    public function saveBookmarkOffer()
    {

        $user_id = trim(RequestInput::post("user_id"));
        $offer_id = Security::decrypt(RequestInput::post("offer_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $offer_id,
            'module' => "offer",
        );

        if ($user_id > 0) {
            $params['guest_id'] = $this->mUserModel->getGuestIDByUserId($user_id);
        }

        $data = BookmarkManager::add($params);

        echo json_encode($data);


    }


    public function getOffers()
    {


        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $order_by = RequestInput::post("order_by");
        //a proximite
        $latitude = doubleval(RequestInput::post("latitude"));
        $longitude = doubleval(RequestInput::post("longitude"));
        $store_id = intval(RequestInput::post("store_id"));
        $user_id = intval(RequestInput::post("user_id"));
        $category_id = intval(RequestInput::post("category_id"));
        $store_id = intval(RequestInput::post("store_id"));
        $offer_id = intval(RequestInput::post("offer_id"));
        $search = RequestInput::post("search");
        $lat = RequestInput::post("lat");
        $lng = RequestInput::post("lng");
        $mac_adr = RequestInput::post("mac_adr");
        $radius = RequestInput::post("radius");

        $offer_value = RequestInput::post("offer_value");
        $value_type = RequestInput::post("value_type");

        $is_featured = intval(Security::decrypt(RequestInput::post("is_featured")));



        $params = array(
            "user_id"  =>$user_id,
            "limit"       =>$limit,
            "page"          =>$page,
            "category_id"   =>$category_id,
            "latitude"   => $latitude,
            "longitude"  => $longitude,
            "store_id"  => $store_id,
            "offer_value"  => $offer_value,
            "value_type"  => $value_type,
            "offer_id"  => $offer_id,
            "search"  => $search,
            "statusM"  => 1,
            "mac_adr"    => $mac_adr,
            "lat"    => $lat,
            "lng"    => $lng,
            "order_by"    => $order_by,
            "radius"    => $radius,
            /*"device_date"    => $device_date,
            "device_timzone"    => $device_timzone,*/
            "is_featured" => $is_featured,
        );



        $data = $this->mOfferModel->getOffers($params, NULL, function ($params) {

            //HIDE Expired offers

            if ((!empty($params['device_date']) && $params['device_date'] != "") && (!empty($params['device_timzone']) && $params['device_timzone'] != "")) {

                $device_date = $params['device_date'];
                $device_timzone = $params['device_timzone'];
                $device_date_to_utc = MyDateUtils::convert($device_date, $device_timzone, "UTC", "Y-m-d H:i:s");

                $this->db->group_start();
                $this->db->where("offer.date_end >=", $device_date_to_utc);
                $this->db->or_where("offer.is_deal = ", 0);
                $this->db->group_end();

                //Display only the offers at the date specified by the store owner
                if (OFFERS_IN_DATE) {
                    $this->db->where("offer.date_start <=", $device_date_to_utc);
                }

            }
        });

        if ($data[Tags::SUCCESS] == 1) {

            foreach ($data[Tags::RESULT] as $key => $article) {

                $p = $data[Tags::RESULT][$key]['description'];
                $p = Text::output($p);
                $p = $this->parse_mobile_html($p);

                $data[Tags::RESULT][$key]['description'] = $p;
                $data[Tags::RESULT][$key]['short_description'] = strip_tags(Text::output($p));
            }

            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);

            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));
        } else {

            echo json_encode($data);
        }

    }


    private function parse_mobile_html($str = "")
    {

        $p = Text::output($str);
        $p = preg_replace("/<\/?div[^>]*\>/i", "", $p);
        $p = str_replace('   ', '', $p);
        $p = str_replace("\n", '<br />', $p);
        //$p = str_replace('<br/>', '<br /><br />', $p);
        //$p = str_replace('<br />', '<br /><br />', $p);
        //$p = str_replace('<br>', '<br /><br />', $p);
        $p = str_replace('<p>', '', $p);
        $p = str_replace('</p>', '<br /><br />', $p);
        $p = str_replace('</h1>', '</h1><br />', $p);
        $p = str_replace('</h2>', '</h2><br />', $p);
        $p = str_replace('</h3>', '</h3><br />', $p);
        $p = str_replace('</h4>', '</h4><br />', $p);
        $p = str_replace('</h5>', '</h5><br />', $p);
        $p = str_replace('<li>', '&nbsp;&nbsp;<b>•</b>&nbsp;&nbsp;', $p);
        $p = str_replace('</li>', '<br/>', $p);
        $p = str_replace('<ul>', '', $p);
        $p = str_replace('</ul>', '<br/>', $p);
        $p = str_replace('<ol>', '', $p);
        $p = str_replace('</ol>', '<br/>', $p);

        $p = str_replace('<br /><br /><br />', '<br/>', $p);
        $p = str_replace('<br/><br/><br/>', '<br/>', $p);
        //$p = nl2br($p);

        return $p;
    }


}