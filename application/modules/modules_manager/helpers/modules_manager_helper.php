<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 4/19/19
 * Time: 14:28
 */

class ModuleManagerTags{
    const FIELDS = "fields";
}

class ModuleManager{


    public static function loadAllModules(){

        $modules = array();

        $external = FModuleLoader::loadExternalModules();
        $core = FModuleLoader::loadCoreModules();

        foreach ($external as $value)
            $modules[] = $value;

        foreach ($core as $value)
            $modules[] = $value;

        return $modules;
    }

    public static function checkCompatibility($v1,$v2){

        $v1 = preg_replace("#([0-9]).([0-9]).([0-9])#","$1$2$3",$v1);
        $v2 = preg_replace("#([0-9]).([0-9]).([0-9])#","$1$2$3",$v2);

        $v1 = intval($v1);
        $v2 = intval($v2);

        if($v1<=$v2)
            return TRUE;

        return FALSE;
    }



    public static function getLoadedModules(){

        $modules = ModulesChecker::getLoadedModules();
        $modules = ModulesChecker::getLoadedModules();

        return $modules;

    }




    public static function fetch(){

        $modules = ModulesChecker::getLoadedModules();

        foreach ($modules as $key => $value){
            $m_data = FModuleLoader::getModuleDetail($value['module_name']);

            if(empty($m_data)){
                unset($modules[$key]);
                continue;
            }

            $modules[$key]['detail'] = $m_data;
            $modules[$key]['detail']['icon'] = self::getModuleImage($value['module_name'], $modules[$key]['detail']['icon']);

            if(isset($modules[$key]['detail']['module_system'])
             and $modules[$key]['detail']['module_system']==1
             and $modules[$key]['_enabled']==1 ){
                unset($modules[$key]);
            }
        }


        return $modules;

    }

    private static function getModuleImage($module_name,$icon){

        $path = Path::getPath(array('application','modules',$module_name,'config',$icon));

        if(!file_exists($path))
            $image = AdminTemplateManager::assets("modules_manager",'images/plug_logo.jpg');
        else
            $image = base_url("application/modules/$module_name/config/$icon");

        return $image;
    }


    public static function isInstalled($moduleName){


        if(!is_string($moduleName)){
            $moduleName = strtolower(get_class($moduleName));
        }

        if(ModulesChecker::getLoadedModules()!=NULL)
            foreach (ModulesChecker::getLoadedModules() as $module){
                if($moduleName==$module['module_name'] AND $module['_installed']==1){
                    return TRUE;
                }
            }

        return FALSE;
    }

}



function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function delTree($dir) {
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if( substr( $file, -1 ) == '/' )
            delTree( $file );
        else
            unlink( $file );
    }

    if (is_dir($dir)) rmdir( $dir );

}