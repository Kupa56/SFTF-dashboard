<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */

class Ajax extends AJAX_Controller  {

	private $module = "mailer";


	public function __construct()
    {
        parent::__construct();
    }


    public function send(){

        $this->enableDemoMode();

		//send mail here
		$from_email = RequestInput::post("from-email");
		$to_email = RequestInput::post("to-email");

		$to_cc = RequestInput::post("to-cc");
		$to_bcc = RequestInput::post("to-bcc");
		$subject = RequestInput::post("subject");
		$content = RequestInput::post("content");
		$attachments = RequestInput::post("attachments");



		$result = $this->mMailer->send(array(
			"from_email" 	=> $from_email,
			"to_email" 		=> $to_email,
			"to_cc" 		=> $to_cc,
			"to_bcc" 		=> $to_bcc,
			"subject" 		=> $subject,
			"content" 		=> $content,
			"attachments" 	=> $attachments,
		));


        echo json_encode($result);
        return;

    }



}
