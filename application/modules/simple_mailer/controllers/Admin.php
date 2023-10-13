<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */

class Admin extends ADMIN_Controller {

	private $module = "mailer";

    public function __construct(){
        parent::__construct();
    }

    /*
     * $data => default_from, default_to, template,
     */


    public function mailConfig(){

        $data = array();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("simple_mailer/backend/mail-config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }






}



/* End of file ArticleDB.php */
