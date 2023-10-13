<?php

$config['stripe_publishable_key'] = "xxxx";
$config['stripe_secret_key'] = "xxxx";

if(defined("STRIPE_PUBLISHABLE_KEY"))
    $config['stripe_publishable_key'] = STRIPE_PUBLISHABLE_KEY;
if(defined("STRIPE_SECRET_KEY"))
    $config['stripe_secret_key'] = STRIPE_SECRET_KEY;


