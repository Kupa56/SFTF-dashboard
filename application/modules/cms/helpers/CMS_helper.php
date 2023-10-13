<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 4/19/19
 * Time: 13:56
 */


class CMSToken{

    public static function createApi(){
        $ctx = &get_instance();
        return $ctx->mCMS->createApi("test");
    }

}

class WEBAPP_Template{

    private static $css = array();
    private static $js = array();
    private static $meta_tags = array();

    public static function addMeta($name,$content,$attr="name"){
        if(!isset(self::$meta_tags[$name])){
            self::$meta_tags[$name] = array(
                "attr" => $attr,
                "attrValue" => $name,
                "content" => $content
            );
        }
    }

    public static function addJsLib($path){
        if(!isset(self::$js[$path])){
            self::$js[$path] = $path;
        }
    }

    public static function addCssLib($path){
        if(!isset(self::$css[$path])){
            self::$css[$path] = $path;
        }
    }

    public static function loadCssLibs(){
        return self::$css;
    }

    public static function loadJsLibs(){
        return self::$js;
    }

    public static function loadMetaTags(){
        return self::$meta_tags;
    }

}

class CMS_Manager{

    private static $pages = array();

    public static function add_page($page,$template,$callback){
        self::$pages[] = array(
            "page" => $page,
            "template" => $template,
            "callback" => $callback,
        );
    }

    public static function add_api($page,$template,$callback){

        self::$pages[] = array(
            "page" => $page,
            "template" => $template,
            "callback" => $callback,
            "exeBeforeCallback" => function() use ($page) {
                //verify token
                verifyToken();
                //prevent robot from accessing to this links
                preventIndex();
            },
        );
    }


    public static function loadPages(){
        return self::$pages;
    }

    private static $headers = array();

    public static function add_header($key,$template){
        self::$headers[$key] = $template;
    }

    public static function loadHeaders(){
        return self::$headers;
    }


    private static $footers = array();

    public static function add_footer($key,$template){
        self::$footers[$key] = $template;
    }

    public static function loadFooters(){
        return self::$footers;
    }


    private static $custom_pages = array();

    public static function register_custom_page($key){
        self::$custom_pages[] = $key;
    }

    public static function register_custom_pages($keys){
        foreach ($keys as $val){
            self::$custom_pages[] = $val;
        }
    }

    public static function get_custom_pages_keys($key){
        return self::$custom_pages;
    }


    public static function onCustomPageCalled($callback){
        self::$custom_pages[] = array(
            "callback" => $callback,
        );
    }

    public static function getCustomPages(){
        return self::$custom_pages;
    }

    public static function getCustomPage($slug){

        foreach (self::$custom_pages as $p){
            return $p;
        }

        return NULL;
    }



    private static $templates = array();


    public static function add_template($key,$template,$data=array()){
        self::$templates[$key]['path'] = $template;
        self::$templates[$key]['data'] = $data;
        self::$templates[$key]['type'] = "template";
    }

    public static function loadTemplates(){
        return self::$templates;
    }

    public static function add_widget($key,$template,$data=array()){
        self::$templates[$key]['path'] = $template;
        self::$templates[$key]['data'] = $data;
        self::$templates[$key]['type'] = "widget";
    }

}

class CMS_Display{

    private static $hook_data_list = array();

    public static function createHook($hook){
        self::$hook_data_list[$hook] = array();
    }

    public static function setHTML($hook, $html){
        self::$hook_data_list[$hook][] = array(
            'html' => $html
        );
    }

    public static function set($hook, $path, $data=array()){

        if(!isset(self::$hook_data_list[$hook]['replaced'])){
            self::$hook_data_list[$hook][] = array(
                'path' => $path,
                'data' => $data,
            );
        }

    }

    public static function replace($hook, $path, $data=array()){
        self::$hook_data_list[$hook] = array();
        self::$hook_data_list[$hook]["replaced"] = array(
            'path' => $path,
            'data' => $data,
        );
    }


    public static function render($hook){
        if(isset(self::$hook_data_list[$hook])){

            foreach (self::$hook_data_list[$hook] as $key => $data){

                if(isset($data['path'])){
                    $context = &get_instance();
                    $context->load->view(
                        $data['path'],
                        $data['data']
                    );
                }else{
                    echo $data['html'];
                }

            }

        }
    }
}