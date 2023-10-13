<?php




if(defined("PAYPAL_CONFIG_CLIENT_ID"))
$config['paypal_client_id'] = PAYPAL_CONFIG_CLIENT_ID;
if(defined("PAYPAL_CONFIG_SECRET_ID"))
$config['paypal_secret'] = PAYPAL_CONFIG_SECRET_ID;
/**
 * SDK configuration
 */
/**
 * Available option 'sandbox' or 'live'
 */
$config['paypal_settings'] = array(

    'mode' => PAYPAL_CONFIG_DEV_MODE,
    /**
     * Specify the max request time in seconds
     */
    'http.ConnectionTimeOut' => 1000,
    /**
     * Whether want to log to a file
     */
    'log.LogEnabled' => true,
    /**
     * Specify the file that want to write on
     */
    'log.FileName' => 'application/logs/paypal.log',
    /**
     * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
     *
     * Logging is most verbose in the 'FINE' level and decreases as you
     * proceed towards ERROR
     */
    'log.LogLevel' => 'FINE'
);

if(PAYPAL_CONFIG_DEV_MODE==TRUE)
    $config['paypal_settings']['mode'] ="sandbox";
else
    $config['paypal_settings']['mode'] ="live";
