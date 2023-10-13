<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Pages extends MAIN_Controller {

    public $templateName = "";

    public function getTemplateName(){
        return $this->templateName;
    }

    public function __construct(){
        parent::__construct();

        $this->templateName = ConfigManager::getValue("DEFAULT_TEMPLATE");
        NSModuleLoader::loadModel('setting','config_model','mConfigModel');

    }


    public function error404(){
        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view(AdminPanel::TemplatePath."/error404");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");
    }


    private function validateExistingURI($uri,$pages){

        $this->db->where('slug',$uri);
        $result = $this->db->get('cms_uri',1);
        $result = $result->result_array();

        if(count($result)> 0){

            foreach ($pages as $page){

                if(preg_match("#^".$page['page']."#",$result[0]['default_uri'])){
                    return array(
                        "parameters" => explode("/",$result[0]['default_uri']),
                        "default" => $page['page'],
                    );
                }
            }
        }

        return NULL;
    }


    private function onPageCalled($slug,$data,$parameters=array()){

        foreach (CMS_Manager::getCustomPages() as $cpage){
            $result = call_user_func($cpage['callback'],$slug,$parameters);
            if($result != NULL)
                foreach ($result as $key => $value){
                    $data[$key] = $value;
                }
        }

    }

    private function webFirstStep(){

        if(!ModulesChecker::isEnabled("webapp")){
            redirect(site_url("user/login?callback=webappDisabled"));
        }

        if(!ConfigManager::getValue("ENABLE_FRONT_END")){
            redirect(site_url("user/login?callback=frontDisabled"));
        }

        if(!file_exists("views/frontend/".$this->templateName."/functions.php")){
            die("Web version doesn't exist");
        }

        //include functions file
        if(file_exists("views/frontend/".$this->templateName."/functions.php")){
            require_once "views/frontend/".$this->templateName."/functions.php";
        }

        //include pages
        require_once FCPATH . "/application/modules/webapp/controllers/WebV1_func.php";

    }

    public function template(){

        //start web version custom pages
        $this->webFirstStep();


        $data['template_url'] = base_url("views/frontend/" . ConfigManager::getValue("FRONTEND_TEMPLATE_NAME")  );
        $data['base_url'] = base_url("");
        $data['webapp_url'] = webapp_url("");
        $data['site_url'] = site_url();
        $data['lang'] = getLanguage();


        //load saved Pages from database
        $this->loadCMS();


        //get current uri segment
        $segments = $this->uri->segment_array();
        $segment = $segments[2];


        //load pages
        $pages = CMS_Manager::loadPages();

        //init language if needed
        $this->init_language();

        //load saved uri and validate URIs
        $current_segment = $this->validateExistingURI($segment,$pages);


        if($current_segment == NULL){
            $current_segment = array(
                'default' => $segment,
                'parameters' => array_slice($segments, 1),
            );
        }



        foreach ($pages as $page){

            if($current_segment['default'] == $page['page']){

                if(!empty($current_segment['parameters']))
                    $segments = $current_segment['parameters'];

                //on page called
                $this->onPageCalled($page['page'],$data,$segments);

                //exe functions
                $get = RequestInput::get();

                //call back functions
                if(isset($page['exeBeforeCallback']))
                    call_user_func($page['exeBeforeCallback'],$segments,$get);


                $result = call_user_func($page['callback'],$segments,$get);


                if($result == 404){
                    //show page 404
                    if(file_exists(FCPATH."views/frontend/".$this->templateName."/error404.php")
                    OR file_exists(FCPATH."views/frontend/".$this->templateName."/error404.tpl")
                    OR file_exists(FCPATH."views/frontend/".$this->templateName."/error404.html")
                    OR file_exists(FCPATH."views/frontend/".$this->templateName."/error404.htm")){
                        $data = $this->mergeTemplates($data);
                        echo $this->parse($this->tpl_path("error404"),$data);
                    }
                    return;
                }else if($result == NULL){
                    return;
                }

                $data['data'] = $result;

                if(isset($result['breadcrumb'])){
                    $GLOBALS['breadcrumb'] = $result['breadcrumb'];
                }

                if(isset($result['data_to_parse'])){
                    $data = array_merge($data,$result['data_to_parse']);
                }

                $data = $this->mergeTemplates($data);

                echo $this->parse($this->tpl_path($page['template']),$data);
                return;
            }

        }



        //show page 404
        $data = $this->mergeTemplates($data);
        echo $this->parse($this->tpl_path("error404"),$data);

    }

    private function init_language(){

        $saved_lang = strtolower(Translate::getDefaultLangCode());
        $segments = $this->uri->segment_array();
        if(count($segments) > 0
            && preg_match("#[a-z]#i",$segments[1])){
            if($saved_lang != strtolower($segments[1])){
                if(array_key_exists(strtolower($segments[1]), Translate::getLangsCodes())){
                    Translate::changeSessionLang($segments[1]);
                }
            }
        }
    }

    private function mergeTemplates($data){

        //load all existing templates
        $templates = CMS_Manager::loadTemplates();

        foreach ($templates as $k => $tem) {
            foreach ($tem['data'] as $kd => $d1)
                $data[$kd] = $d1;
            $data[$k] = $this->parse($this->tpl_path( $tem['path'] ),$data);
        }
        return $data;

    }


    public function loadCMS(){

        $segments = $this->uri->segment_array();
        $pages = $this->mCMS->getCustomPages($segments[2]);

        foreach ($pages as $page){

            //get default page template
            $template = 'pages/content';

            if($page['template'] != NULL
                && $page['template'] != ""
                && in_array($page['template'],CMSUtils::getPTemplates())){
                $template = $page['template'];
            }

            CMS_Manager::add_page($page['slug'], $template, function ($uri_args, $parameters = array()) use ($page) {

                $data = array();

                //on page called
                $this->onPageCalled($page['slug'],$data,$parameters);

                $data_to_parse['title'] = $page['title'];
                $data_to_parse['content'] = Text::output($page['content']);
                $data['title'] = $page['title'];

                return array(
                    "uri_args" => $uri_args,
                    "parameters" => $parameters,
                    "data" => $data,
                    "data_to_parse" => $data_to_parse,
                    "breadcrumb" => array(
                        "Home"=>"",
                        "Page"=>"",
                        $page['title'] => $page['slug']
                    ),
                );
            });

        }


    }

    public function index(){

        //start web version home page
        $this->webFirstStep();

        $data['template_url'] = base_url("views/frontend/" . ConfigManager::getValue("FRONTEND_TEMPLATE_NAME")  );
        $data['base_url'] = base_url("");
        $data['webapp_url'] = webapp_url("");
        $data['site_url'] = site_url();
        $data['lang'] = getLanguage();


        $segments = $this->uri->segment_array();


        //init language if needed
        $this->init_language();


        foreach (CMS_Manager::loadPages() as $page){

            if($page['page'] == "index"){

                $get = RequestInput::get();

                if(isset($page['exeBeforeCallback']))
                    call_user_func($page['exeBeforeCallback'],$segments,$get);

                $result = call_user_func($page['callback'],$segments,$get);

                $data['data'] = $result;

                //load all existing templates
                $templates = CMS_Manager::loadTemplates();


                foreach ($templates as $k => $tem){
                    foreach ($tem['data'] as $kd => $d1)
                        $data[$kd] = $d1;

                    //parse widget
                    $data[$k] = $this->parse($this->tpl_path($tem['path']),$data);
                }

                //parse template
                echo $this->parse($this->tpl_path($page['template']),$data);
                break;
            }

        }

    }


    private function tpl_path($tpl){
        return tpl_path($tpl);
    }

    private function parse($path,$data){

        //add globals data
        if(isset($data['data']['data']['title'])){
            $GLOBALS['title'] = $data['data']['data']['title'];
        }

        if(isset($data['data']['data']['description'])){
            $GLOBALS['description'] = $data['data']['data']['description'];
        }

        if(file_exists(FCPATH . "views/" . $path  . ".php")
            OR preg_match("#.php$#",$path)){
            $html = $this->load->view($path,$data,TRUE);
            return trim($this->parser->parse_string($html, $data,TRUE));
        }

        $this->template->assign($data);
        return trim($this->template->fetch($path));

    }

    public function fpassword(){

        redirect(site_url("user/fpassword"));

    }


    public function myPortal(){


        $this->load->model("User/mUserModel");

        redirect(site_url("user/login"));

    }

    public function webdashboard(){

        $token = RequestInput::get('token');

        $this->business_manager->checkMobileRequest($token);

    }



    public function webDashboardAction(){


        $action = RequestInput::get('action');

        if($action == "my_account")
            redirect(admin_url("user/profile"));
        else if($action == "logout")
            redirect("user/logout");
        else if($action == "subscription")
            redirect("pack/pickpack?req=upgrade");

    }

    public function webDashboardActionNoLogged(){

        $action = RequestInput::get('action');

       if($action == "subscription")
            redirect("pack/pickpack?req=upgrade");

    }


    public function mail(){

        // Only process POST reqeusts.

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the form fields and remove whitespace.
            $name = strip_tags(trim(RequestInput::post("name")));
            $name = str_replace(array("\r","\n"),array(" "," "),$name);
            $email = filter_var(trim(RequestInput::post("email")), FILTER_SANITIZE_EMAIL);
            // $cont_subject = trim($_POST["subject"]);
            $message = trim(RequestInput::post("message"));

            // Check that data was sent to the mailer.
            if ( empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Set a 400 (bad request) response code and exit.
                http_response_code(400);
                echo _lang("Oops! There was a problem with your submission. Please complete the form and try again.");
                exit;
            }

            // Set the recipient email address.
            // FIXME: Update this to your desired email address.
            $recipient = DEFAULT_EMAIL;

            // Set the email subject.
            $subject = APP_NAME.": New contact from $name";

            // Build the email content.
            $email_content = "Name: $name\n";
            $email_content .= "Email: $email\n\n";
            // $email_content .= "Subject: $cont_subject\n";
            $email_content .= "Message:\n$message\n";

            // Build the email headers.
            $email_headers = "From: $name <$email>";


            $mailer = new DTMailer();
            $mailer->setFrom($email);
            $mailer->setFrom_name($name);
            $mailer->setRecipient($recipient);
            $mailer->setSubject($subject);
            $mailer->setMessage($email_content);
            $mailer->setType("plain");

            // Send the email.
            if ($mailer->send()) {
                // Set a 200 (okay) response code.
                http_response_code(200);
                echo _lang("Thank You! Your message has been sent.");
            } else {
                // Set a 500 (internal server error) response code.
                http_response_code(500);
                echo _lang("Oops! Something went wrong and we couldn't send your message.");
            }

        } else {
            // Not a POST request, set a 403 (forbidden) response code.
            http_response_code(403);
            echo _lang("There was a problem with your submission, please try again.");
        }

    }

    public function version(){


        if(ConfigManager::getValue("_APP_VERSION")!=APP_VERSION){
            echo "Current Version: <B>".ConfigManager::getValue("_APP_VERSION")."</B><br>";
            echo "Ready for: <B>".APP_VERSION."</B><br><br>";
            echo '<a href="'.base_url("update?id=".CRYPTO_KEY).'">Run the update</a><br>';
            die();
        }else{
            echo "Current Version: <B>"._APP_VERSION."</B><br>";
        }

        echo 'Current PHP version: ' . phpversion();
        return;

    }

    public function recoverSettingsFile(){

        $this->mUpdateModel->prepareDemagedSettingFile();

    }



}
