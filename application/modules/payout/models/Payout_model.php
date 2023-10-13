<?php


class Payout_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    const MESSAGE_METHOD_NOT_SELECTED = "Payment method not selected";
    const MESSAGE_STATUS_NOT_SELECTED = "Payment status not selected";
    const MESSAGE_AMOUNT_NOT_SELECTED = "The amount is not filled in";


    public function getPayoutObject($id)
    {

        $this->db->where("id", intval($id));
        $p = $this->db->get("payouts", 1);
        $p = $p->result_array();

        if (isset($p[0]))
            return $p[0];

        return NULL;

    }


    public function getPayout($params = array(),$whereArray=array())
    {

        extract($params);


        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);

        if (isset($payout_id) and $payout_id > 0)
            $this->db->where("id", $payout_id);

        if (isset($transaction_id) and $transaction_id > 0)
            $this->db->where("id", $transaction_id);

        if (!empty($whereArray))
            $this->db->where($whereArray);

        $count = $this->db->count_all_results("payouts");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);

        if (isset($payout_id) and $payout_id > 0)
            $this->db->where("id", $payout_id);

        if (isset($transaction_id) and $transaction_id > 0)
            $this->db->where("id", $transaction_id);

        if (!empty($whereArray))
            $this->db->where($whereArray);

        $this->db->from("payouts");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        //$this->db->group_by("payouts.created_at", "DESC");

        if (isset($order_by_date) and $order_by_date == 1)
            $this->db->order_by("payouts.created_at", "DESC");
        else
            $this->db->order_by("payouts.created_at", "ASC");

        $payout = $this->db->get();
        $payout = $payout->result_array();

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $payout);
    }

    public function delete($id=0){

        $this->db->where('id', $id);
        $this->db->delete('payouts');

    }

    public function addPayout($params = array())
    {
        extract($params);

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }

        if (isset($amount) and doubleval($amount) > 0) {
            $data['amount'] = doubleval($amount);
        } else {
            $errors['amount'] = Translate::sprint(self::MESSAGE_AMOUNT_NOT_SELECTED);
        }

        if (isset($method) and $method != "") {
            $data['method'] = Text::input($method);
        } else {
            $errors['method'] = Translate::sprint(self::MESSAGE_METHOD_NOT_SELECTED);
        }

        if (isset($status) and $status != "") {
            $data['status'] = Text::input($status);
        } else {
            $errors['status'] = Translate::sprint(self::MESSAGE_STATUS_NOT_SELECTED);
        }

        if (isset($currency) and $currency != "") {
            $data['currency'] = Text::input($currency);
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($note) and $note != "") {
            $data['note'] = Text::inputWithoutStripTags($note);
        }


        if (empty($errors) and !empty($data)) {

            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['created_at'] = MyDateUtils::convert($data['created_at'], TimeZoneManager::getTimeZone(), "UTC");
            $data['updated_at'] = $data['created_at'];

            $this->db->insert("payouts", $data);

            $payout_id = $this->db->insert_id();

            return array(Tags::SUCCESS => 1, Tags::RESULT => $payout_id, "url" => admin_url("payout/payouts"));

        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }

    public function editPayout($params = array())
    {
        extract($params);

        if (isset($id) and $id > 0) {
            $data['id'] = intval($id);
        } else {
            $errors['id'] = _lang("ID is missing!");
        }

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }

        if (isset($method) and $method != "") {
            $data['method'] = Text::input($method);
        } else {
            $errors['method'] = Translate::sprint(self::MESSAGE_METHOD_NOT_SELECTED);
        }

        if (isset($status) and $status != "") {
            $data['status'] = Text::input($status);
        } else {
            $errors['status'] = Translate::sprint(self::MESSAGE_STATUS_NOT_SELECTED);
        }

        if (isset($currency) and $currency != "") {
            $data['currency'] = Text::input($currency);
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($note) and $note != "") {
            $data['note'] = Text::inputWithoutStripTags($note);
        }


        if (empty($errors) and !empty($data)) {

            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['created_at'] = MyDateUtils::convert($data['created_at'], TimeZoneManager::getTimeZone(), "UTC");
            $data['updated_at'] = $data['created_at'];

            $this->db->where("id", $id);
            $this->db->update("payouts", $data);

            return array(Tags::SUCCESS => 1, "url" => admin_url("payout/payouts"));

        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }

    public function createPayoutsTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'method' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),

            'amount' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'currency' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),

            'note' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'VARCHAR(50)',
                'default' => NULL
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'transaction_id' => array(
                'type' => 'VARCHAR(60)',
                'default' => NULL
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
        $this->dbforge->create_table('payouts', TRUE, $attributes);

        //==========  Payment_transactions ==========/


    }


}