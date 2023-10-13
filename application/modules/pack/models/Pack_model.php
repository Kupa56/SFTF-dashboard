<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Pack_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
    }

    public function getDurations()
    {
        return array(
            'month' => 1,
            'year' => 12,
        );
    }


    public function delete($pack_id)
    {


        $this->db->where("pack_id",$pack_id);
        $count = $this->db->count_all_results("user_subscribe_setting");



        if ($count == 0) {

            $this->db->where("id", $pack_id);
            $this->db->delete("packmanager");

            $this->db->where("pack_id", $pack_id);
            $this->db->update("user_subscribe_setting", array(
                "pack_id" => 0,
            ));

            return array(Tags::SUCCESS => 1);

        } else {

            return array(Tags::SUCCESS => 0, Tags::ERRORS => array("err" => Translate::sprint("You can't delete this pack, because it linked with many accounts")));

        }


    }


    public function edit($params = array())
    {

        extract($params);

        $data = array();
        $errors = array();


        if (isset($id) && $id > 0) {
            $pack = $this->getPack(intval($id));
            if ($pack == NULL) {
                $errors[] = Translate::sprint("The ID is missing");
            }
        } else {
            $errors[] = Translate::sprint("The ID is missing");
        }


        if (isset($name) && $name != "")
            $data['name'] = $name;
        else
            $errors[] = Translate::sprint("The name field is empty!");

        if (isset($group_access) && $group_access > 0)
            $data['grp_access_id'] = intval($group_access);

        if (isset($description) && $description != "")
            $data['description'] = $description;


        //verify user settings
        if (isset($user_subscribe) and !empty($user_subscribe)) {

            $user_subscribe_verified = array();

            $user_subscribe_fields = UserSettingSubscribe::load();
            foreach ($user_subscribe_fields as $field) {
                if ($field['_display'] == 1 && isset($user_subscribe[$field['field_name']])) {

                    if ($field['field_type'] == UserSettingSubscribeTypes::BOOLEAN
                        or $field['field_type'] == UserSettingSubscribeTypes::INT) {
                        $user_subscribe_verified[$field['field_name']] = intval($user_subscribe[$field['field_name']]);
                    } else if ($field['field_type'] == UserSettingSubscribeTypes::DOUBLE) {
                        $user_subscribe_verified[$field['field_name']] = doubleval($user_subscribe[$field['field_name']]);
                    } else if ($field['field_type'] == UserSettingSubscribeTypes::TEXT or $field['field_type'] == UserSettingSubscribeTypes::TEXT) {
                        $user_subscribe_verified[$field['field_name']] = Text::input($user_subscribe[$field['field_name']]);
                    }

                }
            }

            foreach ($user_subscribe_verified as $key => $uf) {
                $data[$key] = $uf;
            }

        }


        $data['duration'] = 30;

        if (isset($free) and $free == 0) {
            if (isset($price) && is_numeric($price))
                $data['price'] = doubleval($price);
            else
                $errors[] = Translate::sprint("The price field is empty!");
        } else {
            $data['price'] = 0;
        }

        if (isset($price_yearly) && is_numeric($price_yearly) and $price_yearly > 0) {
            $data['price_yearly'] = doubleval($price_yearly);
        } else {
            if (isset($data['price']))
                $data['price_yearly'] = $py = $data['price'] * 12;
        }

        if (isset($order) && is_numeric($order))
            $data['_order'] = intval($order);
        else {
            $data['_order'] = $this->getLastOder();
        }

        if (isset($trial_period) && $trial_period > 0)
            $data['trial_period'] = intval($trial_period);
        else
            $data['trial_period'] = 0;


        if (isset($recommended) && is_numeric($recommended))
            $data['recommended'] = intval($recommended);
        else {
            $data['recommended'] = 0;
        }

        if (isset($display) && is_numeric($display))
            $data['display'] = intval($display);
        else {
            $data['display'] = 0;
        }

        if (isset($data['price']) == 0) {
            $data['trial_period'] = 0;
        }

        if (empty($errors)) {

            $this->db->where("id", $id);
            $this->db->update('packmanager', $data);

            return array(Tags::SUCCESS => 1);
        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }

    public function add($params = array())
    {

        extract($params);

        $data = array();
        $errors = array();


        if (isset($name) && $name != "")
            $data['name'] = $name;
        else
            $errors[] = Translate::sprint("The name field is empty!");


        if (isset($description) && $description != "")
            $data['description'] = $description;


        if (isset($group_access) && $group_access > 0)
            $data['grp_access_id'] = intval($group_access);

        //verify user settings
        if (isset($user_subscribe) and !empty($user_subscribe)) {

            $user_subscribe_verified = array();

            $user_subscribe_fields = UserSettingSubscribe::load();
            foreach ($user_subscribe_fields as $field) {
                if ($field['_display'] == 1 && isset($user_subscribe[$field['field_name']])) {

                    if ($field['field_type'] == UserSettingSubscribeTypes::BOOLEAN
                        or $field['field_type'] == UserSettingSubscribeTypes::INT) {
                        $user_subscribe_verified[$field['field_name']] = intval($user_subscribe[$field['field_name']]);
                    } else if ($field['field_type'] == UserSettingSubscribeTypes::DOUBLE) {
                        $user_subscribe_verified[$field['field_name']] = doubleval($user_subscribe[$field['field_name']]);
                    } else if ($field['field_type'] == UserSettingSubscribeTypes::TEXT or $field['field_type'] == UserSettingSubscribeTypes::TEXT) {
                        $user_subscribe_verified[$field['field_name']] = Text::input($user_subscribe[$field['field_name']]);
                    }

                }
            }

            foreach ($user_subscribe_verified as $key => $uf) {
                $data[$key] = $uf;
            }

        }


        $data['duration'] = 30;

        if (isset($free) and $free == 0) {
            if (isset($price) && is_numeric($price))
                $data['price'] = doubleval($price);
            else
                $errors[] = Translate::sprint("The price field is empty!");
        } else {
            $data['price'] = 0;
        }

        if (isset($price_yearly) && is_numeric($price_yearly) and $price_yearly > 0) {

            /*$py = $data['price']*12;
            if(isset($data['price']) and $py>=$price_yearly)
                $data['price_yearly'] = doubleval($price_yearly);
            else{
                $errors[] = Translate::sprint("The price yearly is not valid!");

            }*/
            $data['price_yearly'] = doubleval($price_yearly);
        } else {
            $data['price_yearly'] = 0;
        }

        if (isset($order) && is_numeric($order))
            $data['_order'] = intval($order);
        else {
            $data['_order'] = $this->getLastOder();
        }


        if (isset($trial_period) && $trial_period > 0)
            $data['trial_period'] = intval($trial_period);
        else
            $data['trial_period'] = 0;


        if (isset($recommended) && is_numeric($recommended))
            $data['recommended'] = intval($recommended);
        else {
            $data['recommended'] = 0;
        }

        if (isset($display) && is_numeric($display))
            $data['display'] = intval($display);
        else {
            $data['display'] = 0;
        }

        if (isset($data['price']) == 0) {
            $data['trial_period'] = 0;
        }

        if (empty($errors)) {
            $data['created_at'] = date("Y-m-d H:i:s",time());
            $data['updated_at'] = date("Y-m-d H:i:s",time());
            $this->db->insert('packmanager', $data);
            return array(Tags::SUCCESS => 1);
        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }

    public function getLastOder()
    {

        $this->db->select("_order");
        $this->db->order_by("_order", "DESC");
        $p = $this->db->get("packmanager", 1);
        $p = $p->result();

        if (count($p) > 0) {
            return $p[0]->_order + 1;
        }

        return 1;
    }

    public function getPack($pack_id)
    {

        if ($pack_id > 0) {
            $this->db->where("id", $pack_id);
            $pack = $this->db->get("packmanager", 1);
            $pack = $pack->result();

            if (count($pack) > 0) {
                return $pack[0];
            }
        }

        return NULL;
    }


    public function getAccountPack()
    {

        $pack_id = intval($this->mUserBrowser->getData('pack_id'));
        return $this->getPack($pack_id);
    }

    public function getPacks()
    {

        $this->db->order_by("_order", "asc");
        $pack = $this->db->get("packmanager");
        $packs = $pack->result();

        return $packs;
    }


    public function getList($params = array())
    {

        extract($params);

        if (!isset($page)) {
            $page = 1;
        }

        if (!isset($limit)) {
            $limit = NO_OF_ITEMS_PER_PAGE;
        }


        $count = $this->db->count_all_results("packmanager");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        $this->db->order_by("_order", "ASC");
        $this->db->from("packmanager");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());
        $packs = $this->db->get();
        $packs = $packs->result();


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $packs);

    }

    public function confirmPayment($args = array())
    {

        $this->db->where('id', intval($args['invoiceId']));
        $this->db->where('module', "pack");
        $this->db->where('status', 0);
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();


        if (count($invoice) > 0) {

            $items = json_decode($invoice[0]->items, JSON_OBJECT_AS_ARRAY);
            $user_id = $invoice[0]->user_id;


            foreach ($items as $value) {

                $item_id = $value['item_id'];

                $this->updatePackAccount($item_id, $user_id, TRUE, $value['months']);

                $pack = $this->getPack($item_id);
                $this->load->model("user/user_model");
                $user = $this->user_model->getUserData($user_id);


                if ($pack != NULL && $user != NULL) {
                    $this->sendPackConfirmation($user['id_user'], $pack->id);
                }


                return TRUE;

            }

        }


        return FALSE;
    }


    public function haveInvoiceToPay()
    {

        $user_id = intval($this->mUserBrowser->getData("id_user"));

        $this->db->where("user_id", $user_id);
        $this->db->where("status", 0);
        $c = $this->db->count_all_results("invoice");

        if ($c > 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function createInvoice($pack, $user_id, $qty = 1, $module = "Pack")
    {

        if ($pack == NULL)
            return 0;

        if ($qty == 1) {
            $amount = $pack->price;
        } else {
            $amount = $pack->price_yearly;
        }


        $items = array(
            array(
                'item_id' => $pack->id,
                'item_name' => $module . " - " . $pack->name,
                'price' => $amount,
                'price_per_unit' => $pack->price,
                'unit' => 'month',
                'qty' => 1,
                'months' => $qty
            )
        );

        $items = json_encode($items, JSON_FORCE_OBJECT);

        $this->db->where('user_id', $user_id);
        $no = $this->db->count_all_results('invoice');
        $no++;


        $data = array(
            "method" => "",
            "amount" => $amount,
            "no" => $no,
            "module" => "pack",
            "tax_id" => 0,
            "items" => $items,
            "currency" => PAYMENT_CURRENCY,
            "status" => 0,
            "user_id" => $user_id,
            "transaction_id" => "",
            "updated_at" => date("Y-m-d H:i:s", time()),
            "created_at" => date("Y-m-d H:i:s", time())
        );


        $this->db->where("user_id", $user_id);
        $this->db->where("status", 0);
        $this->db->where("module", "pack");
        $this->db->delete("invoice");

        $this->db->insert('invoice', $data);

        return $this->db->insert_id();
    }


    public function trial_period_used($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('trial_period_used', 1);
        $count = $this->db->count_all_results('user_subscribe_setting');

        if ($count == 1)
            return TRUE;

        return FALSE;
    }


    public function updateSubscription($trial_days = 0)
    {

        $user_id = SessionManager::getData("id_user");
        $invoice = $this->mPaymentModel->getInvoice_by_user_id($user_id, 0);

        if ($invoice != NULL) {
            $items = $invoice->items;
            $items = json_decode($items, JSON_OBJECT_AS_ARRAY);
        }

        if (isset($items[0])) {

            $item_id = intval($items[0]['item_id']);
            $months = intval($items[0]['months']);

            $this->mPack->updatePackAccount($item_id, $user_id, $months);

            if ($trial_days > 0) {

                $this->db->where('trial_period_used', 0);
                $this->db->where('user_id', $user_id);
                $this->db->update('user_subscribe_setting', array(
                    'trial_period_date' => date('Y-m-d H:i:s', strtotime('+' . $trial_days . ' day')),
                    'trial_period_used' => 1
                ));
            }

            $this->mUserBrowser->refreshData($user_id);
        }
    }

    public function updatePackAccount($pack_id, $user_id, $enable = FALSE, $qty = 1)
    {

        $user_fields = UserSettingSubscribe::load();

        $this->db->where("id", $pack_id);
        $pack_data = $this->db->get("packmanager", 1);
        $pack_data = $pack_data->result();

        if (count($pack_data) > 0) {

            $pack_data = $pack_data[0];

            $current_date = MyDateUtils::convert(date("Y-m-d H:i:s", time()), TIME_ZONE, "UTC", "Y-m-d H:i:s");

            $duration_by_month = $pack_data->duration / 30;

            //add a trial period for subscription
            if ($pack_data->price == 0 && $pack_data->trial_period > 0 && !$this->trial_period_used($user_id)) {
                $will_expired_at = date('Y-m-d H:i:s', strtotime(' +' . $pack_data->trial_period . '  day'));
            } else {

                if ($duration_by_month >= 1) {
                    $will_expired_at = date('Y-m-d H:i:s', strtotime($current_date . ' +' . $duration_by_month * $qty . ' month'));
                } else {
                    $will_expired_at = date('Y-m-d H:i:s', strtotime($current_date . ' +' . ($pack_data->duration * $qty) . ' day'));
                }

            }


            $package = array(
                "will_expired" => $will_expired_at,
                "last_updated" => $current_date,
                "pack_id" => $pack_data->id,
                "status" => 0,
                "reminded" => 0,
                "trial_period_used" => 1,
            );


            foreach ($user_fields as $field) {
                if ($field['_display'] == 1)
                    $package[$field['field_name']] = $pack_data->{$field['field_name']};
            }


            if (!$this->isExpired($will_expired_at))
                $package['status'] = 1;


            if ($enable == FALSE) {

                $pkg = array();

                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $pkg[$field['field_name']] = 0;
                }

                $json = json_encode($pkg);

                $package['user_settings_package'] = $json;
                $package['status'] = 0;

                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $package[$field['field_name']] = 0;
                }

            } else {

                $pkg = array();

                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $pkg[$field['field_name']] = $package[$field['field_name']];
                }

                $json = json_encode($pkg);

                $package['user_settings_package'] = $json;
                $package['status'] = 1;

            }

            $this->db->where("user_id", $user_id);
            $count = $this->db->count_all_results('user_subscribe_setting');

            if ($count == 1) {
                $this->db->where("user_id", $user_id);
                $this->db->update('user_subscribe_setting', $package);
            } else {
                $package['user_id'] = $user_id;
                $this->db->insert('user_subscribe_setting', $package);
            }

            if ($enable == TRUE && $pack_data->grp_access_id > 0) {
                //set default business owner access

                $grp = $this->mGroupAccessModel->getGroupAccess($pack_data->grp_access_id );

                $user = array(
                    "typeAuth" => $grp['name'],
                    "grp_access_id" => $grp['id']
                );

                $user['typeAuth'] = $grp['name'];
                $user['grp_access_id'] = $pack_data->grp_access_id;

                $this->db->where("id_user", $user_id);
                $this->db->update('user', $user);

            } else if ($enable == FALSE) {

                $this->db->where("id_user", $user_id);
                $this->db->update('user', array(
                    'grp_access_id' => DEFAULT_USER_GRPAC
                ));

            }


            return TRUE;
        }

        return FALSE;
    }

    public function enableTrialPeriod($pack_id, $user_id, $enable = FALSE)
    {

        $user_fields = UserSettingSubscribe::load();

        $this->db->where("id", $pack_id);
        $pack_data = $this->db->get("packmanager", 1);
        $pack_data = $pack_data->result();

        if (count($pack_data) > 0) {

            $pack_data = $pack_data[0];

            $current_date = MyDateUtils::convert(date("Y-m-d H:i:s", time()), TIME_ZONE, "UTC", "Y-m-d H:i:s");

            //add a trial period for subscription
            if ($pack_data->trial_period > 0 && !$this->trial_period_used($user_id)) {
                $will_expired_at = date('Y-m-d H:i:s', strtotime(' +' . $pack_data->trial_period . '  day'));
            } else {
                return;
            }


            $package = array(
                "will_expired" => $will_expired_at,
                "last_updated" => $current_date,
                "pack_id" => $pack_data->id,
                "status" => 0,
                "reminded" => 0,
                "trial_period_used" => 1,
            );


            foreach ($user_fields as $field) {
                if ($field['_display'] == 1)
                    $package[$field['field_name']] = $pack_data->{$field['field_name']};
            }


            if (!$this->isExpired($will_expired_at))
                $package['status'] = 1;


            if ($enable == FALSE) {

                $pkg = array();


                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $pkg[$field['field_name']] = 0;
                }

                $json = json_encode($pkg);


                $package['user_settings_package'] = $json;
                $package['status'] = 0;

                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $package[$field['field_name']] = 0;
                }

            } else {

                $pkg = array();

                foreach ($user_fields as $field) {
                    if ($field['_display'] == 1)
                        $pkg[$field['field_name']] = $package[$field['field_name']];
                }

                $json = json_encode($pkg);


                $package['user_settings_package'] = $json;
                $package['status'] = 1;

            }


            $this->db->where("user_id", $user_id);
            $count = $this->db->count_all_results('user_subscribe_setting');

            if ($count == 1) {
                $this->db->where("user_id", $user_id);
                $this->db->update('user_subscribe_setting', $package);
            } else {
                $package['user_id'] = $user_id;
                $this->db->insert('user_subscribe_setting', $package);
            }

            //TODO check why the group access is not stored on database
            if ($pack_data->grp_access_id > 0) {
                //set default business owner access
                $default_grp_id = DEFAULT_USER_GRPAC;
                $grp = $this->mGroupAccessModel->getGroupAccess($default_grp_id);

                $user = array(
                    "typeAuth" => $grp['name'],
                    "grp_access_id" => DEFAULT_USER_GRPAC
                );


                $grp = $this->mGroupAccessModel->getGroupAccess($pack_data->grp_access_id);
                $user['typeAuth'] = $grp['name'];
                $user['grp_access_id'] = $pack_data->grp_access_id;

                $this->db->where("id_user", $user_id);
                $this->db->update('user', $user);
            }


            return TRUE;
        }

        return FALSE;
    }


    public function isRenewal()
    {

        $user_id = intval($this->mUserBrowser->getData("id_user"));
        $this->db->where("user_id", $user_id);
        $this->db->where("status", 0);

        $c = $this->db->count_all_results("user_subscribe_setting");

        if ($c > 0)
            return TRUE;

        return FALSE;
    }

    public function initSubscription()
    {

        $this->db->update('user_subscribe_setting', array(
            'pack_id' => 0
        ));

    }

    public function canUpgrade()
    {

        $pack_id = intval($this->mUserBrowser->getData("pack_id"));

        $pack = $this->getPack($pack_id);

        if ($pack == NULL)
            return FALSE;

        $this->db->where("price >", $pack->price);
        $c = $this->db->count_all_results("packmanager");

        if ($c > 0)
            return TRUE;

        return FALSE;
    }


    public function refreshPackage($user_id = 0)
    {

        // update package monthly ...

        $user_fields = UserSettingSubscribe::load();

        $cDate = date("Y-m-d H:i:s", time());
        $cDate = MyDateUtils::convert($cDate, TIME_ZONE, "UTC");

        $currentDate = date("Y-m", time());
        $this->db->select("last_updated,package,user_id");
        $this->db->where("last_updated <", $currentDate . '-01');
        $this->db->where("will_expired >", $cDate);
        $this->db->where("status", 1);

        if ($user_id > 0)
            $this->db->where("user_id", $user_id);

        $settings = $this->db->get("user_subscribe_setting");
        $settings = $settings->result();
        foreach ($settings as $setting) {

            $pack = $setting->package;
            $pack = json_decode($pack, JSON_OBJECT_AS_ARRAY);

            foreach ($user_fields as $field) {
                if ($field['_display'] == 1)
                    $pkg[$field['field_name']] = $pack[$field['field_name']];
            }

            $this->db->where("user_id", $setting->user_id);

            $pack['last_updated'] = MyDateUtils::convert(date("Y-m-01", time()), TIME_ZONE, "UTC");

            $this->db->update("user_subscribe_setting", $pkg);

        }


        // update duration and status of package

        $d = date("Y-m-d H:i:s", time());
        $currentDate = MyDateUtils::convert($d, TIME_ZONE, "UTC");

        $this->db->select("last_updated,package,user_id,pack_id");
        $this->db->where("will_expired <", $currentDate);
        $this->db->where("status", 1);

        if ($user_id > 0)
            $this->db->where("user_id", $user_id);


        $user = $this->db->get("user_subscribe_setting", 50);
        $settings = $user->result();


        foreach ($settings as $setting) {
            if ($setting->pack_id > 0) {
                $this->updatePackAccount(
                    $setting->pack_id,
                    $setting->user_id,
                    FALSE
                );
            }

            $pkg = json_decode($setting->package, JSON_OBJECT_AS_ARRAY);

            foreach ($user_fields as $field) {
                if ($field['_display'] == 1)
                    $pkg[$field['field_name']] = 0;
            }


            $pkg['status'] = 0;

            $this->db->where("user_id", $setting->user_id);

            if ($setting->pack_id > 0)
                $this->db->where("pack_id", $setting->pack_id);

            $this->db->update('user_subscribe_setting', $pkg);


        }


    }


    public function havePicked()
    {

        $selected_pack = intval(RequestInput::get("selected_pack"));
        if ($selected_pack > 0) {
            $this->session->set_userdata(array(
                "pack_id" => $selected_pack
            ));
            return TRUE;
        }


        return FALSE;
    }


    public function isExist($id)
    {
        $this->db->wgere("id", intval($id));
        return $this->db->count_all_results("pack");
    }

    public function print_duration($duration)
    {

        if ($duration == 30) {
            echo "<strong>1 " . Translate::sprint("Month") . "</strong>";
        } else if ($duration >= 30) {

            $duration0 = $duration / 30;
            $duration0 = number_format($duration0, 2, '.', '');

            if ($duration0 < 12) {

                if (!fmod($duration0, 1))
                    echo "<strong>" . intval($duration0) . " " . Translate::sprint("Months") . "</strong>";
                else
                    echo "<strong>" . intval($duration) . " " . Translate::sprint("Days") . "</strong>";

            } else
                echo "<strong>" . intval($duration0) . " " . Translate::sprint("Years") . "</strong>";


        } else {
            echo "<strong>" . $duration . " " . Translate::sprint("Days") . "</strong>";
        }
    }

    public function isExpired($dateExpired)
    {

        $current_date = MyDateUtils::convert(date("Y-m-d H:i:s", time()), TIME_ZONE, "UTC", "Y-m-d H:i:s");

        $diff_millseconds_exp = strtotime($dateExpired) - strtotime($current_date);

        if ($diff_millseconds_exp < 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function createRenewInvoice()
    {

        //  $id = $this->createInvoice($pack[0], $user_id, 1, Translate::sprint("Renew Pack"));
    }

    public function getLastInvoice($user_id = 0)
    {

        if ($user_id == 0)
            $user_id = intval($this->mUserBrowser->getData("id_user"));

        $this->db->where("user_id", $user_id);
        $this->db->order_by('id', 'DESC');
        $invoices = $this->db->get('invoice', 1);
        $invoices = $invoices->result();

        if (count($invoices) > 0)
            return $invoices[0];

        return NULL;

    }


    public function getInvoice($invoice_id = 0, $user_id = 0)
    {

        $this->db->where("id", $invoice_id);
        $this->db->where("user_id", $user_id);
        $this->db->order_by('id', 'DESC');
        $invoices = $this->db->get('invoice', 1);
        $invoices = $invoices->result();

        if (count($invoices) > 0)
            return $invoices[0];

        return NULL;

    }

    public function cancelSubscriptionIfNeeded()
    {


    }

    public function checkRenewalNeeded()
    {

        $this->load->model('user/user_browser', 'mUserBrowser');
        if ($this->mUserBrowser->isLogged()) {

            $user_id = intval($this->mUserBrowser->getData("id_user"));
            $this->db->where("user_id", $user_id);
            $c = $this->db->count_all_results("user_subscribe_setting");

            if ($c == 1) {

                $expired_date = $this->mUserBrowser->getData('will_expired');
                $days = MyDateUtils::getDays($expired_date);

                if ($days < 7) {

                    $this->db->where("user_id", $user_id);
                    //$this->db->where("status", 0);
                    $setting = $this->db->get("user_subscribe_setting", 1);
                    $setting = $setting->result();
                    $setting = $setting[0];

                    $pack = $this->getPack($setting->pack_id);

                    if ($pack != NULL) {

                        if (!$this->haveInvoiceToPay()) {
                            return $pack;
                        }

                    } else {

                        redirect('pack/pickpack?sm');

                    }

                }

            }
        }

        return FALSE;
    }

    public function hadInvoices()
    {
        $this->load->model('user/user_browser', 'mUserBrowser');

        if ($this->mUserBrowser->isLogged()) {
            $user_id = intval($this->mUserBrowser->getData('id_user'));

            $this->db->where("status", 0);
            $this->db->where("module", "pack");
            $this->db->where("user_id", $user_id);
            $c = $this->db->count_all_results("invoice");

            if ($c > 0)
                return TRUE;

        }

        return FALSE;
    }

    public function checkUpgradeNeededToManager()
    {

        /*$this->load->model('user/user_browser','mUserBrowser');
        if($this->mUserBrowser->isLogged()){

            $type = $this->mUserBrowser->getData('typeAuth');

            $uri = $this->uri->segment(1);
            if($type=="customer" && $uri=="dashboard" && $uri!="pack" && $uri!="payment" && $uri!="user_subscribe_setting" ){
                //check if have an invoice
                if(!$this->haveInvoiceToPay()){
                    redirect("pack/upgradeAccount");
                }

            }
        }*/

    }

    public function havePickedPack()
    {

        if ($this->mUserBrowser->isLogged()) {
            $pack_id = intval($this->mUserBrowser->getData("pack_id"));
            if ($pack_id > 0) {
                return TRUE;
            }
        }

        return FALSE;
    }


    public function cancelSubscription($user_id, $pack_id)
    {

        //update user subscription and remove actions
        $this->updatePackAccount($pack_id, $user_id, FALSE, 0);

        //call action
        ActionsManager::add_action("user","userShutDown",$user_id);

    }

    public function checkUserPackAndRemind($limit = 10)
    {

        $current_date = MyDateUtils::convert(date("Y-m-d H:i:s", time()), TIME_ZONE, "UTC", "Y-m-d H:i:s");

        $this->db->select('pack_id,user_id,auto_renew');
        $this->db->where('will_expired <=', $current_date);
        $this->db->where('status', 1);
        $users = $this->db->get('user_subscribe_setting', $limit);
        $users = $users->result();

        //change status
        foreach ($users as $value) {
            //create pack if needed
            $pack = $this->getPack($value->pack_id);
            if ($pack != NULL && $pack->price > 0) {
                $this->createInvoice($pack, $value->user_id, 1);
                $this->cancelSubscription($value->user_id, $pack->id);
            }else if ($pack != NULL && $pack->price == 0) {
                $this->cancelSubscription($value->user_id, $pack->id);
            }
        }

        //send a reminder
        $current_date = MyDateUtils::convert(date("Y-m-d H:i:s", time()), TIME_ZONE, "UTC", "Y-m-d H:i:s");

        $added_six_days = strtotime($current_date . ' +6 day');
        $utc_date = date('Y-m-d H:i:s', $added_six_days);

        //$current_date+6 days
        $this->db->select('pack_id,user_id,auto_renew');
        $this->db->where('reminded', 0);
        $this->db->where('will_expired <', $utc_date);
        $users = $this->db->get('user_subscribe_setting', $limit);
        $users = $users->result();


        foreach ($users as $value) {

            //check balance & auto renew
            if ($value->auto_renew == 1
                && $this->mWalletModel->autoRenew($value->user_id)) {
                echo "Auto renew (userID: " . $value->user_id . ") => DONE<br>";
                continue;
            } else if ($this->sendRemind($value->user_id, $value->pack_id)) {

                $this->db->where('user_id', $value->user_id);
                $this->db->update('user_subscribe_setting', array(
                    'reminded' => 1
                ));
                //echo "User ID:".$value->user_id." was reminded<br>";
            }

        }


    }

    public function sendRemind($user_id = 0, $pack_id = 0)
    {

        if ($user_id == 0 && $pack_id == 0)
            return;


        $pack = $this->getPack($pack_id);
        $user = $this->mUserModel->getUserData($user_id);

        if ($user != NULL && $pack != NULL) {

            $appLogo = _openDir(APP_LOGO);
            $imageUrl = "";
            if (!empty($appLogo)) {
                $imageUrl = $appLogo['200_200']['url'];
            }

            if (file_exists(FCPATH . '/application/modules/pack/views/mailing/' . DEFAULT_LANG . '_pack_remind.php')) {
                $html = $this->load->view('pack/mailing/' . DEFAULT_LANG . '_pack_remind', NULL, TRUE);
            } else {
                $html = $this->load->view('pack/mailing/en_pack_remind', NULL, TRUE);
            }

            //send mail verification
            $messageText = Text::textParserHTML(array(
                "name" => $user['name'],
                "url" => admin_url("store/create"),
                "imageUrl" => $imageUrl,
                "email" => DEFAULT_EMAIL,
                "appName" => strtolower(APP_NAME),
                "date_expired" => $user['will_expired'] . ' UTC',
                "renewUrl" => admin_url('pack/renew')
            ), $html);


            $mail = new DTMailer();
            $mail->setRecipient($user['email']);
            $mail->setFrom(DEFAULT_EMAIL);
            $mail->setFrom_name(APP_NAME);
            $mail->setMessage($messageText);
            $mail->setReplay_to(DEFAULT_EMAIL);
            $mail->setReplay_to_name(APP_NAME);
            $mail->setType("html");
            $mail->setSubject(Translate::sprintf('Your pack %s will expired soon', array($pack->name)));
            $mail->send();

            return TRUE;
        }

        return FALSE;
    }


    public function sendPackConfirmation($user_id, $pack_id)
    {

        if ($user_id == 0 && $pack_id == 0)
            return;


        $pack = $this->getPack($pack_id);
        $user = $this->mUserModel->getUserData($user_id);
        $invoice = $this->getLastInvoice($user_id);


        if ($user != NULL && $pack != NULL && $invoice != NULL) {

            $invoice_items = json_decode($invoice->items, JSON_OBJECT_AS_ARRAY);
            $invoice_items = $invoice_items[0];

            $appLogo = _openDir(APP_LOGO);
            $imageUrl = "";
            if (!empty($appLogo)) {
                $imageUrl = $appLogo['200_200']['url'];
            }

            $path = FCPATH . '/application/modules/pack/views/mailing/pack_confirmation.php';
            if (file_exists($path)) {
                $html = $this->load->view('pack/mailing/pack_confirmation', NULL, TRUE);
            } else {
                $html = $this->load->view('pack/mailing/pack_confirmation', NULL, TRUE);
            }

            //send mail verification
            $messageText = Text::textParserHTML(array(
                "name" => $user['name'],
                "url" => admin_url("store/create"),
                "imageUrl" => $imageUrl,
                "email" => DEFAULT_EMAIL,
                "appName" => strtolower(APP_NAME),
                "packName" => $pack->name,
                "packDuration" => $invoice_items['qty'],
                "packExpired" => $user['will_expired'] . ' UTC',
                "packPrice" => Currency::parseCurrencyFormat($invoice->amount, PAYMENT_CURRENCY),
                "billUrl" => admin_url('payment/printBill?id=' . $invoice->id),
                "paymentMethod" => $invoice->method,
            ), $html);


            $mail = new DTMailer();
            $mail->setRecipient($user['email']);
            $mail->setFrom(DEFAULT_EMAIL);
            $mail->setFrom_name(APP_NAME);
            $mail->setMessage($messageText);
            $mail->setReplay_to(DEFAULT_EMAIL);
            $mail->setReplay_to_name(APP_NAME);
            $mail->setType("html");
            $mail->setSubject(Translate::sprint(APP_NAME . " - Your pack \"$pack->name\" is activated"));
            if($mail->send()){
                return FALSE;
            }


        }

        return TRUE;
    }


    public function verify_pid()
    {

        $pid = ConfigManager::getValue("DF_SUBSCRIPTION_PACK_PID");

        if ($pid == "") {
            return array(Tags::SUCCESS => 0);
        }

        //execute api
        $api_endpoint = "https://apiv2.webox360.win/api/api3/pchecker";
        $post_data = array(
            "pid" => $pid,
            "item" => "1.0,df-subscription-pack",
            "reqfile" => 1,
        );

        $response = MyCurl::run($api_endpoint, $post_data);
        $response = json_decode($response, JSON_OBJECT_AS_ARRAY);


        $response[] = $pid;

        if (!isset($response[Tags::SUCCESS]))
            return array(Tags::SUCCESS => 0);

        if (isset($response[Tags::SUCCESS]) && $response[Tags::SUCCESS] == 0)
            return $response;


        $sql = base64_decode($response['datasql']);
        $sql_list = array();

        if (preg_match("#;#", $sql)) {
            $sql_list = explode(";", $sql);
        } else
            $sql_list[] = $sql;

        foreach ($sql_list as $query) {
            if (trim($query) != "")
                $this->db->query($query);
        }


        return array(Tags::SUCCESS => 1);
    }


    public function updateTableFields()
    {

        /*
         *  SETTING FIELD UPDATE
         */

        /////////// USER SUBSCRIBE UPDATE FIELDS ///////////

        if (!$this->db->field_exists('pack_id', 'user_subscribe_setting')) {
            $fields = array(
                'pack_id' => array('type' => 'INT', 'after' => 'user_id', 'default' => NULL),);
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

        if (!$this->db->field_exists('status', 'user_subscribe_setting')) {
            $fields = array(
                'status' => array('type' => 'INT', 'after' => 'pack_id', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

        if (!$this->db->field_exists('will_expired', 'user_subscribe_setting')) {
            $fields = array(
                'will_expired' => array('type' => 'DATETIME', 'after' => 'status', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

        if (!$this->db->field_exists('reminded', 'user_subscribe_setting')) {
            $fields = array(
                'reminded' => array('type' => 'INT', 'after' => 'will_expired', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

        /////// END USER SUBSCRIBE UPDATE FIELDS ///////

        if (!$this->db->field_exists('description', 'packmanager')) {
            $fields = array(
                'description' => array('type' => 'VARCHAR(300)', 'after' => 'display', 'default' => NULL),);
            $this->dbforge->add_column('packmanager', $fields);
        }


        if (!$this->db->field_exists('grp_access_id', 'packmanager')) {
            $fields = array(
                'grp_access_id' => array('type' => 'INT', 'after' => 'description', 'default' => NULL),);
            $this->dbforge->add_column('packmanager', $fields);
        }


        $user_subscribe_fields = UserSettingSubscribe::load();

        foreach ($user_subscribe_fields as $field) {

            if ($field['_display'] == 1) {
                if (!$this->db->field_exists($field['field_name'], 'packmanager')) {
                    $fields = array($field['field_name'] => array('type' => $field['field_type'], 'after' => 'display', 'default' => NULL),);
                    $this->dbforge->add_column('packmanager', $fields);
                }
            }
        }


        if (!$this->db->field_exists('trial_period_date', 'user_subscribe_setting')) {
            $fields = array(
                'trial_period_date' => array('type' => 'DATETIME', 'after' => 'pack_id', 'default' => NULL),);
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }


        if (!$this->db->field_exists('trial_period_used', 'user_subscribe_setting')) {
            $fields = array(
                'trial_period_used' => array('type' => 'INT', 'after' => 'trial_period_date', 'default' => 0),);
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

    }


    public function createTables()
    {

        $sql = '
                CREATE TABLE IF NOT EXISTS `packmanager` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) DEFAULT NULL,
                  `recommended` int(11) DEFAULT NULL,
                  `duration` int(11) DEFAULT NULL,
                  `price` double DEFAULT NULL,
                  `price_yearly` double DEFAULT NULL,
                  `display` int(11) NOT NULL DEFAULT \'1\',
                  `nbr_products_monthly` int(11) DEFAULT NULL,
                  `nbr_events_monthly` int(11) DEFAULT NULL,
                  `nbr_campaigns_monthly` int(11) DEFAULT NULL,
                  `push_campaign_auto` tinyint(1) DEFAULT NULL,
                  `nbr_stores` int(11) DEFAULT NULL,
                  `description` varchar(300) DEFAULT NULL,
                  `grp_access_id` int(11) DEFAULT NULL,
                  `trial_period` int(11) NOT NULL DEFAULT \'1\',
                  `_order` int(11) DEFAULT NULL,
                  `updated_at` datetime NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';

        $this->db->query($sql);


    }
}