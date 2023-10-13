<?php

    require_once 'init.php';
    require_once '../init.php';

    //start with remoive unwanted files
    deleteDir("../application/modules/demo");


    $redirection_url = APPURL;

    if (Input::post("action") != "install") {
        jsonecho("Invalid action", 101);
    }

    $required_fields = [
        "key",
        "db_host",
        "db_name",
        "db_username",
        "admin_path",
    ];

    $required_fields[] = "user_username";
    $required_fields[] = "user_name";
    $required_fields[] = "user_email";
    $required_fields[] = "user_password";
    $required_fields[] = "user_timezone";
    $required_fields[] = "crypto_key";

    foreach ($required_fields as $f) {
        if (!Input::post($f)) {
            jsonecho("Missing data: " . $f, 102);
        }
    }

    //init variablesÒ
    $base_url0 = APPURL;
    $base_url = APPURL . "/index.php";
    $admin_url = APPURL . "/index.php/" . Input::post("admin_path");
    $license_key = Input::post("key");
    $email = Input::post("user_email");
    $username = Input::post("user_username");
    $password = Input::post("user_password");
    $name = Input::post("user_name");
    $crypto_key = Input::post("crypto_key");
    $timezone = Input::post("user_timezone");

    $api_endpoint = "https://apiv2.webox360.win/api/api3";

    $post_data = array(
        "pid" => $license_key,
        "ip" => getIp(),
        "uri" => APPURL,
        "email" => $email,
        "item" => INSTALL_PROJECT_ID . "@" . APP_VERSION,
        "reqfile" => 1,
        "data" => base64_encode(json_encode(array("email" => $email, "username" => $username, "crypto_key" => $crypto_key)))
    );

    // Validate License Key
    $validation_url = $api_endpoint . "/lvalidate";

    try {
        $validation = runCURL($validation_url, $post_data);
        $validation = json_decode($validation);
    } catch (Exception $e) {
        jsonecho("The API server is down! message error: \"" . $e->getMessage() . "\"", 105);
    }


    if (!isset($validation->success)) {
        jsonecho("Couldn't validate your license key!. (Error:011)", 104);
    }

    if ($validation->success != 1) {
        if ($validation->success == 0) {
            jsonecho("Couldn't validate your license key! (Error:012)" . json_encode($validation), 105);
        } else {
            jsonecho($validation->error, 105);
        }
    }


    //download files if needed
    if (isset($validation->download_file_url)) {

        $files = json_decode($validation->download_file_url, JSON_OBJECT_AS_ARRAY);
        foreach ($files as $file) {
            $download_url = $file['download_url'];
            $extract_path = $file['install_path'];
            $filename = $file['file_name'];
            //download file

            if($extract_path==""
                OR (is_dir_empty("../".$extract_path) OR !is_dir("../".$extract_path))){

                $fileContent = file_get_contents($download_url);
                file_put_contents("temp/".$filename, $fileContent);
                move_unzip("temp/".$filename,"../".$extract_path);

            }else if(!is_dir("../".$extract_path."/".$filename)){

                $fileContent = file_get_contents($download_url);
                file_put_contents("temp/".$filename, $fileContent);
                move_unzip("temp/".$filename,"../".$extract_path);

            }
        }

    }



    $dataconfig = $validation->dataconfig;
    $dataconfig = base64_decode($dataconfig);


    $host = Input::post("db_host");
    $user = Input::post("db_username");
    $pass = Input::post("db_password");
    $dbname = Input::post("db_name");
    $admin_path = Input::post("admin_path");
    $crypto_key = Input::post("crypto_key");

    //setup files
    $dataConfig = parse($dataconfig, array(
        "HOSTNAME" => $host,
        "USERNAME" => $user,
        "PASSWORD" => $pass,
        "DATABASE" => $dbname,
        "BASE_URL" => APPURL,
        "ADMIN_PATH" => strtolower($admin_path),
        "SECURE_URL" => APPURL,
        "IMAGES_BASE_URL" => APPURL . '/uploads/images/',
        "CRYPTO_KEY" => $crypto_key,
    ));

    $dataConfig = "<?php \n\n" . $dataConfig;

    //check file if exist
    if (file_exists(ROOTPATH . "/config/config.php"))
        @unlink(ROOTPATH . "/config/config.php");

    //generate config file
    try {

        saveInFile(ROOTPATH . "/config/config.php", $dataConfig);

        if (!file_exists(ROOTPATH . "/config/config.php")) {
            jsonecho("Couldn't generate config file, please change config folder to 0777", 105);

            @chmod(ROOTPATH . "/config", 0777);
            saveInFile(ROOTPATH . "/config/config.php", $dataConfig);
        }

    } catch (Exception $e) {
        jsonecho("Couldn't access to the config folder (Error:014)", 105);
    }


    $sql = $validation->datasql;
    $sql = base64_decode($sql);

    //setup database
    $pdo = setupDatabase($host, $user, $pass, $dbname, $sql);

    if (!$pdo) {
        $messages = "Please make sure that all privileges mysql user are guaranteed";
        jsonecho($messages, 105);
    }

    if(!file_exists("../.htaccess")){
        jsonecho("Please ensure the presence of the '.htaccess' file.", 105);
    }

    //generate modules table & get existing modules
    $api_endpoint_get_modules = $base_url . '/modules_manager/ajax/get_modules';
    $deployed_modules = runCURL($api_endpoint_get_modules, array());
    $deployed_modules = json_decode($deployed_modules, JSON_OBJECT_AS_ARRAY);

    if (!isset($deployed_modules['result'])) { //

        @unlink(ROOTPATH . "/config/" . md5($crypto_key) . ".json");
        @unlink(ROOTPATH . "/config/config.php");

        $messages = "Something wrong!<br>";
        $messages .= "Caused by your server configuration<br>";
        $messages .= "Before contacting our support, please make sure the following requirement are done:<br>";
        $messages .= "1. Config folder has permission 0777 <br>";
        $messages .= "2. index.php file has permission 0644 <br>";
        $messages .= "3. Server support mod_rewrite (Please Enable it then restart http server)<br>";
        $messages .= "4. All privileges mysql user are guaranteed<br>";
        $messages .= "5. be sure that the option ONLY_FULL_GROUP_BY is disabled in your MySQL database<br>";
        $messages .= "Don't forget delete all tables from database \"" . $dbname . "\" and set it up<br>";
        $messages .= "Please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>";

        jsonecho($messages, 105);
    }


    /*
     * INIT APP CONFIG
     */

    $settings = $validation->datasettings;
    $settings = base64_decode($settings);
    $settings = parse($settings, array(
        'PID' => $license_key,
        "DASHBOARD_VERSION" => APP_VERSION,
    ));



    //add main purchase id
    if(isset($validation->api)){

        if(!is_array($settings))
            $settings = json_decode($settings,JSON_OBJECT_AS_ARRAY);

        //add key and values
        $settings['EVT_PID_'.md5(INSTALL_PROJECT_ID)] = $license_key;
        $settings['API_'.md5(INSTALL_PROJECT_ID)] = $validation->api;

        //encode it to json format
        $settings = json_encode($settings);
    }

    $settings = base64_encode($settings);

    $api_url = $base_url . '/setting/init_settings';
    $response = runCURL($api_url, array("data" => $settings));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    /*
     * SETUP EXSITING MODULES
     */

    //install main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/install';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during install (Code: 0UI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }

    //install all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_install';
    $_response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($_response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during install (Code: 0BI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a><br>" . $_response, 105);
    }


    //enable main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/enable';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error(user):" . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during install (Code: 0UE), \n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }

    //enable all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_enable';
    $response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during install (Code: 0BE),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }

    //create application & admin

    $user_data = array(
        'login' => $username,
        'password' => $password,
        'email' => $email,
        'name' => $name,
        'timezone' => $timezone,
    );

    $api_url = $base_url . '/user/createDefaultUser';
    $_response = runCURL($api_url, $user_data);
    $response = json_decode($_response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);

    //jsonecho("DONE => ".$_response, 105);
    jsonecho(userConnectionInfo($username, $password, "", $admin_url), 1, $admin_url);

    //UN-DONE
    function userConnectionInfo($login, $password, $data, $url = "")
    {

        $html = "";
        $html .= "<b><u>Admin url:</u></b> <a href='" . $url . "' target='_blank'>" . $url . "</a><br>";
        $html .= "<b><u>Login:</u></b> $login <br> ";
        $html .= "<b><u>Password:</u></b> $password<BR> <BR>";
        $html .= $data;

        return $html;
    }

    function parse($content = "", $args = array())
    {

        foreach ($args as $key => $value) {
            $content = preg_replace("#\{" . $key . "\}#", $value, $content);
        }
        return $content;

    }

    function getIp()
    {
        return $_SERVER['SERVER_ADDR'];
    }


    function move_unzip($downloaded_file, $destination)
    {
        if (!file_exists($downloaded_file))
            return;

        $zipArchive = new ZipArchive();

        if ($zipArchive->open($downloaded_file) !== TRUE) {
            die ("An error occurred creating your ZIP file $downloaded_file.");
        }

        $zipArchive->extractTo($destination);
        $zipArchive->close();

        return TRUE;
    }

    function is_dir_empty($dir) {
        if (!is_readable($dir)) return null;
        return (count(scandir($dir)) == 2);
    }


    function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
           return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
