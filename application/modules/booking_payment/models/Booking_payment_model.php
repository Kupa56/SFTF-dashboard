<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Booking_payment_model extends CI_Model
{


    public function updateBookingPaymentStatus($inv_object, $status)
    {

        $booking_id = $inv_object->module_id;

        $this->db->where("id", $booking_id);
        $this->db->update("booking", array(
            'payment_status' => $status,
            'amount' => $inv_object->amount,
        ));

    }

    public function getInvoice($module_id)
    {

        $this->db->where('module', "booking_payment");
        $this->db->where('module_id', $module_id);

        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();

        if (isset($invoice[0]))
            return $invoice[0];

        return NULL;
    }

    private function getInvoiceBooking($user_id, $booking_id){
        $this->db->where('user_id', $user_id);
        $this->db->where('module', "booking_payment");
        $this->db->where('module_id', $booking_id);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();

        return $invoice;
    }

    public function convert_booking_to_invoice($user_id, $booking_id)
    {

        $payable = 0;

        $invoice = $this->getInvoiceBooking($user_id,$booking_id);

        if(isset($invoice[0])) {
            if($invoice[0]->amount > 0)
                $payable = 1;
            return array(Tags::SUCCESS => 1, Tags::RESULT => $invoice[0]->id,"payable"=>$payable);
        }


        $this->db->where('user_id', $user_id);
        $this->db->where('id', $booking_id);
        $booking = $this->db->get('booking', 1);
        $booking = $booking->result_array();

        if (isset($booking[0])) {

            $booking = $booking[0];

            $items = array();
            $amount = 0;

            $cart = json_decode($booking['cart'], JSON_OBJECT_AS_ARRAY);

            foreach ($cart as $item) {

                $callback = NSModuleLinkers::find($item['module'], 'getData');

                if ($callback != NULL) {

                    $params = array(
                        'id' => $item['module_id']
                    );

                    $result = call_user_func($callback, $params);

                    $items[] = array(
                        'item_id' => $item['module_id'],
                        'item_name' => $result['label'],
                        'price' => $item['amount'],
                        'qty' => $item['qty'],
                        'unit' => 'item',
                        'price_per_unit' => $item['amount'],
                    );

                    $amount = $amount + ($item['amount'] * $item['qty']);

                }

            }

            if ($amount == 0)
                return array(Tags::SUCCESS => 1, Tags::RESULT => -1);

            $this->db->where('user_id', $user_id);
            $no = $this->db->count_all_results('invoice');
            $no++;


            $data = array(
                "method" => "",
                "amount" => $amount,
                "no" => $no,
                "module" => "booking_payment",
                "module_id" => $booking['id'],
                "tax_id" => 0,
                "items" => json_encode($items, JSON_FORCE_OBJECT),
                "currency" => PAYMENT_CURRENCY,
                "status" => 0,
                "user_id" => $user_id,
                "transaction_id" => "",
                "updated_at" => date("Y-m-d H:i:s", time()),
                "created_at" => date("Y-m-d H:i:s", time())
            );



            $this->db->insert('invoice', $data);
            $id = $this->db->insert_id();

            return array(Tags::SUCCESS => 1, Tags::RESULT => $id,"payable"=>$payable);
        }


        return array(Tags::SUCCESS => 0);

    }


    public function updateFields()
    {

        if (!$this->db->field_exists('amount', 'booking')) {
            $fields = array(
                'amount' => array('type' => 'DOUBLE', 'default' => 0),
            );
            $this->dbforge->add_column('booking', $fields);
        }

        if (!$this->db->field_exists('payment_status', 'booking')) {
            $fields = array(
                'payment_status' => array('type' => 'VARCHAR(150)', 'default' => 0),
            );
            $this->dbforge->add_column('booking', $fields);
        }

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
                'type' => 'INT',
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

