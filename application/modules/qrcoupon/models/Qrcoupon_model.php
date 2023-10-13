<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qrcoupon_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getCoupons($params=array())
    {

        //register offer
        NSModuleLinkers::newInstance('offer', 'getData', function ($args) {

            $params = array(
                "offer_id" => $args['id'],
                "limit" => 1,
            );

            $results = $this->mOfferModel->getOffers($params);

            if (isset($results[Tags::RESULT][0])) {

                $expires = 0;
                if($results[Tags::RESULT][0]['is_deal'] == 1
                    && strtotime($results[Tags::RESULT][0]['date_end'])  < time() ){
                    $expires = 1;
                }

                return array(
                    'label' => strip_tags(Text::output($results[Tags::RESULT][0]['name'])),
                    'label_description' => strip_tags(Text::output($results[Tags::RESULT][0]['description'])),
                    'image' => $results[Tags::RESULT][0]['images'],
                    'expires' => $expires,
                );
            }

            return NULL;
        });




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
                        $data[Tags::RESULT][$k]['expires'] = $result['expires'];
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

       return $data;

    }



    function remove($coupon_id,$user_id){

        $id = intval($coupon_id);
        $user_id = intval($user_id);

        $this->db->where('id',intval($id));
        $this->db->where('user_id',intval($user_id));
        $this->db->delete('coupons');

       return array(Tags::SUCCESS=> 1);
    }

    public function updateStatus($params = array())
    {

        $errors = array();
        $whereArray=array();

        if(isset($params['coupon_id']) && $params['coupon_id']>0){
            $whereArray['coupons.id'] = $params['coupon_id'];
        }else{
            $errors['coupon_id'] = _lang("Invalid coupon id");
        }


        if(isset($params['offer_id']) && $params['offer_id']>0){
            $whereArray['coupons.offer_id'] = $params['offer_id'];
        }else{
            $errors['offer_id'] = _lang("Invalid offer id");
        }

        if(isset($params['status']) && $params['status']>=-1 && $params['status']<=2){
            $data['status'] = $params['status'];
        }else{
            $errors['status'] = _lang("Invalid status ");
        }

        if(isset($params['business_user_id']) && $params['business_user_id']>0){
            $whereArray['store.user_id'] = $params['business_user_id'];
        }else{
            $errors['business_user_id'] = _lang("Invalid business id");
        }


        if(empty($errors)){

            $this->db->where($whereArray);
            $this->db->join("offer","offer.id_offer=coupons.offer_id");
            $this->db->join("store","store.id_store=offer.store_id");

            $count = $this->db->count_all_results("coupons");

            if($count == 0){
                $errors["err"] = _lang("Something wrong!");
                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
            }


            $this->db->where("id",$whereArray['coupons.id']);
            $this->db->join("offer","offer.id_offer=coupon.offer_id");
            $this->db->join("store","store.id_store=offer.store_id");

            $this->db->update("coupons",array(
                "status" =>  $data['status'],
                "updated_at" =>  date("Y-m-d H:i:s",time()),
            ));

            return array(Tags::SUCCESS=>1);

        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);


    }


    public function checkCouponOwner($coupon_code,$client_id,$business_user_id)
    {


        $this->db->where("coupons.code", $coupon_code);
        $this->db->where("coupons.user_id", $client_id);
        $this->db->where("store.user_id", $business_user_id);

        $this->db->join("offer","offer.id_offer=coupons.offer_id");
        $this->db->join("store","store.id_store=offer.store_id");
        $this->db->join("user","user.id_user=coupons.user_id");

        $this->db->select("coupons.*, user.name as 'user_coupon', store.name as 'store_name', store.id_store as 'store_id'");
        $this->db->from("coupons");
        $this->db->limit(1);

        $this->db->order_by("coupons.id", "DESC");

        $result = $this->db->get();
        $result = $result->result_array();

        return array(Tags::SUCCESS => 1, Tags::RESULT => $result);
    }

        public function getOfferCoupons($params = array(),$whereArray=array())
    {

        extract($params);

        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($coupon_id) && $coupon_id>0)
            $this->db->where("coupons.id", $coupon_id);


        if (isset($user_id) && $user_id>0)
            $this->db->where("coupons.user_id", $user_id);

        if (isset($status) and $status >= -1)
            $this->db->where("coupons.status", $status);

        if (isset($offer_id) and $offer_id >0)
            $this->db->where("coupons.offer_id", $offer_id);

        if (isset($business_user_id) and $business_user_id >0)
            $this->db->where("store.user_id", $business_user_id);


        if (!empty($whereArray))
            $this->db->where($whereArray);

        $this->db->join("offer","offer.id_offer=coupons.offer_id");
        $this->db->join("store","store.id_store=offer.store_id");
        $this->db->join("user","user.id_user=coupons.user_id");

        $count = $this->db->count_all_results("coupons");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if (isset($coupon_id) && $coupon_id>0)
            $this->db->where("coupons.id", $coupon_id);


        if (isset($user_id))
            $this->db->where("coupons.user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("coupons.status", $status);

        if (isset($offer_id) and $offer_id >0)
            $this->db->where("coupons.offer_id", $offer_id);

        if (isset($business_user_id) and $business_user_id >0)
            $this->db->where("store.user_id", $business_user_id);


        $this->db->join("offer","offer.id_offer=coupons.offer_id");
        $this->db->join("store","store.id_store=offer.store_id");
        $this->db->join("user","user.id_user=coupons.user_id");

        $this->db->select("coupons.*, user.name as 'user_coupon', store.name as 'store_name', store.id_store as 'store_id'");
        $this->db->from("coupons");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $this->db->order_by("coupons.id", "DESC");

        $payout = $this->db->get();
        $payout = $payout->result_array();

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $payout);
    }


    public function hasGotCoupon($offer_id,$user_id){

        $this->db->where('coupons.offer_id',$offer_id);
        $this->db->where('coupons.user_id',$user_id);
        $count =  $this->db->count_all_results('coupons');

        return $count>0?1:0;

    }

    public function redeemCoupon($offer_id,$user_id){

        $this->db->where('coupons.offer_id',$offer_id);
        $this->db->where('coupons.user_id',$user_id);
        $result =  $this->db->get('coupons',1);
        $result = $result->result_array();

        if(count($result) > 0)
            return $result[0]['code'];


        //get offer
        $this->db->where("id_offer",$offer_id);
        $this->db->where("coupon_config !=","disabled");
        $offer = $this->db->get('offer',1);
        $offer = $offer->result_array();

        if(!isset($offer[0]))
            return FALSE;

        $this->db->insert('coupons',array(
            'offer_id' => $offer[0]['id_offer'],
            'label' => $offer[0]['name'],
            'user_id' => $user_id,
            'code' => $offer[0]['coupon_code'],
            'status' => 0,
            'updated_at' => date("Y-m-d H:i:s",time()),
            'created_at' => date("Y-m-d H:i:s",time()),
        ));

        //send notification to the email
        $data = "coupon".":".$offer[0]['coupon_code'].":".$user_id;
        $data = base64_encode($data);
        $qrcodeImage = site_url('uploader/client_qrcode?data='.$data);

        $subject = Translate::sprintf("Congratulation you've got new coupon for '%s'",array( $offer[0]['name']));
        $body =  $subject."\n\n";
        $body .= "<center>".Translate::sprintf("Code").":<b>".$offer[0]['coupon_code']."</b>\n\n";
        $body .= "<img style=' width: 200px;' src='".$qrcodeImage."'/>"."</center>";
        $body .= "\n";
        $body .= Translate::sprintf("You can use %s application to see your generated coupons",array(ConfigManager::getValue("APP_NAME")));


        $this->mMailer->sendSimpleNotification($user_id, $body,$subject);

        return $offer[0]['coupon_code'];
    }




    public function getGeneratedCouponsCount($offer_id)
    {

        $this->db->where('coupons.offer_id',$offer_id);
        return $this->db->count_all_results('coupons');

    }


    public function createTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'offer_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'user_id' => array(
                'type' => 'INT',
                'default' => 0
            ),
            'label' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'code' => array(
                'type' => 'VARCHAR(120)',
                'default' => NULL
            ),
            'status' => array(
                'type' => 'INT',
                'default' => 0 //0, 1, -1
            ),
            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('coupons', TRUE, $attributes);

    }

    public function updateOfferFields()
    {

        if (!$this->db->field_exists('coupon_config', 'offer')) {
            $fields = array(
                'coupon_config' => array('type' => 'VARCHAR(30)', 'after' => 'tags', 'default' => Qrcoupon::COUPON_DISABLED),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('coupon_redeem_limit', 'offer')) {
            $fields = array(
                'coupon_redeem_limit' => array('type' => 'INT', 'after' => 'tags', 'default' => -1),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('coupon_code', 'offer')) {
            $fields = array(
                'coupon_code' => array('type' => 'VARCHAR(6)', 'after' => 'tags', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

    }

}




