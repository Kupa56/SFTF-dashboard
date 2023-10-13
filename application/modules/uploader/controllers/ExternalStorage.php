<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

require APPPATH.'/modules/uploader/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

class ExternalStorage extends MAIN_Controller
{
    public function check($fileName){
        try {
            return Modules::run('uploader/GoogleCloudBucket/listFiles',$fileName);
        }catch (Exception $e){
            return [];
        }
    }

    public function upload($fileName){


        $imagesFromLocal = _getAllSizes($fileName);

        foreach ($imagesFromLocal as $image){

            if(!is_array($image))
                continue;

            // get local file for upload testing
            $fileContent = file_get_contents(
                $image['path']
            );

            // NOTE: if 'folder' or 'tree' is not exist then it will be automatically created !
            $cloudPath = $fileName.'/' . $image['name'];

            Modules::run('uploader/GoogleCloudBucket/uploadFile', $fileContent, $cloudPath);

        }

        return  $imagesFromLocal;
    }


    public function setup(){



    }
}

/* End of file UploaderDB.php */