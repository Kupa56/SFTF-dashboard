<?php

    require_once 'init.php';
    require_once '../init.php';

    //start with remoive unwanted files
    deleteDir("../application/modules/demo");


    $base_url0 = APPURL;
    $base_url = APPURL . "/index.php";


    //purchase verification
    $api_endpoint = "https://apiv2.webox360.win/api/api3";


    $post_data = array(
        "pid" => Input::post("pid"),
        "ip" => getIp(),
        "uri" => APPURL,
        "item" => INSTALL_PROJECT_ID . "@" . APP_VERSION,
        "update" => 1,
        "reqfile" => 1,
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


    //generate modules table & get existing modules
    $api_endpoint_get_modules = $base_url . '/modules_manager/ajax/get_modules';
    $_deployed_modules = runCURL($api_endpoint_get_modules, array());
    $deployed_modules = json_decode($_deployed_modules, JSON_OBJECT_AS_ARRAY);

    if (!isset($deployed_modules['result'])) { //
        $messages = "Something wrong! #UP01<br>";
        jsonecho($messages, 105);
    }


    //install main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/install';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during update (Code: 0UI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }


    //install all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_install';
    $_response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($_response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during update (Code: 0BI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a><br>" . $_response, 105);
    }

    //enable main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/enable';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error(user):" . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during update (Code: 0UE), \n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }

    //enable all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_enable';
    $response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);


    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during update (Code: 0BE),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }


    //upgrade all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_upgrade';
    $response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));


    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if (!isset($response['success'])) {
        jsonecho("Something went wrong during update (Code: 0BU),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='" . $base_url0 . "/logs/LogViewer.php'>here</a>", 105);
    }





    $new_settings = $validation->datasettings;
    $settings = base64_decode($new_settings);
    $settings = parse($settings, array(
        'PID' => Input::post("pid"),
        "DASHBOARD_VERSION" => APP_VERSION,
    ));


    //add main purchase id
    if (isset($validation->api)) {

        if (!is_array($settings))
            $settings = json_decode($settings, JSON_OBJECT_AS_ARRAY);

        //add key and values
        $settings['EVT_PID_' . md5(INSTALL_PROJECT_ID)] = Input::post("pid");
        $settings['API_' . md5(INSTALL_PROJECT_ID)] = $validation->api;

        //encode it to json format
        $settings = json_encode($settings);
    }



    $settings = base64_encode($settings);

    //enable main (user) modules
    $api_url = $base_url . '/setting/ajax/update_version';
    $response = runCURL($api_url, array("settings" => $settings));

    jsonecho("DONE", 1);


    function parse($content = "", $args = array())
    {

        foreach ($args as $key => $value) {
            $content = preg_replace("#\{" . $key . "\}#", $value, $content);
        }
        return $content;

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
