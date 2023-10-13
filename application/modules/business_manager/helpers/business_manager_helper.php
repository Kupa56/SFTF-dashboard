<?php


class BusinessManager{


}

function business_manager_url($uri){
    return site_url("business_manager/".$uri);
}

function business_manager_url_admin($uri){
    return admin_url("business_manager/".$uri);
}