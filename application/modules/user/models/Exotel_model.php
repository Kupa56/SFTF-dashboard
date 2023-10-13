<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Exotel_model extends CI_Model
{

    public function curl($url, $data, $token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, JSON_OBJECT_AS_ARRAY);

    }

    public function send($userId, $phone)
    { //send code


        $result = $this->curl(
            'https://' . ConfigManager::getValue('OTP_CONFIG_exotel_SubDomain') . '/v2/accounts/' . ConfigManager::getValue('OTP_CONFIG_exotel_AccountSid') . '/verifications/sms',
            array(
                'application_id' => ConfigManager::getValue('OTP_CONFIG_exotel_AuthKey'),
                'phone_number' => $phone,
            ),
            base64_encode(ConfigManager::getValue('OTP_CONFIG_exotel_AuthKey') . ':' . ConfigManager::getValue('OTP_CONFIG_exotel_AuthToken'))
        );


        if (isset($result['response']['code'])
            && $result['response']['code'] == 200
            && isset($result['response']['data'])
            && $result['response']['status'] == 'success') {

            //save session
            SessionManager::setValue('opt_verification_id', $result['response']['data']['verification_id']);

            return array(Tags::SUCCESS => 1, Tags::RESULT => $result['response']['data']['verification_id']);

        } else if (isset($result['response']['error_data']['description'])) {

            return array(Tags::SUCCESS => 0, Tags::ERRORS => array('err' => $result['response']['error_data']['description']));
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => array('Err' => 'Error#1'));
    }

    public function verify($userId, $phone, $optCode)
    {
        //verify the code
        $verification_id = SessionManager::getValue('opt_verification_id');

        $result = $this->curl(
            'https://' . ConfigManager::getValue('OTP_CONFIG_exotel_SubDomain') . '/v2/accounts/' . ConfigManager::getValue('OTP_CONFIG_exotel_AccountSid') . '/verifications/sms/' . $verification_id,
            array(
                'otp' => $optCode,
            ),
            base64_encode(ConfigManager::getValue('OTP_CONFIG_exotel_AuthKey') . ':' . ConfigManager::getValue('OTP_CONFIG_exotel_AuthToken'))
        );

        if (isset($result['response']['code'])
            && $result['response']['code'] == 200
            && isset($result['response']['data'])
            && $result['response']['status'] == 'success') {

            return array(Tags::SUCCESS => 1);
        } else if (isset($result['response']['error_data']['description'])) {

            return array(Tags::SUCCESS => 0, Tags::ERRORS => array('err' => $result['response']['error_data']['description']));
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => array('Err' => 'Error#2'));
    }

}

