<!DOCTYPE html>
<html>
<head>

<?php if(isset($title)): ?>
        <title><?=$title?></title>
<?php else: ?>
        <title><?=_lang("Business Manager")?> - <?=APP_NAME?></title>
<?php endif; ?>

    <base href="<?= base_url() ?>"/>
    <link rel="stylesheet" href="//cdn.materialdesignicons.com/2.3.54/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="//cdn.materialdesignicons.com/5.0.45/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?=_lang("Business Manager")?></title>
    <link rel="stylesheet" href="<?=AdminTemplateManager::assets("business_manager", "css/framework7.ios.min.css")?>">
<?php if(Translate::getDir()=="rtl"): ?>
    <link rel="stylesheet" href="<?=AdminTemplateManager::assets("business_manager", "css/framework7.rtl.min.css")?>">
<?php endif; ?>
    <link rel="stylesheet" href="<?=AdminTemplateManager::assets("business_manager", "css/style.css")?>">
    <style>
        @font-face{
            font-family:"Ionicons";
            src:url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.eot")?>");
            src:url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.eot")?>") format("embedded-opentype"),
            url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.woff2")?>") format("woff2"),
            url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.woff")?>") format("woff"),
            url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.ttf")?>") format("truetype"),
            url("<?=AdminTemplateManager::assets("business_manager", "fonts/ionicons/ionicons.svg")?>") format("svg");
            font-weight:normal;font-style:normal}

    </style>
    <link rel="stylesheet" href="<?=AdminTemplateManager::assets("business_manager", "css/ionicons.css")?>">
<?php AdminTemplateManager::loadScriptsLibs() ?>
<?php AdminTemplateManager::loadCssLibs(); ?>


</head>

<body class="color-theme-pink" dir="rtl">
<!-- App root element -->