<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */

class Mailer_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
    }

    public function sendSimpleNotification($userId,$message,$subject=""){

        $userData = $this->mUserModel->getUserData($userId);

        $appLogo = _openDir(ConfigManager::getValue('APP_LOGO'));
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }

        $messageText = Text::textParserHTML(array(
            "name" => $userData['name'],
            "imageUrl" => $imageUrl,
            "email" => ConfigManager::getValue('DEFAULT_EMAIL'),
            "appName" => strtolower(ConfigManager::getValue('APP_NAME')),
            "body" => nl2br($message),
        ), $this->load->view("mailing/templates/default.html",NULL,TRUE));


        $mail = new DTMailer();
        $mail->setRecipient($userData['email']);
        $mail->setFrom(ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setFrom_name( ConfigManager::getValue('APP_NAME'));
        $mail->setMessage($messageText);
        $mail->setReplay_to( ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setReplay_to_name( ConfigManager::getValue('APP_NAME'));
        $mail->setType("html");
        $mail->setSubject($subject==""? Translate::sprintf("You have new notification from %s",array(ConfigManager::getValue("APP_NAME"))) : $subject );
        if(!$mail->send()){
            return FALSE;
        }

    }


    public function sendAdminNotification($message,$subject=""){

        $appLogo = _openDir(ConfigManager::getValue('APP_LOGO'));
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }

        $messageText = Text::textParserHTML(array(
            "name" => "",
            "imageUrl" => $imageUrl,
            "email" => ConfigManager::getValue('DEFAULT_EMAIL'),
            "appName" => strtolower(ConfigManager::getValue('APP_NAME')),
            "body" => nl2br($message),
        ), $this->load->view("mailing/templates/default.html",NULL,TRUE));


        $mail = new DTMailer();
        $mail->setRecipient(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setFrom(ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setFrom_name( ConfigManager::getValue('APP_NAME'));
        $mail->setMessage($messageText);
        $mail->setReplay_to( ConfigManager::getValue('DEFAULT_EMAIL'));
        $mail->setReplay_to_name( ConfigManager::getValue('APP_NAME'));
        $mail->setType("html");
        $mail->setSubject($subject==""? Translate::sprintf("You have new notification from %s",array(ConfigManager::getValue("APP_NAME"))) : $subject );
        if(!$mail->send()){
            return FALSE;
        }

    }

    public function send($params=array()){


		extract($params);

		$errors = array();
    	$data = array();

        if(isset($recipient) AND $recipient!=""
            AND Text::checkEmailFields($recipient))
            $data["recipient"] = $recipient;
        else
            $errors[] = Translate::sprint("Your recipient is not valid!");


        if(isset($from_email) AND $from_email!=""
			AND Text::checkEmailFields($from_email))
    		$data["from_email"] = $from_email;
    	else
    		$errors[] = Translate::sprint("Your email is not valid!");


        if(isset($from_name) AND $from_name!="")
            $data["from_name"] = $from_name;
        else
            $errors[] = Translate::sprint("Your name is not valid!");


        if(isset($reply_email) AND $reply_email!=""
            AND Text::checkEmailFields($reply_email))
            $data["reply_email"] = $reply_email;
        else
            $errors[] = Translate::sprint("Reply email is not valid!");


        if(isset($reply_name) AND $reply_name!="")
            $data["reply_name"] = $reply_name;
        else
            $errors[] = Translate::sprint("Reply_name is not valid!");

        //recipients
    	if(isset($to_email) and !empty($to_email)){

    		foreach ($to_email as $value) {

				if (Text::checkEmailFields($value)){

					$data["recipients"][] = $value;

				}
			}

		}else{
			$errors[] = Translate::sprint("Recipient email field is empty!");
		}

		//cc recipients
		if(isset($to_cc) and !empty($to_cc)){

			foreach ($to_cc as $value) {

				if (Text::checkEmailFields($value)){
					$data["cc_recipients"][] = $value;
				}
			}

		}

		//bcc recipients
		if(isset($to_bcc) and !empty($to_bcc)){

			foreach ($to_bcc as $value) {

				if (Text::checkEmailFields($value)){

					$data["bcc_recipients"][] = $value;

				}
			}

		}


		if(isset($subject) and $subject!=""){
    		$data["subject"] = trim($subject);
		}else{
			$errors[] = Translate::sprint("Subject field is not valid!");
		}


		if(isset($content) and $content!=""){
			$data["content"] = trim($content);
		}else{
			$errors[] = Translate::sprint("Content field is not valid!");
		}


    	if(empty($errors)){

			$mail = new DTMailer();
			$mail->setRecipient($data["recipients"]);

			if(isset($data["cc_recipients"])
				AND !empty($data["cc_recipients"]))
				$mail->setCc($data["cc_recipients"]);

			if(isset($data["bcc_recipients"])
				AND !empty($data["bcc_recipients"]))
					$mail->setBcc($data["bcc_recipients"]);

			$mail->setRecipient($data["recipient"]);

			$mail->setFrom($data["from_email"]);
			$mail->setFrom_name($data["from_name"]);

			$mail->setMessage($data["content"]);

            $mail->setReplay_to($data["reply_email"]);
			$mail->setReplay_to_name($data["reply_name"]);

			$mail->setType("html");
			$mail->setSubject($data["subject"]);

			if(isset($attachments) and !empty($attachments) and count($attachments)>0){
				$mail->setAttachments($attachments);
			}

			if($mail->send()){
                return array(Tags::SUCCESS=>1);
            }

            $errors[] = _lang("Error mailing");

		}

		return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);

	}


}
