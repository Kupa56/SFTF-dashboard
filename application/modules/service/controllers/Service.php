<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init('service');
    }

    public function onLoad()
    {

        $this->load->model("service/Service_model", "mService");

    }

    public function onCommitted($isEnabled)
    {

        if(!$isEnabled)
            return;


        ActionsManager::register("store","func_getStores",function ($list){

            foreach ($list as $key => $value){
                $list[$key]['nbrServices'] = $this->db->where("hidden", 0)->where("store_id", $value['id_store'])->count_all_results("service");
            }

            return $list;
        });




        //store
        NSModuleLinkers::newInstance('service','getData',function ($args){

            $services = $this->db->where('id',$args['id'])
                ->get('service',1)->result_array();

            if(isset($services[0])){

                return array(
                    'label' => $services[0]['label'],
                    'label_description' => $services[0]['description'],
                );
            }

            return NULL;
        });

    }


    public function plug($params = array())
    {

        $data['var'] = "result_" . rand(9999, 10000);
        $data['id'] = $params['id'];

        if (isset($params['label']))
            $data['label'] = $params['label'];

        if (isset($params['title']))
            $data['title'] = $params['title'];

        return array(
            'html' => $this->load->view('service/plug/html', $data, TRUE),
            'script' => $this->load->view('service/plug/script', $data, TRUE),
            'var' => $data['var'],
        );

    }


    public function onInstall()
    {

        $this->mService->createTable();

        return TRUE;

    }

    public function onUpgrade()
    {

        $this->mService->createTable();

        return TRUE;
    }

    public function onEnable()
    {
        return TRUE;
    }


}