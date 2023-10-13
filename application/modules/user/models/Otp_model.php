<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Otp_model extends CI_Model
{

    private  $method = "";
    private  $config = array();

    public function setup()
    {

        $this->method = ConfigManager::getValue('OTP_METHOD');
        $config = json_decode(ConfigManager::getValue('OTP_METHODS'),JSON_OBJECT_AS_ARRAY);
        $this->config = isset($config[$this->method])?$config[$this->method]:[];

        @$this->load->model(ucfirst($this->method).'_model','OTP_VerifyModel');

    }

    public function sendWithUser($userId,$phone){ //send code

        $this->db->where('id_user',$userId);
        $this->db->where('telephone',$phone);
        $this->db->where('hidden',0);
        $this->db->where('status !=' ,-1);
        $count = $this->db->count_all_results('user');

        if($count==0){
            return  array(Tags::SUCCESS=>-1,Tags::ERRORS=>array("err"=>_lang("There is no user linked with this phone number, try to create new account")));
        }

        //check limit
        $opt_limit =  intval(SessionManager::getValue( date('Y-m-d H',time()).'_'.$phone ,1));

        if($opt_limit>=5){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array('Err'=>'You have exceeded the limit of requests. try after 1 hour'));
        }


        $result = $this->OTP_VerifyModel->send($userId,$phone);
        if($result[Tags::SUCCESS]==1){

            $opt_limit++;
            SessionManager::setValue(  date('Y-m-d H',time()).'_'.$phone   ,$opt_limit);

            $result['message'] = Translate::sprintf("You still have %s attempt",array( 5 - $opt_limit ));
        }

        return $result;
    }

    public function send($userId,$phone){ //send code

        $this->db->where('telephone',$phone);
        $this->db->where('hidden',0);
        $this->db->where('status !=' ,-1);
        $count = $this->db->count_all_results('user');

        if($count==0){
            return  array(Tags::SUCCESS=>-1,Tags::ERRORS=>array("err"=>_lang("There is no user linked with this phone number, try to create new account")));
        }

        //check limit
        $opt_limit =  intval(SessionManager::getValue( date('Y-m-d H',time()).'_'.$phone ,1));

        if($opt_limit>=5){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array('Err'=>'You have exceeded the limit of requests. try after 1 hour'));
        }


        $result = $this->OTP_VerifyModel->send($userId,$phone);
        if($result[Tags::SUCCESS]==1){

            $opt_limit++;
            SessionManager::setValue(  date('Y-m-d H',time()).'_'.$phone   ,$opt_limit);

            $result['message'] = Translate::sprintf("You still have %s attempt",array( 5 - $opt_limit ));
        }

        return $result;
    }

    public function verify($userId,$phone,$optCode){ //verify the code

        $result = $this->OTP_VerifyModel->verify($userId,$phone,$optCode);

        if($result[Tags::SUCCESS]==1 && $userId==0){

            $this->db->where('telephone',$phone);
            $this->db->where('hidden',0);
            $this->db->where('status !=',-1);
            $user = $this->db->get('user',1);
            $user = $user->result_array();
            $result['userId'] = isset($user[0]['id_user']);
            $this->db->where('id_user',$user[0]['id_user']);
            $this->db->where('telephone',$phone);
            $this->db->update('user',array(
                'phoneVerified' => 1
            ));

        }else if($result[Tags::SUCCESS]==1){
            SessionManager::setValue('opt_limit_'.$phone,1);
        }

        $result = array(Tags::SUCCESS=>1);

        if($result[Tags::SUCCESS]==1){

            $this->db->where('telephone',$phone);
            $this->db->where('status !=',-1);
            $this->db->where('hidden',0);
            $user = $this->db->get('user',1);
            $user = $user->result_array();

            if(isset($user[0]['id_user'])){
                $user = $this->mUserModel->syncUser(array(
                    'user_id' => $user[0]['id_user']
                ));
                if(isset($user[Tags::RESULT]))
                    $result[Tags::RESULT] = $user[Tags::RESULT];
            }

        }

        return $result;
    }


}

