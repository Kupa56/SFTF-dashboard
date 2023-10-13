<?php use SendGrid\Sendgrid\mail\Mail;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */

class Simple_mailer extends MAIN_Controller {

	private $module = "simple_mailer";

    const MAILER_LOCAL = 1;
    const MAILER_SMTP = 2;
    const MAILER_ESP_SENDGRID = 3;
    const MAILER_ESP_MAILGUN = 4;
    const MAILER_ESP_MAILJET = 5;

	public function __construct(){
        parent::__construct();
        $this->init($this->module);
    }


    public function onLoad()
    {
        $this->load->helper("simple_mailer/email");
        $this->load->helper("simple_mailer/mailer");
        $this->load->model("simple_mailer/Mailer_model","mMailer");


    }

    public function MailJetTest(){

        $this->enableDemoMode();

        if(SessionManager::isLogged() && SessionManager::getData('manager') == 1){
            $mailer = new DTMailer();
            $mailer->setRecipient(ConfigManager::getValue("DEFAULT_EMAIL"));
            $mailer->setFrom(ConfigManager::getValue("DEFAULT_EMAIL"));
            $mailer->setTest(TRUE);
            $mailer->setSubject("Hello");
            $mailer->setMessage("This a test mail from ".ConfigManager::getValue("APP_NAME"));
            $mailer->send();
        }

    }

    public function SendGridTest(){

        $this->enableDemoMode();

        if(SessionManager::isLogged() && SessionManager::getData('manager') == 1){
            $mailer = new DTMailer();
            $mailer->setRecipient(ConfigManager::getValue("DEFAULT_EMAIL"));
            $mailer->setFrom(ConfigManager::getValue("DEFAULT_EMAIL"));
            $mailer->setTest(TRUE);
            $mailer->setSubject("Hello");
            $mailer->setMessage("This a test mail from ".ConfigManager::getValue("APP_NAME"));
            $mailer->send();
        }

    }


    public function onCommitted($isEnabled)
    {

    	if(!$isEnabled)
    		return;


        AdminTemplateManager::registerMenuSetting(
            'simple_mailer',
            "simple_mailer/menu",
            11
        );



        ConfigManager::setValue("MAILER_ESP_MODULE_ENABLED",self::MAILER_LOCAL,TRUE);

        //SendGrid
        ConfigManager::setValue("MAILER_EXTERNAL_SENDGRID_API_KEY","",TRUE);

        //MailJet
        ConfigManager::setValue("MAILER_EXTERNAL_MAILJET_API_KEY","",TRUE);
        ConfigManager::setValue("MAILER_EXTERNAL_MAILJET_SECRET_KEY","",TRUE);

    }

    /*
     * $data => default_from, default_to, template,
     */

    public function mailer_editor($data){

        $wysihtml5Lib = adminAssets("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js");

		AdminTemplateManager::addScriptLibs($wysihtml5Lib);
		AdminTemplateManager::addScript(
			$this->load->view('simple_mailer/scripts/global_js',NULL,TRUE)
		);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("simple_mailer/backend/send-mail");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function onInstall()
    {

        return TRUE;
    }

    public function onEnable()
    {
        return TRUE;
    }

    public function onDisable()
    {
        return TRUE;
    }

    public function onUpgrade()
    {
        return TRUE;
    }


}



/* End of file ArticleDB.php */
