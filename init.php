<?php


    /*
     * default false
     */

    define("ENCODING", "UTF-8");

    //App version
    define("APP_VERSION", "3.1.3");
    define("APP_CODE_VERSION", 501);

    //logs levels
    define("APP_LOGS", 1);
    define("INDEX", "index.php");

    //Install app api requirements
    define("INSTALL_PROJECT_ID", "ns-android");//api item ID
    define("INSTALL_PROJECT_NAME", "NearbyStores ".APP_VERSION);

    /*
   * IMAGE CONFIGURATION
   */

    define("MAX_IMAGE_UPLOAD", 4); //by MB
    define("MAX_NBR_IMAGES", 6);
    define("MAX_STORE_IMAGES", 8);
    define("MAX_GALLERY_IMAGES", 20);
    define("MAX_FILE_UPLOAD",3);

    /*
     * SESS CONFIGURATION
     */

    define("SESS_USE_LOCAL_CACHE", false);


    /*
     * Set default time zone for server
     */

    @date_default_timezone_set("UTC");





