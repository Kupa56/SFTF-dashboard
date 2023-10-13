<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Wallet_model extends CI_Model
{

    const AMOUNTS = array();

    public function __construct()
    {
        parent::__construct();


    }

    public function getTopUp()
    {
        $val = ConfigManager::getValue("WALLET_TOP_UP_AMOUNTS");
        $val = explode(",", $val);
        return $val;
    }

    public function autoRenew($user_id)
    {

        $balance = $this->getBalance($user_id);

        $invoice = $this->mPaymentModel->getInvoice_by_user_id($user_id, 0);

        if ($invoice == NULL)
            return FALSE;

        if ($balance >= $invoice->amount) {//release balance

            $result = $this->releaseBalanceTransaction(
                SessionManager::getData("id_user"),
                $invoice->amount
            );

            if (!$result)
                return FALSE;


            $key = md5("abc-key" . $invoice->id);
            $result = $this->mPaymentModel->updateInvoice(
                $invoice->id,
                "wallet",
                "wallet:" . $result,
                $key,
                FALSE
            );

            if ($result)
                return TRUE;

        }

        return FALSE;
    }

    public function create_invoice($user_id, $amount)
    {

        $items = array();

        $items[] = array(
            'item_id' => $user_id,
            'item_name' => "Add balance of %s",
            'price' => $amount,
            'qty' => 1,
            'unit' => 'item',
            'price_per_unit' => $amount,
        );

        if ($amount == 0)
            return array(Tags::SUCCESS => 0);

        $this->db->where('user_id', $user_id);
        $no = $this->db->count_all_results('invoice');
        $no++;

        $data = array(
            "method" => "",
            "amount" => $amount,
            "no" => $no,
            "module" => "wallet",
            "module_id" => $user_id,
            "tax_id" => 0,
            "items" => json_encode($items, JSON_FORCE_OBJECT),
            "currency" => PAYMENT_CURRENCY,
            "status" => 0,
            "user_id" => $user_id,
            "transaction_id" => "",
            "created_at" => date("Y-m-d H:i:s", time())
        );


        $this->db->where('module', 'wallet');
        $this->db->where('user_id', $user_id);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();

        if (!isset($invoice[0])) {

            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['updated_at'] = date("Y-m-d H:i:s", time());

            $this->db->insert('invoice', $data);
            $id = $this->db->insert_id();

        } else {
            $this->db->where('id', $invoice[0]->id);
            $this->db->update('invoice', $data);
            $id = $invoice[0]->id;
        }


        return array(Tags::SUCCESS => 1, Tags::RESULT => $id);

    }

    public function sendMoneyAdmin($to, $amount)
    {

        $receiverUserData = $this->mUserModel->findUserByEmail($to);
        if ($receiverUserData == NULL)
            throw new Exception("Receiver undefined");

        //add money to receiver wallet
        $wallet_id = $this->add_Balance($receiverUserData['id_user'], $amount);

        $transactionId = time() . "-" . date("dmy");
        $this->createWalletTransaction($transactionId, $receiverUserData['id_user'], "receive", $amount);

        return array(Tags::SUCCESS => 1);
    }

    public function verifyAndSend($from, $to, $amount)
    {

        $senderUserData = $this->mUserModel->findUserByEmail($from);
        if ($senderUserData == NULL)
            throw new Exception("Sender undefined");


        $receiverUserData = $this->mUserModel->findUserByEmail($to);
        if ($receiverUserData == NULL)
            throw new Exception("Receiver undefined");

        if ($receiverUserData['status'] == -1)
            throw new Exception("Receiver is disabled");


        if ($receiverUserData['confirmed'] == 0)
            throw new Exception("Receiver didn't verified his email");


        //release money from sennder wallet
        $released = $this->releaseBalance($senderUserData['id_user'], $amount);

        if (!$released)
            throw new Exception("Insufficient funds. Please use the 'Top-up' feature to add more funds.");

        //add money to receiver wallet
        $wallet_id = $this->add_Balance($receiverUserData['id_user'], $amount);

        $transactionId = time() . "-" . date("dmy");

        //register new transaction
        $this->createWalletTransaction($transactionId, $senderUserData['id_user'], "send", $amount);
        $this->createWalletTransaction($transactionId, $receiverUserData['id_user'], "receive", $amount);


        //send notifications
        $this->sendTransactionNotification(
            $receiverUserData['id_user'],
            $senderUserData['id_user'],
            Currency::parseCurrencyFormat($amount, ConfigManager::getValue("DEFAULT_CURRENCY")),
            $transactionId
        );

        return array(Tags::SUCCESS => 1,Tags::RESULT=>$transactionId);
    }


    private function createWalletTransaction($no, $user_id, $operation, $amount)
    {


        $this->db->insert('wallet_transaction', array(
            'amount' => $amount,
            'currency' => ConfigManager::getValue("DEFAULT_CURRENCY"),
            'no' => $no,
            'operation' => $operation,
            'user_id' => $user_id,
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return TRUE;

    }

    public function getSenderByTranId($tranID)
    {

        $this->db->select('user_id');
        $this->db->where('no', $tranID);
        $this->db->where('operation', 'send');
        $object = $this->db->get('wallet_transaction');
        $object = $object->result_array();

        if (!isset($object[0]['user_id']))
            return NULL;

        $sender = $this->mUserModel->getUserData($object[0]['user_id']);

        if ($sender == NULL)
            return NULL;

        return $sender;
    }

    public function getWalletTransactions($params = array())
    {

        if (!isset($params['page'])) {
            $page = 1;
        } else {
            $page = intval($params['page']);
        }

        if (!isset($params['limit'])) {
            $limit = 100;
        } else {
            $page = intval($params['limit']);
        }

        if (isset($params['user_id']) && $params['user_id'] > 0)
            $this->db->where('user_id', intval($params['user_id']));

        $count = $this->db->count_all_results("wallet_transaction");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if (isset($params['user_id']) && $params['user_id'] > 0)
            $this->db->where('user_id', intval($params['user_id']));

        $this->db->order_by("created_at desc, id desc");
        $this->db->from("wallet_transaction");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());
        $wallet_transactions = $this->db->get();
        $wallet_transactions = $wallet_transactions->result_array();

        foreach ($wallet_transactions as $key => $val) {
            $user = $this->mUserModel->getUserData($val['user_id']);
            $wallet_transactions[$key]['client'] = array(
                'username' => $user['username'],
                'email' => $user['email'],
                'name' => $user['name'],
            );
        }

        return array(
            Tags::SUCCESS => 1,
            Tags::RESULT => $wallet_transactions,
            Tags::PAGINATION => $pagination
        );

    }

    public function sendTransactionNotification($receiverId, $senderId, $amount, $transactionId)
    {

        $receiverData = $this->mUserModel->getUserData($receiverId);
        $senderData = $this->mUserModel->getUserData($senderId);

        if ($receiverData == NULL && $senderData == NULL)
            return;

        $appLogo = _openDir(ConfigManager::getValue('APP_LOGO'));
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }


        $msg = "You received a payment of %s from %s";
        $msg = Translate::sprintf($msg, array($amount, $receiverData['name']));

        $body = $msg . "\n\nTo see all the transaction details, log in to your account.\n\nTransactionID: %s\n";
        $body = Translate::sprintf($body, array($transactionId));

        $messageText = Text::textParserHTML(array(
            "name" => $receiverData['name'],
            "imageUrl" => $imageUrl,
            "email" => ConfigManager::getValue('DEFAULT_EMAIL'),
            "appName" => strtolower(ConfigManager::getValue('APP_NAME')),
            "body" => nl2br($body),
        ), $this->load->view("mailing/templates/default.html", NULL, TRUE));


        $mail = new DTMailer();
        $mail->setRecipient($receiverData['email']);
        $mail->setFrom(ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setFrom_name(ConfigManager::getValue('APP_NAME'));
        $mail->setMessage($messageText);
        $mail->setReplay_to(ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setReplay_to_name(ConfigManager::getValue('APP_NAME'));
        $mail->setType("html");
        $mail->setSubject($msg);
        if ($mail->send()) {
            return FALSE;
        }

    }

    public function getBalance($user_id)
    {

        $this->db->where('user_id', $user_id);
        $wallet = $this->db->get('wallet', 1);
        $wallet = $wallet->result();

        if (isset($wallet[0])) {
            return $wallet[0]->balance;
        }

        return 0;
    }

    public function releaseBalance($user_id, $amount)
    {

        $this->db->where('user_id', $user_id);
        $this->db->where('balance >=', $amount);
        $wallet = $this->db->get('wallet', 1);
        $wallet = $wallet->result();


        if (isset($wallet[0])) {

            $this->db->where('id', $wallet[0]->id);
            $this->db->update('wallet', array(
                'balance' => $wallet[0]->balance - $amount,
                'updated_at' => date("Y-m-d H:i:s", time()),
            ));
            return TRUE;
        }

        return FALSE;
    }

    public function releaseBalanceTransaction($user_id, $amount)
    {

        $released = $this->releaseBalance($user_id, $amount);

        if (!$released)
            return $released;

        $transactionId = time() . "-" . date("dmy");
        //register new transaction
        $this->createWalletTransaction($transactionId, $user_id, "send", $amount);

        return $transactionId;
    }


    public function add_Balance($user_id, $amount)
    {

        $this->db->where('user_id', $user_id);
        $wallet = $this->db->get('wallet', 1);
        $wallet = $wallet->result();

        if (isset($wallet[0])) {

            $this->db->where('id', $wallet[0]->id);
            $this->db->update('wallet', array(
                'balance' => $wallet[0]->balance + $amount,
                'updated_at' => date("Y-m-d H:i:s", time()),
            ));

            return $wallet[0]->id;
        }

        $this->db->insert('wallet', array(
            'balance' => $amount,
            'user_id' => $user_id,
            'currency' => ConfigManager::getValue("DEFAULT_CURRENCY"),
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return $this->db->insert_id();
    }

    public function add_BalanceTransaction($user_id, $amount)
    {

        $this->add_Balance($user_id, $amount);

        $transactionId = time() . "-" . date("dmy");
        //register new transaction
        $this->createWalletTransaction($transactionId, $user_id, "top-up", $amount);


    }


    public function createTables()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'balance' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'currency' => array(
                'type' => 'VARCHAR(10)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
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
        $this->dbforge->create_table('wallet', TRUE, $attributes);


    }

    public function createTableWT()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'currency' => array(
                'type' => 'VARCHAR(10)',
                'default' => NULL
            ),
            'no' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'operation' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
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
        $this->dbforge->create_table('wallet_transaction', TRUE, $attributes);


    }


}