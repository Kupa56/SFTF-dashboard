<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 11/15/2017
 * Time: 23:00
 */

class FieldsTranslator
{

    public static function newInstance(array $fields)
    {
        $object = new FieldsTranslator($fields);
        return $object;
    }

    public static $skip = FALSE;
    public $fields = array(

    );

    /**
     * FieldsTranslator constructor.
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function setup(){

        $languages = array();

        $languages[] = Translate::getDefaultLangCode();

        foreach (Translate::getLangsCodes() as $k => $obj){
            if(!in_array($obj,$languages))
             $languages[] = $k;
        }

        //include fields translator scripts
        AdminTemplateManager::addScript(
            "<script data-languages='".implode(",",$languages)."' src='".AdminTemplateManager::assets("nstranslator","plugins/fields_translator_func.js")."'></script>"
        );

        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets("nstranslator","plugins/fields-translator.css")
        );


        /*
        * Manage Store output
        */

        $this->setupFieldTranslator();

        /*
         * End Manage Store output
         */
    }

    private function setupFieldTranslator(){


        /*
        * Manage Store output
        */

        if(ModulesChecker::isEnabled("store") && !self::whenIs("store/edit")){
            ActionsManager::register("store","func_getStores",function ($list){
                $lang = FieldsTranslator::getLang();
                foreach ($list as $k => $value){
                    $list[$k]['name'] = $this->transObj($value['name'],$lang);
                    $list[$k]['detail'] = $this->transObj($value['detail'],$lang);
                }
                return $list;
            });
        }

        /*
         * End Manage Store output
         */

        /*
        * Manage Offer output
        */

        if(ModulesChecker::isEnabled("offer") && !self::whenIs("offer/edit")){
            ActionsManager::register("offer","func_getOffers",function ($list){
                $lang = FieldsTranslator::getLang();
                foreach ($list as $k => $value){
                    $list[$k]['name'] = $this->transObj($value['name'],$lang);
                    $list[$k]['description'] = $this->transObj($value['description'],$lang);
                    $list[$k]['store_name'] = $this->transObj($value['store_name'],$lang);
                }
                return $list;
            });
        }

        /*
         * End Manage offer output
         */


        /*
       * Manage Event output
       */

        if(ModulesChecker::isEnabled("event") && !self::whenIs("event/edit")){
            ActionsManager::register("event","func_getEvents",function ($list){

                $lang = FieldsTranslator::getLang();

                foreach ($list as $k => $value){
                    $list[$k]['name'] = $this->transObj($value['name'],$lang);
                    $list[$k]['description'] = $this->transObj($value['description'],$lang);
                    $list[$k]['store_name'] = $this->transObj($value['store_name'],$lang);
                }
                return $list;
            });
        }

        /*
         * End Manage offer output
         */


        /*
           * Manage Category output
           */

        if(ModulesChecker::isEnabled("category")){
            ActionsManager::register("category","func_getCategories",function ($list){
                $lang = FieldsTranslator::getLang();
                foreach ($list as $k => $value){
                    $list[$k]['name'] = $this->transObj($value['name'],$lang);
                }
                return $list;
            });
        }

        /*
         * End Manage Category output
         */

    }

    public static function getLang(){
        $default_lang = Translate::getDefaultLangCode();
        $ctx = &get_instance();

        $lang = Security::decrypt($ctx->input->get_request_header('Language', $default_lang));
        if($lang=="")
            $lang = $default_lang;

        return $lang;
    }

    public  static function transObj0($obj,$lang){
        if(self::isJson0($obj)){
            $obj = json_decode($obj,JSON_OBJECT_AS_ARRAY);
            if(isset($obj[$lang])){
                return $obj[$lang];
            }else{
                foreach ($obj as $value){
                    return $value;
                }
            }
        }
        return $obj;
    }

    public static function  isJson0($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }


    private function transObj($obj,$lang){
        $obj = Text::output($obj);
        if($this->isJson($obj)){
            $obj = json_decode($obj,JSON_OBJECT_AS_ARRAY);
            if(isset($obj[$lang])){
                return $obj[$lang];
            }else{
                foreach ($obj as $value){
                    return $value;
                }
            }
        }
        return $obj;
    }

    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function whenIs($path){
        $url = current_url();
        if(preg_match("#".$path."#",$url)){
            return TRUE;
        }
        return FALSE;
    }

    public function whenApiIsActive($module){

        if(!is_string($module)){
            $module = strtolower(get_class($module));
        }

        $cxt = &get_instance();

        $uri0 = $cxt->uri->segment(1);
        $uri1 = $cxt->uri->segment(2);

        if($uri0 == "api" && $uri1==$module){
            return TRUE;
        }else if($uri0 == $module && $uri1=="api"){
            return TRUE;
        }

        return FALSE;
    }

    public function whenAdminIsActive($module){

        if(!is_string($module)){
            $module = strtolower(get_class($module));
        }

        $cxt = &get_instance();

        $uri0 = $cxt->uri->segment(1);
        $uri1 = $cxt->uri->segment(2);

        if($uri0 == __ADMIN && $uri1==$module){
            return TRUE;
        }else if($uri0 == __ADMIN && $uri1=="" && $module=="cms"){
            return TRUE;
        }

        return FALSE;
    }



}
