<?php

/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 3/13/2017
 * Time: 22:27
 */


class FileUploader{

    private $context;
    private $files;
    public $ext = array(
        'text/plain',
        // images
        'image/png',
        'image/jpeg',
        'image/jpeg',
        'image/jpeg',
        'image/gif',
        'image/bmp',
        'image/vnd.microsoft.icon',
        'image/tiff',
        'image/tiff',
        'image/svg+xml',
        'image/svg+xml',

        // archives
        'application/zip',

        // audio/video
        'audio/mpeg',
        'video/quicktime',
        'video/quicktime',

        // adobe
        'application/pdf',
        'image/vnd.adobe.photoshop',
        'application/postscript',

        // ms office
        'application/msword',
        'application/rtf',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

        // open office
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
    );
    public $size;
    private $errors;
    private $namedir;
    private $download;
    private $name;




    public function __construct($context=NULL,$data=array()) {

        $this->context = $context;

        $this->files = $data;
        $this->size = (1048576 * MAX_FILE_UPLOAD);
        $this->errors = array();
        $this->namedir = time().rand(000, 99999);
        $this->download = FALSE;
        $this->name = "";

    }



    public function start(){

        $data=array(
          "result"      => array(), "errors"    => array()
        );

        $rootFolder = $this->createDir();

        $dis = Path::addPath($rootFolder,array($this->files['name']));

        if(true) {

            if (isset($this->files['type']) AND !in_array(strtolower($this->files['type']), $this->ext)) {
                $this->errors['type'] = "File type unsupported";
            }

            if (isset($this->files['size']) AND $this->files['size'] > $this->size) {
                $this->errors['size'] = "Error in size, the max size :" . $this->size." MB";
            }

            if (empty($this->errors)) {

                if (move_uploaded_file($this->files['tmp_name'], $dis)) {

                    if (file_exists($dis)) {

                        $url = "";
                        $data = FileManager::_openDir($this->namedir);
                        if(isset($data[0]["url"])){
                            $url = $data[0]["url"];
                        }

                        $data['result'] = array(
                            "dis"   => $dis,
                            "dir"   => $this->namedir,
                            "html"  => "<div class=\"uploaded-file item_".$this->namedir."\">
                                         <a  target='_blank' href='$url' ><i class=\"fa fa-paperclip\" aria-hidden=\"true\"></i> &nbsp;&nbsp;<strong>".$this->files["name"]."</strong></a>
                                         <a href=\"#\"  data=\"$this->namedir\" id=\"delete\"><i class=\"fa fa-trash\"></i>&nbsp;&nbsp;</a>
                                        </div><input id=\"image-data\" type=\"hidden\" value=\"". md5($this->namedir)."\">"
                        );


                    }
                }

            }
        }

        if(!empty($this->errors)){
            $data["errors"] = $this->errors;

        }

        return $data;
    }



    public function getErrors() {
        return $this->errors;
    }

    private function createDir($specific_folder=""){

        $rootFolder = Path::getPath(array("uploads","files"));

        if(!file_exists( $rootFolder ))
            mkdir(  $rootFolder );


        //add specific folder
        if($specific_folder!=""){

            $rootFolder = Path::addPath($rootFolder,array($specific_folder));
            if(!file_exists( $rootFolder ))
                mkdir(  $rootFolder );

        }

        $folder = Path::addPath($rootFolder,array($this->namedir));

        if(!file_exists( $folder ))
            mkdir(  $folder );

        return $folder;
    }


}
