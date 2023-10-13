<?php 

    /******************************************************PATHS CONFIGURATION*************************************/
    // you can change this to let files stays protecteds
    define("__ADMIN", "app");
    // If deployed in a web server, change this according to your configuration
    // For Example. the domain name is www.domain.com, then if the php files are stored in
	// a folder named as "responsive" then the complete url would be
	// www.domain.com/dashboard
    define("BASE_URL", "http://stuffed.local");
	// Folder directory for images uploaded from the desktop
    // Change Only the domain name and application folder  :  http://domain/nearbystores
    define("IMAGES_BASE_URL","http://stuffed.local/uploads/images/");


    /******************************************************DATABASE CONFIGURATION *****************************/
    //Set your database Host name
    define("HOST_NAME", "localhost");
    // change the user access, CPanel have user roles, when writing and reading files
	// set it to allow the certain User to read/write
    define("DB_USERNAME", "root");
    // change this according to your account credentials
    define("DB_PASSWORD", "root-9625");
    // if you wish you create your own name for   Database then change the word "db_name"
    define("DB_NAME", "stuffed_db");

	define("CONF_VERSION", "2.0.0");
    define("CRYPTO_KEY", "8cf3f0af86751540083fcb5b27d582b3");
    define("PARAMS_FILE", md5(CRYPTO_KEY));
