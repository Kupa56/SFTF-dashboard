<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 4/19/19
 * Time: 13:56
 */

class CMSUtils{


    public static function getPTemplates(){


        $templatePathTpl = Path::getPath(array(
            'views',
            'frontend',
            ConfigManager::getValue("DEFAULT_TEMPLATE"),
            "pages/*.tpl"
        ));

        $templatePathPhp = Path::getPath(array(
            'views',
            'frontend',
            ConfigManager::getValue("DEFAULT_TEMPLATE"),
            "pages/*.php"
        ));

        $templates = array(
            "pages/content"
        );

        //for tpl
        foreach (glob($templatePathTpl) as $filename)
        {

            $filename = basename($filename);
            $newFileName = substr($filename, 0 , (strrpos($filename, ".")));

            if($newFileName != "content"){
                $templates[] = "pages/".$newFileName;
            }
        }

        //for php
        foreach (glob($templatePathPhp) as $filename)
        {

            $filename = basename($filename);
            $newFileName = substr($filename, 0 , (strrpos($filename, ".")));

            if($newFileName != "content"){
                $templates[] = "pages/".$newFileName;
            }
        }

        return $templates;
    }


    public static function addNewSlug($slug,$uri){

        $ctx = &get_instance();

        return $ctx->mCMS->addSlug(array(
            'slug' => $slug,
            'uri' => $uri,
        ));

    }


    public static function createSlug($str, $delimiter = '-'){
        return parse_slug($str,$delimiter);
    }

}


    if (!function_exists("parse_slug")) {
        function parse_slug($str, $delimiter = '-'){
            $slug = Text::removeSymbols($str);
            $slug = preg_replace("`[^\w]+`", "-", $slug);
            $slug = str_replace(" ", "-", $slug);
            return trim($slug, '-');
        }
    }


    if (!function_exists("error404")) {
        function error404(){
            return 404;
        }
    }




