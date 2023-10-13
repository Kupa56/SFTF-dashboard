<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

require APPPATH.'/modules/uploader/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

class GoogleCloudBucket extends MAIN_Controller
{

    public $privateKeyFileContent = '';
    private $bucketName = "xxxxxxx";
    private $bucketBaseURL = "https://storage.googleapis.com";


    public function __construct()
    {

        if(ConfigManager::getValue('APP_STORAGE') != "google_bucket"){
           return;
        }

        $file = ConfigManager::getValue('APP_STORAGE_BUCKET_FILE');

        if(!file_exists($file)){
            echoJson(array(Tags::SUCCESS=>0,Tags::ERRORS=>["err"=>"File doesn't exists"]));
            exit();
        }

        $this->privateKeyFileContent = file_get_contents($file);
        $this->bucketName = ConfigManager::getValue('APP_STORAGE_BUCKET_NAME');

    }


    function uploadFile( $fileContent, $cloudPath)
    {
        $privateKeyFileContent = $this->privateKeyFileContent;
        // connect to Google Cloud Storage using private key as authentication
        try {
            $storage = new StorageClient([
                'keyFile' => json_decode($privateKeyFileContent, true)
            ]);
        } catch (Exception $e) {
            // maybe invalid private key ?
            print $e;
            return false;
        }

        // set which bucket to work in
        $bucket = $storage->bucket($this->bucketName);

        // upload/replace file
        $storageObject = $bucket->upload(
            $fileContent,
            ['name' => $cloudPath]
        // if $cloudPath is existed then will be overwrite without confirmation
        // NOTE:
        // a. do not put prefix '/', '/' is a separate folder name  !!
        // b. private key MUST have 'storage.objects.delete' permission if want to replace file !
        );

        // is it succeed ?
        return $storageObject != null;
    }

    function getFileInfo($cloudPath)
    {
        $privateKeyFileContent = $this->privateKeyFileContent;
        // connect to Google Cloud Storage using private key as authentication
        try {
            $storage = new StorageClient([
                'keyFile' => json_decode($privateKeyFileContent, true)
            ]);
        } catch (Exception $e) {
            // maybe invalid private key ?
            print $e;
            return false;
        }

        // set which bucket to work in
        $bucket = $storage->bucket($this->bucketName);
        $object = $bucket->object($cloudPath);
        return $object->info();
    }


    function listFiles($directory = null) {

        $privateKeyFileContent = $this->privateKeyFileContent;
        // connect to Google Cloud Storage using private key as authentication
        try {
            $storage = new StorageClient([
                'keyFile' => json_decode($privateKeyFileContent, true)
            ]);
        } catch (Exception $e) {
            return [];
        }

        // set which bucket to work in
        $bucket = $storage->bucket($this->bucketName);

        if ($directory == null) {
            // list all files
            $objects = $bucket->objects();
        } else {
            // list all files within a directory (sub-directory)
            $options = array('prefix' => $directory);
            $objects = $bucket->objects($options);
        }

        $result = [];

        foreach ($objects as $object) {

            $path = parse_url($object->name(), PHP_URL_PATH);

            $result["name"] = explode("/",$object->name())[0];

            $name = explode('.',basename($path) );
            $result[ $name[0] ] = [
                'name' => basename($path),
                'path' => $this->bucketBaseURL.'/'.$this->bucketName.'/'.$object->name(),
                'url' => $this->bucketBaseURL.'/'.$this->bucketName.'/'.$object->name(),
                'ext' => $name[1],
            ];

        }

        return $result;
    }

}

/* End of file UploaderDB.php */