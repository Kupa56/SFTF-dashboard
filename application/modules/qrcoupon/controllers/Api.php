<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{


    public function __construct()
    {
        parent::__construct();
    }

    function remove()
    {

        $id = intval(RequestInput::post("id"));
        $user_id = intval(RequestInput::post("user_id"));
        $result = $this->mQrcouponModel->remove($id, $user_id);
        echo json_encode($result);
        return;

    }


    public function checkCoupon()
    {

        $coupon_code = RequestInput::post("coupon_code");
        $client_id = RequestInput::post("client_id");
        $business_user_id =  RequestInput::post("business_user_id");

        if (!GroupAccess::isGrantedUser($business_user_id,'qrcoupon',GRP_MANAGE_QRCOUPONS_KEY)){
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $result = $this->mQrcouponModel->checkCouponOwner($coupon_code,$client_id,$business_user_id);

        echo json_encode($result,JSON_FORCE_OBJECT);

    }


    public function updateStatus()
    {

        $coupon_id =  intval(RequestInput::post("id"));
        $status =  intval(RequestInput::post("status"));
        $business_user_id =  intval(RequestInput::post("business_user_id"));
        $offer_id =  intval(RequestInput::post("offer_id"));


        if (!GroupAccess::isGrantedUser($business_user_id,'qrcoupon', GRP_MANAGE_QRCOUPONS_KEY)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $result = $this->mQrcouponModel->updateStatus(array(
            "coupon_id" =>$coupon_id,
            "status" =>$status,
            "business_user_id" =>$business_user_id,
            "offer_id" =>$offer_id,
        ));


        echo json_encode($result);

    }

    public function getCoupons()
    {

        $this->register_modules();

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $user_id = RequestInput::post("user_id");
        $coupon_code = RequestInput::post("coupon_code");
        $status = RequestInput::post("status");

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "user_id" => $user_id,
            "coupon_code" => $coupon_code,
            "status" => $status
        );


        $data = $this->mQrcouponModel->getOfferCoupons($params);

        //get updated data using a callback

        if ($data[Tags::COUNT] > 0) {

            foreach ($data[Tags::RESULT] as $k => $object) {

                $callback = NSModuleLinkers::find("offer", 'getData');

                if ($callback != NULL) {

                    $params = array(
                        'id' => $object['offer_id']
                    );

                    $result = call_user_func($callback, $params);

                    if ($result != NULL) {
                        $data[Tags::RESULT][$k]['label'] = $result['label'];
                        $data[Tags::RESULT][$k]['label_description'] = $result['label_description'];
                        $data[Tags::RESULT][$k]['image'] = $result['image'];
                    } else {
                        $data[Tags::RESULT][$k]['label'] = "";
                        $data[Tags::RESULT][$k]['label_description'] = "";
                        $data[Tags::RESULT][$k]['image'] = "";
                    }

                } else {
                    $data[Tags::RESULT][$k]['label'] = "";
                    $data[Tags::RESULT][$k]['label_description'] = "";
                    $data[Tags::RESULT][$k]['image'] = "";
                }

            }

        }

        if ($data[Tags::SUCCESS] == 1) {
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));
        } else {

            echo json_encode($data);
        }

    }


    public function register_modules()
    {

        //offer
        NSModuleLinkers::newInstance('offer', 'getData', function ($args) {

            $params = array(
                "offer_id" => $args['id'],
                "limit" => 1,
            );

            $stores = $this->mOfferModel->getOffers($params);

            if (isset($stores[Tags::RESULT][0])) {

                return array(
                    'label' => strip_tags(Text::output($stores[Tags::RESULT][0]['name'])),
                    'label_description' => strip_tags(Text::output($stores[Tags::RESULT][0]['description'])),
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });
    }

    public function getCouponCode()
    {

        $user_id = trim(RequestInput::post("user_id"));
        $offer_id = Security::decrypt(RequestInput::post("offer_id"));

        $result = $this->mQrcouponModel->redeemCoupon($offer_id, $user_id);

        if ($result == FALSE) {
            $result = array(Tags::SUCCESS => 0);
        } else {
            $result = array(Tags::SUCCESS => 1, Tags::RESULT => $result);
        }

        echo json_encode($result);

    }


}