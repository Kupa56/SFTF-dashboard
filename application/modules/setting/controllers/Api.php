<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Api extends API_Controller {

    public function __construct(){
        parent::__construct();
    }


    public function app_initialization(){
        echo json_encode(array(Tags::SUCCESS=>1,"token"=>CRYPTO_KEY));
    }


    public function getAppConfig()
    {
        $data = $this->mConfigModel->getAppConfig();
        echo json_encode(array(Tags::SUCCESS => 1, Tags::RESULT => $data), JSON_FORCE_OBJECT);
    }

    public function content_report(){

        $title = RequestInput::post('title');
        $owner_user_id = RequestInput::post('owner_user_id');
        $reported_by_user_id = RequestInput::post('reported_by_user_id');
        $content = RequestInput::post('content');

        $result = $this->mSettingModel->sendContentReport(
            $title,
            $content,
            $owner_user_id,
            $reported_by_user_id
        );

        echo json_encode($result);return;
    }

    public function save_logs(){

        $key = RequestInput::post('key');
        $value = RequestInput::post('message');
        $platform = RequestInput::post('platform');

        if(file_exists('logs/'.$platform.'.html')){
            $file_content = @url_get_content('logs/'.$platform.'.html');
        }else
            $file_content = "";

        $file_content = date("Y-m-d H:i:s")." --> <span style='color: #00a65a'>$key</span> --> <span style='color: red'>".$value."</span><br><br>".$file_content."<br><br>";

        @file_put_contents('logs/'.$platform.'.html', $file_content);

        echo json_encode(array(Tags::SUCCESS=>1));return;
    }

}

/* End of file SettingDB.php */