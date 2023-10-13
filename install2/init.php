<?php
if(file_exists("../init.php")){
    require_once "../init.php";
}
// Default timezone
ini_set('display_errors', 1);

if(function_exists("date_default_timezone_set")){
    date_default_timezone_set("UTC");
}


// Defaullt multibyte encoding
$mbstring = extension_loaded('mbstring') && function_exists('mb_get_info') ;
if($mbstring)
mb_internal_encoding("UTF-8"); 



// ROOTPATH
define("ROOTPATH", dirname(__FILE__)."/..");

// Check if SSL enabled
$ssl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off" 
     ? true : false;
define("SSL_ENABLED", $ssl);

// Define APPURL
$app_url = (SSL_ENABLED ? "https" : "http")
         . "://"
         . $_SERVER["SERVER_NAME"]
         . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
         . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

$p = strrpos($app_url, "/install2");
if ($p !== false) {
    $app_url = substr_replace($app_url, "", $p, strlen("/install2"));
}




function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

$absolute_url = url_origin( $_SERVER );

define("APPURL", $absolute_url);

require_once ROOTPATH."/install2/app/helpers/common.helper.php";
require_once ROOTPATH."/install2/app/helpers/password.helper.php";
require_once ROOTPATH."/install2/app/helpers/db.helper.php";
require_once ROOTPATH."/install2/app/vendor/RandomCompat/random.php";
require_once ROOTPATH."/install2/app/core/Autoloader.php";

$loader = new Autoloader;
$loader->register();
$loader->addBaseDir(ROOTPATH.'/install2/app/core');
$loader->addBaseDir(ROOTPATH.'/install2/app/vendor');
