<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Cms_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();

    }

    public function updateUserPermission($user_id)
    {

        $this->db->where('name', 'WebAppClientAcc');
        $grp = $this->db->get('group_access', 1);
        $grp = $grp->result();

        if (count($grp) > 0) {

            $this->db->where('id_user', $user_id);
            $this->db->update('user', array(
                'grp_access_id' => $grp[0]->id
            ));

        } else {

            $grp_id = $this->createPermission();

            $this->db->where('id_user', $user_id);
            $this->db->update('user', array(
                'grp_access_id' => $grp_id
            ));

        }

        return TRUE;
    }

    public function createPermission($permission=array(),$force=FALSE)
    {

        if($force==FALSE){ // check if already created
            $this->db->where('name', 'WebAppClientAcc');
            $count = $this->db->count_all_results('group_access');
            if($count > 0)
                return 0;
        }

        $data = array();
        $actions = $this->db->get('module_actions');
        $actions = $actions->result_array();

        foreach ($actions as $action) {
            $data[$action['module']] = array();
            $ac = json_decode($action['actions'], JSON_OBJECT_AS_ARRAY);
            foreach ($ac as $value) {
                $data[$action['module']][$value] = 0;
            }
        }

        foreach ($data as $module => $val) {
            if(isset($permission[$module])
                && !empty($permission[$module])){
                foreach ($permission[$module] as $k1 => $action){
                    if(isset($permission[$module][$k1]) && $permission[$module][$k1] == TRUE){
                        $data[$module][$k1] = 1;
                    }
                }
            }
        }

        if($force == TRUE){

            $this->db->where('name', 'WebAppClientAcc');
            $group_access = $this->db->get('group_access', 1);
            $group_access = $group_access->result_array();

            $this->db->where('id', $group_access[0]['id']);
            $this->db->update('group_access', array(
                'name' => 'WebAppClientAcc',
                'permissions' => json_encode($data),
                'editable' => 0,
                'updated_at' => date("Y-m-d H:i:s", time()),
            ));

            return $group_access[0]['id'];
        }else{
            $this->db->insert('group_access', array(
                'name' => 'WebAppClientAcc',
                'permissions' => json_encode($data),
                'editable' => 0,
                'created_at' => date("Y-m-d H:i:s", time()),
                'updated_at' => date("Y-m-d H:i:s", time()),
            ));
            return $this->db->insert_id();
        }
    }

    public function generateClientGrpAcc($permission=array()){

        if(defined(DEFAULT_WEBAPP_CLIENT_GRPAC))
            return;

        //request permission
        $grp_id = $this->createPermission($permission);

        //create custom grp for client
        ConfigManager::setValue("DEFAULT_WEBAPP_CLIENT_GRPAC",$grp_id,TRUE);
    }

    public function pageExists($slug){

        $this->db->where('slug',$slug);
        $result = $this->db->get('cms_pages',1);
        $result = $result->result_array();

        if( count($result)>0 )
            return $result[0]['id'];

        return 0;
    }


    public function createApi($session_id = "")
    {

        $context = &get_instance();
        $token = md5(time() . rand(0, 999));

        $context->db->where("type", "request_api");
        $context->db->where("content", $session_id);
        $context->db->delete("token");

        $context->db->insert('token', array(
            "id" => $token,
            "uid" => -1,
            "type" => "request_api",
            "content" => $session_id,
            "created_at" => date("Y-m-d", time())
        ));

        return $token;
    }

    public function getPageBySlug($slug){

        $this->db->where('slug',$slug);
        $pages = $this->db->get("cms_pages");
        $pages = $pages->result_array();

        if(isset($pages[0]))
            return $pages[0];

        return NULL;
    }

    public function getAllPages(){

        $pages = $this->db->get("cms_pages");
        $pages = $pages->result_array();

        return $pages;
    }

    public function getPages($params = array(), $whereArray = array(), $callback = NULL)
    {

        //params login password mac_address
        $errors = array();
        $data = array();

        //extract — Importe les variables dans la table des symboles
        extract($params);

        if (!isset($page))
            $page = 1;


        if (!isset($page) OR $page == 0) {
            $page = 1;
        }

        if (!isset($limit)) {
            $limit = 20;
        }

        if ($limit == 0) {
            $limit = 20;
        }

        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        if (isset($params["q"]) && $params["q"] != "") {
            $this->db->like('title', Text::input($params["q"], TRUE));
        }

        if (isset($params["id"]) && $params["id"] != "") {
            $this->db->where('id', intval($params["id"]));
        }

        $count = $this->db->count_all_results("cms_pages");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        if (isset($params["q"]) && $params["q"] != "") {
            $this->db->like('title', Text::input($params["q"], TRUE));
        }

        if (isset($params["id"]) && $params["id"] != "") {
            $this->db->where('id', intval($params["id"]));
        }

        $this->db->from("cms_pages");

        $pages = $this->db->get();
        $pages = $pages->result_array();

        if (count($pages) < $limit) {
            $count = count($pages);
        }

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $pages);

    }

    public function savePage($params = array())
    {

        $errors = array();
        $data = array();

        if (!isset($params['id']) OR $params['id'] == "") {
            $errors[] = _lang("ID is missed!");
        }

        $this->db->where('id', intval($params['id']));
        $count = $this->db->count_all_results('cms_pages');

        if ($count == 0)
            $errors[] = _lang("ID is missed!");

        if (!isset($params['status']) && is_numeric($params['status'])) {
            $errors[] = _lang("Page status is invalid!");
        }

        if (!isset($params['title']) OR $params['title'] == "") {
            $errors[] = _lang("Title field is empty");
        }

        if (!isset($params['slug']) OR $params['slug'] == "") {
            $errors[] = _lang("Slug field is required");
        }

        if (!isset($params['template']) OR $params['template']=="") {
            $params['template'] = "pages/content";
        }else if(!in_array($params['template'],CMSUtils::getPTemplates())){
            $errors[] = _lang("You select invalid template!");
        }


        if (!empty($errors))
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);


        $this->db->where('id !=', intval($params['id']));
        $this->db->where('slug', ($params['slug']));
        $count = $this->db->count_all_results('cms_pages');

        if ($count > 0)
            $errors[] = _lang("Slug already exists!");


        $this->db->where('id', intval($params['id']));
        $this->db->update('cms_pages', array(
            'slug' => Text::input($params['slug']),
            'template' => $params['template'],
            'title' => Text::input($params['title']),
            'content' => Text::input($params['content'], TRUE),
            'status' => intval($params['status']),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return array(Tags::SUCCESS => 1);
    }


    public function getCustomPage($slug){

        $this->db->where('status',1);
        $this->db->where('slug',$slug);
        $pages = $this->db->get("cms_pages",1);
        $pages = $pages->result_array();

        return $pages;
    }


    public function getCustomPages(){

        $this->db->where('status',1);
        $pages = $this->db->get("cms_pages");
        $pages = $pages->result_array();

        return $pages;
    }


    public function addPage($params = array())
    {

        $errors = array();
        $data = array();

        if (!isset($params['title']) OR $params['title'] == "") {
            $errors[] = _lang("Title field is empty");
        }

        if (!isset($params['slug']) OR $params['slug'] == "") {
            $errors[] = _lang("Slug field is required");
        }

        if (!isset($params['template']) OR $params['template']=="") {
            $params['template'] = "pages/content";
        }else if(!in_array($params['template'],CMSUtils::getPTemplates())){
            $errors[] = _lang("You select invalid template!");
        }

        if (!empty($errors))
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);


        if (!isset($params['editable'])) {
            $params['editable'] = 1;
        }

        $this->db->where('slug', ($params['slug']));
        $count = $this->db->count_all_results('cms_pages');

        if ($count > 0)
            $errors[] = _lang("Slug already exists!");


        $this->db->insert('cms_pages', array(
            'slug' => Text::input($params['slug']),
            'template' => $params['template'],
            'editable' => intval($params['editable']),
            'title' => Text::input($params['title']),
            'content' => Text::input($params['content'], TRUE),
            'status' => intval($params['status']),
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return array(Tags::SUCCESS => 1,"page_id"=>$this->db->insert_id());
    }

    public function removePage($id){

        $this->db->where('id',$id);
        $this->db->delete('cms_pages');

        return array(Tags::SUCCESS=>1);
    }


    public function addSlug($params = array())
    {

        $errors = array();

        if (!isset($params['uri']) OR $params['uri'] == "") {
            $errors[] = _lang("URI is required");
        }

        if (!isset($params['slug']) OR $params['slug'] == "") {
            $errors[] = _lang("Slug is required");
        }

        if (!empty($errors))
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

        $this->db->insert('cms_uri', array(
            'slug' => Text::input($params['slug']),
            'default_uri' => $params['uri'],
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

    }

    public function re_order_menu($params=array()){

        if(isset($params['list'])){

            foreach ($params['list'] as $value){

                $this->db->where('id',intval($value['menu_id']));
                $this->db->update('cms_menu',array(
                    'order'=> intval($value['order'])
                ));

            }

        }

        return array(Tags::SUCCESS=>1);
    }

    public function getMenu($type="main_menu")
    {

        $main_menu = array();

        $this->db->where("type",$type);
        $this->db->where("parent_id",0);
        $this->db->order_by("order","ASC");
        $menu = $this->db->get("cms_menu");
        $menu = $menu->result_array();


        foreach ($menu as $key => $m1){


            $main_menu[$key] = array(
                'id'        =>$m1['id'],
                'title'     =>$m1['title'],
                'parent_id' =>$m1['parent_id'],
                'uri' =>$m1['uri'],
                'menus'     => array()
            );

            $this->db->where("parent_id",$m1['id']);
            $this->db->order_by("order","ASC");
            $menu = $this->db->get("cms_menu");
            $menu1 = $menu->result_array();

            foreach ($menu1 as $k1 => $m2){

                $main_menu[$key]['menus'][] = array(
                    'id'        =>$m2['id'],
                    'title'     =>$m2['title'],
                    'parent_id' =>$m2['parent_id'],
                    'uri' =>$m2['uri'],
                    'menus'=> array()
                );

            }

        }

        return $main_menu;
    }

    public function updateMenu($params = array())
    {

        $errors = array();
        $data = array();

        if (!isset($params['id']) && $params['id'] > 0) {
            $errors[] = _lang("ID is missed!");
        }

        if (!isset($params['title']) OR $params['title'] == "") {
            $errors[] = _lang("Title field is empty");
        }

        if (!isset($params['parent_id']) && $params['parent_id'] > 0) {
            $errors[] = _lang("Parent_ID field is required");
        }


        if(isset($params['option']) && $params['option'] == "1" && $params['page'] > 0){ //page

            $this->db->where('id',intval( $params['page']));
            $pages = $this->db->get("cms_pages",1);
            $pages = $pages->result_array();

            if(isset($pages[0]))
                $params['uri'] = "page::".$pages[0]['slug'];
            else
                $errors[] = _lang("Page is not valid!");

        }else if(isset($params['option']) && $params['option'] == "2" && $params['ex_url'] != ""){ //page
            $params['uri'] = "link::".$params['ex_url'];
        }else if(isset($params['option']) && $params['option'] == "1"){
            $errors[] = _lang("Page is not defined!");
        }else if(isset($params['option']) && $params['option'] == "2"){
            $errors[] = _lang("External url is not defined!");
        }

        if (!empty($errors))
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);


        $this->db->where('id',$params['id']);

        $this->db->update('cms_menu', array(
            'title' => Text::input($params['title']),
            'uri' => Text::input($params['uri']),
            'parent_id' => intval($params['parent_id']),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));


        return array(Tags::SUCCESS => 1);
    }

    public function removeMenu($id)
    {

        $this->db->where("parent_id",$id);
        $this->db->delete("cms_menu");

        $this->db->where("id",$id);
        $this->db->delete("cms_menu");

        return array(Tags::SUCCESS=>1);
    }


    public function addMenu($params = array())
    {

        $errors = array();
        $data = array();

        if (!isset($params['title']) OR $params['title'] == "") {
            $errors[] = _lang("Title field is empty");
        }

        if (!isset($params['parent_id']) && $params['parent_id'] > 0) {
            $errors[] = _lang("Parent_ID field is required");
        }


        if(isset($params['option']) && $params['option'] == "1" && $params['page'] > 0){ //page

            $this->db->where('id',intval( $params['page']));
            $pages = $this->db->get("cms_pages",1);
            $pages = $pages->result_array();

            if(isset($pages[0]))
                $params['uri'] = "page::".$pages[0]['slug'];
            else
                $errors[] = _lang("Page is not valid!");

        }else if(isset($params['option']) && $params['option'] == "2" && $params['ex_url'] != ""){ //page
            $params['uri'] = "link::".$params['ex_url'];
        }else if(isset($params['option']) && $params['option'] == "1"){
            $errors[] = _lang("Page is not defined!");
        }else if(isset($params['option']) && $params['option'] == "2"){
            $errors[] = _lang("External url is not defined!");
        }

        if (!empty($errors))
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);


        $this->db->order_by("order","DESC");
        $last_menu = $this->db->get("cms_menu",1);
        $last_menu = $last_menu->result_array();

        if(isset($last_menu[0]))
            $params['order'] = intval(  $last_menu[0]['order'] + 1  );
        else{
            $params['order'] = 0;
        }

        if(!isset($params['uri']))
            $params['uri'] = "#";

        $this->db->insert('cms_menu', array(
            'title' => Text::input($params['title']),
            'type' => "main_menu",
            'uri' => Text::input($params['uri']),
            'parent_id' => intval($params['parent_id']),
            'order' => intval($params['order']),
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return array(Tags::SUCCESS => 1);
    }


    public function installTemplate($template){

        //set default template
        ConfigManager::setValue("DEFAULT_TEMPLATE",$template);
        ConfigManager::setValue("FRONTEND_TEMPLATE_NAME",$template);

        //init default config
        $this->init_parsed_template_config_schema();

        //load translate form webapp
       TemplateLanguageUtils::installTemplateTranslate($template);

       //confirm the template installation
        ConfigManager::setValue("template_installed_".$template,TRUE);
    }

    private function init_parsed_template_config_schema(){

        $parsed_config = array();

        $config = TemplateUtils::getCurrentTemplate();
        $config_schema = $config['Config'];
        $templateVersion = $config['Version'];
        $prefix = $config['templateId']."_tpl";


        foreach ($config_schema as $key => $value){

            if(is_array($value) && TemplateUtils::hasRadio($value)
                && !TemplateUtils::hasTreeObject($value)){
                $v = $prefix."_".$key; //key
                foreach ($value as $field){ //value
                    $parsed_config[$v] = $field;
                    break;
                }
            }else{

                foreach ($value as $fk => $field){
                    if (is_array($field)){
                        $v = $prefix."_".$key."_".$fk; //key
                        foreach ($field as $fk2 => $option){ //value
                            $parsed_config[$v] = $option;
                            break;
                        }
                    }else{
                        $v = $prefix."_".$key."_".$fk; //key
                        $parsed_config[$v] = $field;
                    }
                }

            }

        }

        //init template version
        ConfigManager::setValue($prefix."_Version",$templateVersion);

        //init template config
        foreach ($parsed_config as $key => $value){
            ConfigManager::setValue($key,$value);
        }
    }

    //check if there is any new update in the template
    public function checkAndUpdateTemplate(){

        $config = TemplateUtils::getCurrentTemplate();
        $newVersion = $config['Version'];
        $prefix = $config['templateId']."_tpl";
        $oldVersion = ConfigManager::getValue($prefix."_Version");
        if (version_compare($newVersion, $oldVersion, '>')) {
            //do update here...
            TemplateLanguageUtils::installTemplateTranslate($config['templateId']);
            //save new version
            ConfigManager::setValue($prefix."_Version",$newVersion);
        }
    }


    public function createTables()
    {

        $sql = '
                CREATE TABLE IF NOT EXISTS `cms_pages` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `key` varchar(100) DEFAULT NULL,
                  `redirected_url` varchar(300) DEFAULT NULL,
                  `status` int(11) DEFAULT NULL,
                  `title` varchar(150) DEFAULT NULL,
                  `slug` varchar(150) DEFAULT NULL,
                  `template` TEXT DEFAULT NULL,
                  `editable` INT DEFAULT 1,
                  `indexation` int(11) DEFAULT 1,
                  `content` TEXT DEFAULT NULL,
                  `updated_at` datetime NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';

        $this->db->query($sql);

        $sql = '
                CREATE TABLE IF NOT EXISTS `cms_uri` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `slug` varchar(150) DEFAULT NULL,
                  `indexation` int(11) DEFAULT 1,
                  `default_uri` TEXT DEFAULT NULL,
                  `updated_at` datetime NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';

        $this->db->query($sql);

        $sql = '
                CREATE TABLE IF NOT EXISTS `cms_menu` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(150) DEFAULT NULL,
                  `uri` TEXT DEFAULT NULL,
                  `parent_id` INT DEFAULT NULL,
                  `order` INT DEFAULT 0,
                    `type` VARCHAR(100) DEFAULT NULL,
                  `updated_at` datetime NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';

        $this->db->query($sql);


    }


    public function updateFields()
    {

        if (!$this->db->field_exists('template', 'cms_pages')) {
            $fields = array(
                'template' => array('type' => 'TEXT', 'after' => 'slug', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('cms_pages', $fields);
        }


        if (!$this->db->field_exists('editable', 'cms_pages')) {
            $fields = array(
                'editable' => array('type' => 'INT', 'after' => 'template', 'default' => 1),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('cms_pages', $fields);
        }


    }
}