<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Setting_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function updateSetting(){

        //make default timeZone
        ConfigManager::setValue("TIME_ZONE","UTC");

    }

    public function updateFields(){

        $this->db->query('ALTER TABLE `token` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;');

    }

    public function sendContentReport($title,$content,$owner_user_id,$reported_by_user_id){

        $this->db->where('id_user',$owner_user_id);
        $owner = $this->db->get('user',1);
        $owner = $owner->result_array();
        $owner = $owner[0];

        $this->db->where('id_user',$reported_by_user_id);
        $reported_by = $this->db->get('user',1);
        $reported_by = $reported_by->result_array();
        $reported_by = $reported_by[0];

        //send mail verification
        $messageText = "
        <br/><br/>
        Title: $title <br/>
        Owner: ".$owner['name']." / Email: ".$owner['email']."  <br/>
        Reported by: ".$reported_by['name']." / Email: ".$reported_by['email']."  <br/>
        Content: $content <br/>";


        $mail = new DTMailer();
        $mail->setRecipient(REPORT_EMAIL);
        $mail->setFrom(DEFAULT_EMAIL);
        $mail->setFrom_name(APP_NAME);
        $mail->setMessage($messageText);
        $mail->setReplay_to(DEFAULT_EMAIL);
        $mail->setReplay_to_name(APP_NAME);
        $mail->setType("html");
        $mail->setSubject(Translate::sprint("Report content: ".$title));
        $mail->send();

        return array(Tags::SUCCESS=>1);
    }
}