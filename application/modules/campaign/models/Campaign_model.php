<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Campaign_model extends CI_Model
{

    private $limit = 15;

    public function __construct()
    {
        parent::__construct();
        $this->addAgrrementField();
        $this->addNotificationField();
    }

    public function hasUnPushedCampaign($cid){


    }

    public function updateNotificationAgreement($id, $user_id, $agreement)
    {

        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);

        $this->db->update('bookmarks', array(
            'notification_agreement' => intval($agreement)
        ));

        return TRUE;
    }


    public function getLast24h()
    {

        $last_24 = array();


        for ($i = 0; $i >= -24; $i--) {
            $last_24[] = date("Y-m-d H:i:s", strtotime($i . " hour"));
        }


        return $last_24;

    }

    public function getLast24hD()
    {

        $last_week = $this->getLast24h();

        foreach ($last_week as $k => $value) {
            $last_week[$k] = date("h:i A", strtotime($value));
        }

        return $last_week;

    }

    public function getLastMonth()
    {

        $last_week = array();


        for ($i = 0; $i >= -15; $i--) {
            $last_week[] = date("Y-m-d", strtotime($i . " day"));
        }


        return $last_week;

    }

    public function getLastMonthD()
    {

        $last_week = $this->getLastMonth();

        foreach ($last_week as $k => $value) {
            $last_week[$k] = date("d, M", strtotime($value));
        }

        return $last_week;

    }


    public function getLastWeek()
    {

        $last_week = array();


        for ($i = 0; $i >= -7; $i--) {
            $last_week[] = date("Y-m-d", strtotime($i . " day"));
        }


        return $last_week;

    }

    public function getLastWeekD()
    {

        $last_week = $this->getLastWeek();

        foreach ($last_week as $k => $value) {
            $last_week[$k] = date("d, M", strtotime($value));
        }

        return $last_week;

    }

    const WEEK = 1;
    const MONTH = 2;


    public function getCampaignReport($campaign_id = 0, $week = 1)
    {

        $analytics = array();

        if ($week == Campaign_model::WEEK)
            $last_week = $this->getLastWeek();
        else
            $last_week = $this->getLastMonth();


        foreach ($last_week as $key => $time) {


            $current = date("H:i:s", time());
            $start = date("Y-m-d 00:00:00", strtotime($time));
            $end = date("Y-m-d", strtotime($time)) . " " . $current;

            $index = date("Y-m-d", strtotime($time));

            $this->db->where("created_at >=", $start);
            $this->db->where("created_at <=", $end);
            $this->db->where('module', "campaign");
            $this->db->where('module_id', $campaign_id);
            $this->db->where('action', "markView");

            $count = $this->db->count_all_results("ns_tracker");
            $analytics['markView'][$index] = $count;


            $this->db->where("created_at >=", $start);
            $this->db->where("created_at <=", $end);
            $this->db->where('module', "campaign");
            $this->db->where('module_id', $campaign_id);
            $this->db->where('action', "markReceive");

            $count = $this->db->count_all_results("ns_tracker");
            $analytics['markReceive'][$index] = $count;

        }


        return $analytics;

    }

    public function getCampaignReport24($campaign_id = 0)
    {

        $analytics = array();

        $last_24 = $this->getLast24h();

        foreach ($last_24 as $key => $time) {

            $start = date("Y-m-d H:i:s", strtotime($time));
            $end = date("Y-m-d H:i:s", strtotime("-1 hour", strtotime($time)));

            $index = date("Y-m-d h:i A", strtotime($time));

            $this->db->where("created_at <=", $start);
            $this->db->where("created_at >=", $end);
            $this->db->where('module', "campaign");
            $this->db->where('module_id', $campaign_id);
            $this->db->where('action', "markView");

            $count = $this->db->count_all_results("ns_tracker");
            $analytics['markView'][$index] = $count;


            $this->db->where("created_at <=", $start);
            $this->db->where("created_at >=", $end);
            $this->db->where('module', "campaign");
            $this->db->where('module_id', $campaign_id);
            $this->db->where('action', "markReceive");

            $count = $this->db->count_all_results("ns_tracker");
            $analytics['markReceive'][$index] = $count;

        }


        return $analytics;

    }


    public function getCampaignsAnalytics($months = array(), $owner_id = 0)
    {

        $analytics = array();

        foreach ($months as $key => $m) {

            $last_month = date("Y-m-t", strtotime($key));
            $start_month = date("Y-m-1", strtotime($key));

            $this->db->where("created_at >=", $start_month);
            $this->db->where("created_at <=", $last_month);


            if ($owner_id > 0)
                $this->db->where('user_id', $owner_id);

            $count = $this->db->count_all_results("campaign");

            $index = date("m", strtotime($start_month));

            $analytics['months'][$key] = $count;

        }

        if ($owner_id > 0)
            $this->db->where('user_id', $owner_id);

        $analytics['count'] = $this->db->count_all_results("campaign");

        $analytics['count_label'] = Translate::sprint("Campaigns");
        $analytics['color'] = "#ff7701";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-square-rounded-badge-outline\"></i>";
        $analytics['label'] = "Campaign";
        $analytics['link'] = admin_url("campaign/campaigns");


        return $analytics;

    }


    public function validateAndPushCampaign($id)
    {

        if ($id > 0) {

            $this->db->where("id", intval($id));
            $this->db->where("status", -1);
            $campaign = $this->db->get("campaign");
            $campaign = $campaign->result_array();

            if (count($campaign) > 0) {

                $campaign[0]['id_campaign'] = $campaign[0]['id'];
                $this->pushCampaign($campaign[0]);

                $this->db->where("id", intval($id));
                $this->db->where("status", -1);
                $this->db->update("campaign", array(
                    "status" => 1
                ));
            }

        }

    }

    public function getPendingCampaigns($params = array())
    {
        extract($params);

        $this->db->where("status", -1);
        $c = $this->db->count_all_results("campaign");

        return $c;
    }

    public function markView($params = array())
    {

        extract($params);

        if (isset($campaignId) and $campaignId > 0) {

            $this->db->where("id", $campaignId);
            $campaign = $this->db->get("campaign", 1);
            $campaign = $campaign->result_array();

            if (count($campaign) > 0) {

                $result = $this->addTrack(array(
                    'module' => 'campaign',
                    'module_id' => $campaignId,
                    'action' => 'markView',
                    'guest_id' => $params['guest_id'],
                    'user_id' => $params['user_id'],
                ));

                if ($result) {


                    $t = ($campaign[0]["seen"] + 1);

                    $dataToUpdate = array(
                        "seen" => $t
                    );

                    if ($t == $campaign[0]["estimation"])
                        $dataToUpdate['status'] = 2;

                    $this->db->where("id", $campaignId);
                    $this->db->update("campaign", $dataToUpdate);
                }

                return array(Tags::SUCCESS => 1);
            }

        }


        return array(Tags::SUCCESS => 0);

    }

    public function markReceive($params = array())
    {

        extract($params);

        if (isset($campaignId) and $campaignId > 0) {

            $this->db->where("id", $campaignId);
            $campaign = $this->db->get("campaign", 1);
            $campaign = $campaign->result_array();

            if (count($campaign) > 0) {

                $result = $this->addTrack(array(
                    'module' => 'campaign',
                    'module_id' => $campaignId,
                    'action' => 'markReceive',
                    'guest_id' => $params['guest_id'],
                    'user_id' => $params['user_id'],
                ));

                if ($result) {

                    $this->db->where("id", $campaignId);
                    $t = ($campaign[0]["seen"] + 1);

                    $dataToUpdate = array(
                        "received" => $t
                    );
                    $this->db->update("campaign", $dataToUpdate);
                }

                return array(Tags::SUCCESS => 1);
            }

        }


        return array(Tags::SUCCESS => 0, "s" => $params);

    }

    public function getEstimation($params = array())
    {

        $errors = array();

        extract($params);

        $nbr = 0;

        if (!isset($module_name)) {
            $errors[] = Translate::sprint("Module Name is not exists");
        }

        if (!isset($module_id)) {
            $errors[] = Translate::sprint("Module ID is not exists");
        }


        if (!isset($user_id)) {
            $errors[] = Translate::sprint("User ID is not exists");
        }


        if (!empty($errors)) {
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
        }

        if (!isset($custom_parameters))
            $custom_parameters = array();


        $modules = CampaignManager::load();

        $get_number_estimated = 0;

        if (isset($modules[$module_name]) && isset($modules[$module_name]['callback_input'])) {

            $result = call_user_func($modules[$module_name]['callback_input'], array(
                'user_id' => $user_id,
                'module_name' => $module_name,
                'module_id' => $module_id,
                'custom_parameters' => $custom_parameters
            ));

            if ($result[Tags::SUCCESS] == 1) {

                if (isset($custom_parameters['getting_option'])
                    && $custom_parameters['getting_option'] == 2) {

                    foreach ($result[Tags::RESULT] as $p) {
                        if ((RADUIS_TRAGET * 1024) > $p['distance']) {
                            $get_number_estimated++;
                        } else {
                            break;
                        }
                    }

                } else {
                    $get_number_estimated = count($result[Tags::RESULT]);
                }

            }


            return array(Tags::SUCCESS => 1, Tags::RESULT => $get_number_estimated);

        } else {
            return array(Tags::SUCCESS => 0, Tags::ERRORS => array("err" => Translate::sprint("Module is not valid")));
        }


    }


    public function getEstimatedGuests($params = array())
    {

        extract($params);

        $get_guests_objects = array();


        if (!isset($module_name)) {
            $errors[] = Translate::sprint("Module Name is not exists");
        }

        if (!isset($module_id)) {
            $errors[] = Translate::sprint("Module ID is not exists");
        }


        if (!isset($user_id)) {
            $errors[] = Translate::sprint("User ID is not exists");
        }


        if (!isset($id_campaign)) {
            $errors[] = Translate::sprint("Campaign ID is not exists");
        }


        if (!empty($errors)) {
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
        }


        if (!isset($custom_parameters))
            $custom_parameters = array();


        $get_guests_objects = array();


        $modules = CampaignManager::load();

        if (isset($modules[$module_name]) && isset($modules[$module_name]['callback_input'])) {

            $result = call_user_func($modules[$module_name]['callback_input'], array(
                'user_id' => $user_id,
                'module_name' => $module_name,
                'module_id' => $module_id,
                'custom_parameters' => $custom_parameters,
            ));

            if ($result[Tags::SUCCESS] == 1) {
                $i = 0;

                if (isset($custom_parameters['getting_option'])
                    && $custom_parameters['getting_option'] == 2) {
                    foreach ($result[Tags::RESULT] as $p) {
                        if ((RADUIS_TRAGET * 1024) > $p['distance']) {
                            $get_guests_objects[$i]['fcm'] = $p['fcm_id'];
                            $get_guests_objects[$i]['guest_id'] = $p['id'];
                            $get_guests_objects[$i]['sender_id'] = $p['sender_id'];
                            $get_guests_objects[$i]['campaign_id'] = $id_campaign;
                            $i++;
                        } else {
                            break;
                        }
                    }
                } else {
                    foreach ($result[Tags::RESULT] as $p) {
                        $get_guests_objects[$i]['fcm'] = $p['fcm_id'];
                        $get_guests_objects[$i]['guest_id'] = $p['id'];
                        $get_guests_objects[$i]['sender_id'] = $p['sender_id'];
                        $get_guests_objects[$i]['campaign_id'] = $id_campaign;
                        $i++;
                    }
                }

            }

        }

        return $get_guests_objects;
    }


    public function getCampaigns($params = array())
    {


        extract($params);
        $errors = array();
        $data = array();


        if (!isset($page)) {
            $page = 1;
        }

        if (isset($limit) && $limit > 0)
            $this->limit = intval($limit);

        if (isset($user_id) and $user_id > 0) {
            $data ['user_id'] = intval($user_id);
        }

        if (isset($module_name) and ModulesChecker::isEnabled($module_name)) {
            $data ['module_name'] = $module_name;
        }

        if (isset($module_id) and $module_id > 0) {
            $data ['module_id'] = intval($module_id);
        }

        if (isset($campaign_id) and $campaign_id > 0) {
            $data ['id'] = intval($campaign_id);
        }


        if (isset($status) and ($status >= 1 or $status < 0)) {
            $data ['status'] = intval($status);
        } else if (isset($status) && $status == 0) {
            $data ['status >='] = -1;
        }


        $this->db->where($data);

        $this->db->from("campaign");
        $count = $this->db->count_all_results();


        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($this->limit);
        $pagination->calcul();


        $this->db->where($data);


        $this->db->from("campaign");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $this->db->order_by("id", "DESC");

        $stores = $this->db->get();
        $offers = $stores->result_array();


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $offers);
    }


    public function createCampaign($params = array())
    {


        extract($params);

        $errors = array();
        $data = array();


        if (isset($custom_title) and $custom_title != "") {
            $data['name'] = Text::input($custom_title);
        } else {
            $errors['custom_title'] = Translate::sprint(Messages::CAMPAIGN_NAME_IS_EMPTY);
        }

        if (isset($custom_text) and $custom_text != "") {
            $data['text'] = Text::input($custom_text);
        } else {
            $errors['custom_title'] = Translate::sprint(Messages::CAMPAIGN_NAME_IS_EMPTY);
        }

        if (isset($module_name) and ModulesChecker::isEnabled($module_name)) {
            $data['module_name'] = $module_name;
        } else {
            $errors['module_name'] = Translate::sprint(Messages::TYPE_NOT_VALID);;
        }

        if (isset($module_id) and $module_id > 0) {
            $data['module_id'] = intval($module_id);
        } else {
            $errors['module_id'] = Translate::sprint(Messages::SOMETHING_WRONG_12);
        }

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = $user_id;
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_FOUND);
        }

        if (isset($custom_parameters) and !empty($custom_parameters)) {

        } else {
            $errors['custom_parameters'] = Translate::sprint("Custom_parameters invalid!");
        }


        if (isset($t) and $t > 0) {
            $data['estimation'] = intval($t);
        }


        $data['date_created'] = date("Y-m-d", time());
        $data['created_at'] = date("Y-m-d H:i:s", time());

        if (empty($errors) and isset($user_id) and $user_id > 0) {

            $nbr_campaign_monthly = UserSettingSubscribe::getUDBSetting($user_id, KS_NBR_CAMPAIGN_MONTHLY);
            $push_campaign_auto = UserSettingSubscribe::getUDBSetting($user_id, KS_PUSH_CAMPAIGN_AUTO);

            if ($nbr_campaign_monthly > 0 || $nbr_campaign_monthly == -1) {

                if ($push_campaign_auto == 1)
                    $data['status'] = 1;
                else
                    $data['status'] = -1;

                $this->db->insert("campaign", $data);
                $id = $this->db->insert_id();
                $data['id_campaign'] = $id;

                if ($nbr_campaign_monthly > 0) {
                    $nbr_campaign_monthly--;
                    UserSettingSubscribe::refreshUSetting($user_id, KS_NBR_CAMPAIGN_MONTHLY, $nbr_campaign_monthly);
                }


                //push campaign
                if ($push_campaign_auto == 1) {
                    $data['custom_parameters'] = $custom_parameters;
                    $this->pushCampaign($data);
                }

                return array(Tags::SUCCESS => 1);

            } else {
                $errors["offers"] = Translate::sprint(Messages::EXCEEDED_MAX_NBR_CAMPAIGNS);
            }

        } else {
            $errors['store'] = Translate::sprint(Messages::SOMETHING_WRONG_12);;
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    public function archiveCampaign($params = array())
    {

        extract($params);
        $errors = array();
        $data = array();

        $user_id = $this->mUserBrowser->getData("id_user");

        if (isset($campaign_id) and $campaign_id > 0 && $user_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("id", $campaign_id);
            $this->db->update("campaign", array(
                "status" => -2
            ));

            $this->db->where("campaign_id", $campaign_id);
            $this->db->delete("pending_campaigns");

            return array(Tags::SUCCESS => 1);

        }

        return array(Tags::SUCCESS => 0);
    }


    public function duplicateCampaign($params = array())
    {

        extract($params);
        $errors = array();
        $data = array();

        $user_id = $this->mUserBrowser->getData("id_user");

        if (isset($campaign_id) and $campaign_id > 0 && $user_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("id", $campaign_id);
            $campaign = $this->db->get("campaign", 1);
            $campaign = $campaign->result_array();


            if (count($campaign) > 0) {
                $campaign[0]['t'] = $campaign[0]['estimation'];
                return $this->createCampaign($campaign[0]);
            }
        }

        return array(Tags::SUCCESS => 0);
    }


    private function pushCampaign($params)
    {

        $fcms = $this->getEstimatedGuests(array(
            "id_campaign" => $params['id_campaign'],
            "module_name" => $params['module_name'],
            "module_id" => $params['module_id'],
            "user_id" => $params['user_id'],
            "custom_parameters" => $params['custom_parameters'],
        ));

        //adde

        //push using FCM server
        if (PUSH_CAMPAIGNS_WITH_CRON == TRUE) {
            //insert into table pending cronjon executed
            foreach ($fcms as $value) {

                $value['date_created'] = date("Y-m-d", time());
                $this->db->insert("pending_campaigns", $value);

            }
        } else {

            //push currently
            foreach ($fcms as $guest) {

                $resultJson = $this->pushSingleCampaignToSingleUser(array(
                    "campaign_id" => $params['id_campaign'],
                    "fcm" => $guest['fcm'],
                    "guest_id" => $guest['guest_id'],
                ));

            }

        }

    }

    private function save_nshistoric($params, $fcms)
    {


        $module_data = $this->getDataFromCampaign($params['id_campaign']);

        foreach ($fcms as $value) {

            $this->db->where('guest_id', $value['guest_id']);
            $user = $this->db->count_all_results('user');

            if ($user > 1) {

                $this->db->where('guest_id', $value['guest_id']);
                $user = $this->db->get('user', 1);
                $user = $user->result();
                $user = $user[0];

                $auth_type = "user";
                $auth_id = $user->id_user;

            } else {

                $auth_type = "guest";
                $auth_id = $value['guest_id'];

            }

            $image = "";

            if (isset($module_data['image_id'])) {
                $image = $module_data['image_id'];
            }


            //add historic
            $historic = NSHistoricManager::add(array(
                'module' => $params['module_name'],
                'module_id' => $params['module_id'],
                'auth_type' => $auth_type,
                'auth_id' => $auth_id,
                'image' => json_encode(array($image)),
                'label' => isset($module_data['sub-title']) ? $module_data['sub-title'] : "",
                /*'label_description'   => Translate::sprintf("balabla blabla  this %s \"%s\"",array(
                    $data['type'],$data['name']
                )),*/
                'label_description' => $module_data['title'],
            ));


            if (isset($historic[Tags::RESULT])) {

                $cid = $params['id_campaign'];
                $nid = intval($historic[Tags::RESULT]);

                $this->db->where('id', $nid);
                $this->db->update('nsh_notifications', array(
                    'campaign_id' => $cid
                ));
            }

        }

    }



    public function retrieveLogs($cid,$unpushed=FALSE){

        $this->db->where("campaign_id", $cid);

        if(!$unpushed)
            $this->db->where("failed !=", 0);
        else
            $this->db->where("failed", 0);

        $pending_campaigns = $this->db->get("pending_campaigns",100);
        $pending_campaigns = $pending_campaigns->result_array();

        foreach ($pending_campaigns as $key => $pc){

            $this->db->select("guest.platform");
            $this->db->from("guest");
            $this->db->where("id",$pc['guest_id']);
            $guest_data = $this->db->get();
            $guest_data = $guest_data->result();

            if(isset($guest_data[0]))
                $pending_campaigns[$key]['platform'] = $guest_data[0]->platform;
            else
                $pending_campaigns[$key]['platform'] = "N/A";
        }

        return $pending_campaigns;

    }

    public function pushPendingCampaigns()
    {

        $currentDate = date("Y-m-d H:i:s", time());
        $currentDate = MyDateUtils::convert($currentDate, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d H:i:s");

        //$this->db->where('push_at <=',  $currentDate  );
        $this->db->where('failed', 0);
        $pendings = $this->db->get("pending_campaigns", NBR_PUSHS_FOR_EVERY_TIME);
        $pendings = $pendings->result_array();

        $this->load->model("notification/notification_model");

        $logs = "";
        foreach ($pendings as $campaign) {


            $resultJson = $this->pushSingleCampaignToSingleUser($campaign);

            if(!is_array($resultJson))
                 $result = json_decode($resultJson, JSON_OBJECT_AS_ARRAY);
            else
                $result = $resultJson;


            if (isset($result["failure"]) and $result["failure"] == 0) {

                $this->db->where("id", $campaign['id']);
                $this->db->update("pending_campaigns", array(
                    "logs" => json_encode($result),
                    "failed" => -1
                ));

                $log = "Guest ID: " . $campaign['guest_id'] . " => <b style='color:green'>Pushed</b> => " . (is_array($resultJson)?json_encode($resultJson):$resultJson) . "<br>";

            } else {

                $this->db->where("id", $campaign['id']);
                $this->db->update("pending_campaigns", array(
                    "logs" => json_encode($result),
                    "failed" => 1
                ));

                $log = "Guest ID: " . $campaign['guest_id'] . " => <b style='color:red'>Not Pushed</b> => ".(is_array($resultJson)?json_encode($resultJson):$resultJson)."<br>";

                //delete invalid guest
                if (isset($result["results"]) and isset($result["results"][0]["error"])
                    and $result["results"][0]["error"] == "NotRegistered") {
                    $this->db->where("id", $campaign['guest_id']);
                    $this->db->delete("guest");
                }

            }


            echo $log;
            $logs = $logs . $log;

        }

        echo "Pushed to " . count($pendings) . " guests at " . $currentDate . "<br>";

    }

    private function getFCM($geuest_id)
    {

        $this->db->select("fcm_id,platform");
        $this->db->where("id", $geuest_id);
        $guest = $this->db->get("guest", 1);
        $guest = $guest->result();

        foreach ($guest as $value) {
            return $value;
            break;
        }

        return NULL;
    }

    private function pushSingleCampaignToSingleUser($campaign)
    {

        $this->load->model("notification/notification_model");
        $campData = $this->getDataFromCampaign($campaign['campaign_id']);

        if (!empty($campData)) {

            $fcm = $this->getFCM($campaign['guest_id']);

            if ($fcm != NULL) {

                $params = array(
                    "regIds" => $fcm->fcm_id,
                    "body" => array(
                        "type" => "campaign",
                        "data" => $campData
                    ),
                );


                $result = $this->notification_model->send_notification($fcm->platform, $params);

                if(Json::isJson($result)){
                    $result_array = json_decode($result, JSON_OBJECT_AS_ARRAY);
                }else{
                    $result_array = array(
                        'failure' => 1
                    );
                }

                if (isset($result_array['success']) && $result_array['success'] == 1) {

                    $camps = $this->getCampaigns(array(
                        'campaign_id' => $campaign['campaign_id'],
                        'limit' => 1
                    ));

                    if (isset($camps[Tags::RESULT][0])) {

                        $this->save_nshistoric(array(
                            "id_campaign" => $camps[Tags::RESULT][0]['id'],
                            "module_name" => $camps[Tags::RESULT][0]['module_name'],
                            "module_id" => $camps[Tags::RESULT][0]['module_id'],
                            "user_id" => $camps[Tags::RESULT][0]['user_id'],
                        ), array(
                            array(
                                'guest_id' => $campaign['guest_id']
                            )
                        ));

                    }


                } else if (isset($result_array["results"]) and isset($result_array["results"][0]["error"])
                    and $result_array["results"][0]["error"] == "NotRegistered") {

                    $this->mUserModel->removeInvalidGuest($campaign['guest_id']);

                } else if (isset($result_array["results"]) and isset($result_array["results"][0]["error"])
                    and $result_array["results"][0]["error"] == "InvalidApnsCredential") {

                    $this->mUserModel->removeInvalidGuest($campaign['guest_id']);

                } else if (isset($result_array["results"]) and isset($result_array["results"][0]["error"])
                    and $result_array["results"][0]["error"] == "MissingRegistration") {

                    $this->mUserModel->removeInvalidGuest($campaign['guest_id']);

                } else if (isset($result_array["failure"]) && $result_array["failure"] == 1) {

                    $this->mUserModel->removeInvalidGuest($campaign['guest_id']);

                }else{

                }


                return $result;
            }

        }

        return;
    }


    private function getDataFromCampaign($cid)
    {

        $data = array(
            "title" => "",
            "body" => "",
            "id" => "",
            "image" => "",
            "type" => ""
        );


        $this->db->where("id", $cid);
        $campaign = $this->db->get("campaign", 1);
        $campaign = $campaign->result_array();


        if (count($campaign) == 0)
            return FALSE;


        $module_name = $campaign[0]['module_name'];

        $modules = CampaignManager::load();

        if (isset($modules[$module_name]) && isset($modules[$module_name]['callback_output'])) {
            $data = call_user_func($modules[$module_name]['callback_output'], $campaign[0]);

            if($data==NULL)
                return FALSE;
        }

        $data["cid"] = $cid;

        return $data;
    }


    public function getImage($dir)
    {

        $images = _openDir($dir);
        if (isset($images['200_200']['url'])) {
            return $images['200_200']['url'];
        }

        return "";
    }

    public function getFirstImage($images)
    {

        $images = json_decode($images, JSON_OBJECT_AS_ARRAY);

        if (isset($images[0])) {
            $images = _openDir($images[0]);
            if (isset($images['200_200']['url'])) {
                return $images['200_200']['url'];
            }
        }

        return "";
    }


    public function updateFields()
    {

        $this->renameFields();

        if (!$this->db->field_exists('created_at', 'campaign')) {
            $fields = array(
                'created_at' => array('type' => 'DATETIME', 'after' => 'status', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('campaign', $fields);
        }

        if (!$this->db->field_exists('text', 'campaign')) {
            $fields = array(
                'text' => array('type' => 'TEXT', 'after' => 'name', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('campaign', $fields);
        }

        if (!$this->db->field_exists('image', 'campaign')) {
            $fields = array(
                'image' => array('type' => 'TEXT', 'after' => 'name', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('campaign', $fields);
        }

    }


    public function addAgrrementField()
    {

        if ($this->db->table_exists("bookmarks"))
            if (!$this->db->field_exists('notification_agreement', 'bookmarks')) {
                $fields = array(
                    'notification_agreement' => array('type' => 'INT', 'default' => 0),
                );
                // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
                $this->dbforge->add_column('bookmarks', $fields);
            }

    }

    public function addNotificationField()
    {

        if ($this->db->table_exists("nsh_notifications"))
            if (!$this->db->field_exists('campaign_id', 'nsh_notifications')) {
                $fields = array(
                    'campaign_id' => array('type' => 'INT', 'default' => 0),
                );
                // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
                $this->dbforge->add_column('nsh_notifications', $fields);
            }

    }


    public function renameFields()
    {

        if (!$this->db->field_exists('module_name', 'campaign')) {
            $fields = array(
                'module_name' => array('type' => 'VARCHAR(60)', 'after' => 'type', 'default' => ""),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('campaign', $fields);

            //move data
            $c = $this->db->get('campaign');
            $c = $c->result();

            foreach ($c as $value) {

                $this->db->where('id', $value->id);
                $this->db->update('campaign', array(
                    'module_name' => $value->type
                ));
            }

            $this->dbforge->drop_column('campaign', 'type');
        }


        if (!$this->db->field_exists('module_id', 'campaign')) {
            $fields = array(
                'module_id' => array('type' => 'INT', 'after' => 'module_name', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('campaign', $fields);

            //move data
            $c = $this->db->get('campaign');
            $c = $c->result();

            foreach ($c as $value) {

                $this->db->where('id', $value->id);
                $this->db->update('campaign', array(
                    'module_id' => $value->int_id
                ));
            }

            $this->dbforge->drop_column('campaign', 'int_id');
        }

    }


    public function create_tracker_table()
    {

        if ($this->db->table_exists('visit')) {
            $this->dbforge->drop_table('visit');
        }

        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'module' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'module_id' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'action' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'guest_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        );

        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ns_tracker', TRUE, $attributes);

    }

    private function addTrack($params)
    {

        $data = array(
            'guest_id' => $params['guest_id'],
            'user_id' => $params['user_id'],
            'module' => $params['module'],
            'module_id' => $params['module_id'],
            'action' => $params['action'],
        );

        $this->db->where($data);
        $count = $this->db->count_all_results('ns_tracker');

        if ($count == 0) {
            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['updated_at'] = date("Y-m-d H:i:s", time());
            $this->db->insert('ns_tracker', $data);
            return TRUE;
        }

        return FALSE;
    }
}