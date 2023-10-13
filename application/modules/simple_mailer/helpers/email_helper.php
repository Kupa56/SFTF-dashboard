<?php


use SendGrid\Sendgrid\mail\Mail;

class MailContact{
    public $name;
    public $email;
    public $id;
}

//V2
class DTMailer{
	//from, from_name, replay, replay_name, sujet, text(plain|html), message
	protected $recipient;
	protected $cc;
	protected $bcc;
	protected $from;
	protected $from_name;
	protected $replay_to;
	protected $replay_to_name;
	protected $subject;
	protected $type;
	protected $message;
	protected $attachments=array();
    protected $test = FALSE;

    /**
     * @return bool
     */
    public function isTest()
    {
        return $this->test;
    }

    /**
     * @param bool $test
     */
    public function setTest(bool $test)
    {
        $this->test = $test;
    }


	/**
	 * @return mixed
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 * @param mixed $attachments
	 */
	public function setAttachments($attachments)
	{
		$this->attachments = $attachments;
	}

	/**
	 * @return mixed
	 */
	public function getCc()
	{
		return $this->cc;
	}

	/**
	 * @param mixed $cc
	 */
	public function setCc($cc)
	{
		$this->cc = $cc;
	}

	/**
	 * @return mixed
	 */
	public function getBcc()
	{
		return $this->bcc;
	}

	/**
	 * @param mixed $bcc
	 */
	public function setBcc($bcc)
	{
		$this->bcc = $bcc;
	}





	public function getRecipient() {
		return $this->recipient;
	}

	public function setRecipient($email) {
		$this->recipient = $email;
	}


	public function __construct() {

		$this->from = "";
		$this->from_name = "";
		$this->replay_to = "";
		$this->replay_to_name ="";
		$this->subject = "";
		$this->type = "";
		$this->message = "";

	}

	public function getFrom() {
		return $this->from;
	}

	public function getFrom_name() {
		return $this->from_name;
	}

	public function getReplay_to() {
		return $this->replay_to;
	}

	public function getReplay_to_name() {
		return $this->replay_to_name;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getType() {
		return $this->type;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function setFrom_name($from_name) {
		$this->from_name = $from_name;
	}

	public function setReplay_to($replay_to) {
		$this->replay_to = $replay_to;
	}

	public function setReplay_to_name($replay_to_name) {
		$this->replay_to_name = $replay_to_name;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setMessage($message) {
		$this->message = $message;
	}


    private function sendWithMailgunAPI_V3(){

        $url = 'https://api.sendgrid.com/v3/mail/send';

        $dataConfig = array();

        $to = array(
            array('email' => $this->recipient)
        );

        if(!empty($this->cc)){
            foreach ($this->cc as $email){
                $to[] = array(
                    'email' => $email
                );
            }
        }

        $personalizations = array(
            array(
                'to' => $to,
            )
        );

        $content = array(
            array(
                'type' => 'text/html',
                'value' => $this->message
            )
        );

        $from = array(
            'email' => $this->from
        );

        $attachments = array();

        if(!empty($this->attachments)){
            foreach ($this->attachments as $attachment){
                $attachments[] = array(
                    'content' => 'BASE64_ENCODED_CONTENT',
                    'filename' => $attachment
                );
            }
            if(!empty($attachments))
                $dataConfig['attachments'] = $attachments;
        }

        $dataConfig['personalizations'] = $personalizations;
        $dataConfig['from'] = $from;
        $dataConfig['subject'] = $this->subject;
        $dataConfig['content'] = $content;


        $data = json_encode($dataConfig);

        // Initialize the cURL request
        $ch = curl_init();

        // Set the API endpoint and request method
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Add the API key to the request headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.ConfigManager::getValue('MAILER_EXTERNAL_SENDGRID_API_KEY') ,
            'Content-Type: application/json'
        ));

        // Set cURL options to return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        // Close the cURL session
        curl_close($ch);

        // Handle the response
        if ($response === false) {
            // An error occurred
            if($this->test)
                echo 'Test:SendGridAPI: Error - ' . curl_error($ch);
            return FALSE;
        } else {
            if($this->test)
                // Request was successful
                echo 'Test:SendGridAPI: Success (sent to '.ConfigManager::getValue("DEFAULT_EMAIL").')';
            return TRUE;
        }

    }

    public function sendWithMailJetAPIV3(){


        $url = 'https://api.mailjet.com/v3.1/send';

        $fromEmail = array(
            'Email' => $this->from,
            'Name' => $this->from_name
        );

        $toEmail = array(
            array(
                'Email' => $this->recipient,
                'Name' => ""
            )
        );

        $data = array(
            'Messages' => array(
                array(
                    'From' => $fromEmail,
                    'To' => $toEmail,
                    'Subject' => $this->subject,
                    'TextPart' => strip_tags($this->message),
                    'HTMLPart' => $this->message
                )
            )
        );

        $data = json_encode($data);

        // Initialize the cURL request
        $ch = curl_init();

        // Set the API endpoint and request method
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode(ConfigManager::getValue('MAILER_EXTERNAL_MAILJET_API_KEY').":".ConfigManager::getValue('MAILER_EXTERNAL_MAILJET_SECRET_KEY'))
        ));

        // Set cURL options to return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        // Execute the request
        $response = curl_exec($ch);
        // Close the cURL session
        curl_close($ch);

        print_r($response);

        // Handle the response
        if ($response === false) {
            // An error occurred
            if($this->test)
                echo 'Test:MailJetAPI: Error - ' . curl_error($ch);
            return FALSE;
        } else {
            if($this->test)
                // Request was successful
                echo 'Test:MailJetAPI: Success (sent to '.ConfigManager::getValue("DEFAULT_EMAIL").')';
            return TRUE;
        }


    }


    private function sendWithSendGridAPI_V3(){

        $url = 'https://api.sendgrid.com/v3/mail/send';

        $dataConfig = array();

        $to = array(
            array('email' => $this->recipient)
        );

        if(!empty($this->cc)){
            foreach ($this->cc as $email){
                $to[] = array(
                    'email' => $email
                );
            }
        }

        $personalizations = array(
            array(
                'to' => $to,
            )
        );

        $content = array(
            array(
                'type' => 'text/html',
                'value' => $this->message
            )
        );

        $from = array(
            'email' => $this->from
        );

        $attachments = array();

        if(!empty($this->attachments)){
            foreach ($this->attachments as $attachment){
                $attachments[] = array(
                    'content' => 'BASE64_ENCODED_CONTENT',
                    'filename' => $attachment
                );
            }
            if(!empty($attachments))
                $dataConfig['attachments'] = $attachments;
        }

        $dataConfig['personalizations'] = $personalizations;
        $dataConfig['from'] = $from;
        $dataConfig['subject'] = $this->subject;
        $dataConfig['content'] = $content;


        $data = json_encode($dataConfig);

        // Initialize the cURL request
        $ch = curl_init();

        // Set the API endpoint and request method
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Add the API key to the request headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.ConfigManager::getValue('MAILER_EXTERNAL_SENDGRID_API_KEY') ,
            'Content-Type: application/json'
        ));

        // Set cURL options to return the response instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        // Close the cURL session
        curl_close($ch);

        // Handle the response
        if ($response === false) {
            // An error occurred
            if($this->test)
                 echo 'Test:SendGridAPI: Error - ' . curl_error($ch);
            return FALSE;
        } else {
            if($this->test)
                // Request was successful
                echo 'Test:SendGridAPI: Success (sent to '.ConfigManager::getValue("DEFAULT_EMAIL").')';
            return TRUE;
        }

    }

    private function sendWithSmtp(){

        $ciContext = get_instance();
        $ciContext->load->config('config');

        $config = array();
        $config['protocol']   = ConfigManager::getValue("SMTP_PROTOCOL");
        $config['smtp_host']  = ConfigManager::getValue("SMTP_HOST");
        $config['smtp_port']  = ConfigManager::getValue("SMTP_PORT");
        $config['smtp_user']  = ConfigManager::getValue("SMTP_USER");
        $config['smtp_pass']  = ConfigManager::getValue("SMTP_PASS");
        $config['mailtype']   = $ciContext->config->item('email_mailtype');
        $config['charset']    = $ciContext->config->item('email_charset');
        $config['_smtp_auth'] = TRUE;

        $ciContext->email->initialize($config);
        $ciContext->email->set_newline("\r\n");

        return $this->sendWithLocal($ciContext);

    }

    public function send(){

        if(ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_SMTP){
            return $this->sendWithSmtp();
        }else  if(ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_LOCAL){
            return $this->sendWithLocal();
        }else  if(ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_ESP_SENDGRID){
            return $this->sendWithSendGridAPI_V3();
        }else  if(ConfigManager::getValue('MAILER_ESP_MODULE_ENABLED') == Simple_mailer::MAILER_ESP_MAILJET){
            return $this->sendWithMailJetAPIV3();
        }

        return FALSE;
    }

	public function sendWithLocal($ciContext = NULL){

        if($ciContext == NULL)
            $ciContext = &get_instance();

		if($this->recipient==""){
            return  FALSE;
		}

        $mail = $this->recipient;
        $from = $this->from;
        $sujet = $this->subject;
        //=========

        $message = $this->message;

        $ciContext->email->set_mailtype("html");
        $ciContext->email->set_newline("\r\n");


        $ciContext->email->to($mail);

        if($this->cc!=NULL)
            $ciContext->email->cc($this->cc);

        if($this->bcc!=NULL)
            $ciContext->email->bcc($this->bcc);

        if($this->replay_to != NULL && $this->replay_to_name)
            $ciContext->email->reply_to($this->replay_to, $this->replay_to_name);

        $ciContext->email->from($from,$this->from_name);
        $ciContext->email->subject($sujet);
        $ciContext->email->message($message);

        if(!empty($this->attachments)){
            foreach ($this->attachments as $file){
                $ciContext->email->attach($file["url"], 'attachment', $file["name"]);
            }
        }

        //=====Send Email
        if($ciContext->email->send()){
            return TRUE;
        }

        return FALSE;
	}



	public static function templateParser($data=array(),$file=''){

		$url = base_url("views/mailing/templates/".$file.".html");
		$content = "";
		try{


			$content = file_get_contents($url);
			if(!empty($data) AND $content!=''){

				foreach ($data AS $key => $value){


					if(filter_var($value,FILTER_VALIDATE_EMAIL)){
						$content = preg_replace("#\{".$key."\}#","<a href='$value'>".$value."</a>" , $content);
					}else{
						$content = preg_replace("#\{".$key."\}#",$value , $content);
					}
				}
			}

		} catch (Exception $ex) {

		}
		return $content;
	}




}


        
        
        
        
        
        
        
        

