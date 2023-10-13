<?php
    $m_uri = $this->uri->segment(1);
    $uri = $this->uri->segment(2);
$title = "";

    if($uri=="login"){
        $title = Translate::sprint("Login");
    }else if($uri=="signup"){
        $title = Translate::sprint("Sign Up");
    }else if($uri=="fpassword"){
        $title = Translate::sprint("Forget Password");
    }else if($uri=="rpassword"){
        $title = Translate::sprint("Reset Password");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <base href="<?=  base_url()?>"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$title." - ".APP_NAME?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?=adminAssets("bootstrap/css/bootstrap.min.css")?>">
    <!-- Font awsome 4.4.0 icons -->
    <link rel="stylesheet" href="<?=adminAssets("icons/font-awsome/css/font-awesome-4.4.0.min.css")?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?=adminAssets("icons/ionicons/css/ionicons.min.css")?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=adminAssets("dist/css/admin.css")?>">
    <link rel="stylesheet" href="<?=adminAssets("plugins/datatables/dataTables.bootstrap.css")?>">
    <link rel="stylesheet" href="<?=adminAssets("plugins/datepicker/datepicker3.css")?>">
    <link rel="stylesheet" href="<?=adminAssets("dist/css/skins/skin-blue.min.css")?>">
    <link rel="stylesheet" href="<?=adminAssets("plugins/select2/select2.min.css")?>">
    <link rel="stylesheet" href="<?=adminAssets("custom_skin/style.css")?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="<?=  adminAssets("plugins/minified/themes/default.min.css")?>" type="text/css" media="all" />

    <link rel="stylesheet" href="//cdn.materialdesignicons.com/2.3.54/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112054244-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?=DASHBOARD_ANALYTICS?>');
    </script>

    <style>

        .skin-blue .main-header .logo{
            color: <?=DASHBOARD_COLOR?> !important;
        }

        .btn-primary {
            background-color: <?=DASHBOARD_COLOR?>;
            border-color: <?=DASHBOARD_COLOR?>;
            border: 1px solid #eeeeee;
        }


        .skin-blue .sidebar-menu > li:hover > a, .skin-blue .sidebar-menu > li.active > a{
            border-left-color: <?=DASHBOARD_COLOR?>;
        }


        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.hover {
            background-color: <?=DASHBOARD_COLOR?> !important;
            border: 1px solid #eeeeee !important;
        }

        .skin-blue .sidebar-menu > li:hover > a, .skin-blue .sidebar-menu > li.active > a{
            border-left-color: <?=DASHBOARD_COLOR?>;
        }

        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
            background-color: <?=DASHBOARD_COLOR?> !important;
            border-color: <?=DASHBOARD_COLOR?> !important;
        }
        a{
            color: <?=DASHBOARD_COLOR?> ;
        }

        .skin-blue .main-header .navbar .sidebar-toggle{
            color: <?=DASHBOARD_COLOR?> ;
        }

        .skin-blue .main-header .navbar .sidebar-toggle:hover {
            background-color: <?=DASHBOARD_COLOR?> ;
        }

        .full-width{
            width: 100%;
        }


    </style>

<?php if(Translate::getDir()=="rtl"): ?>
        <link rel="stylesheet" href="<?=  adminAssets("custom_skin/rtl.css")?>">
<?php endif; ?>


</head>

<?php

        $logo = ImageManagerUtils::getValidImages(APP_LOGO);
        $imageUrl = adminAssets("images/logo.png");
        if(!empty($logo)){
            $imageUrl = $logo[0]["560_560"]["url"];
        }

    ?>

<body class="hold-transition login-page"  dir="<?=Translate::getDir()?>">
<div class="login-box">
    <div class="login-logo">
        <img style="max-width: 260px;max-height: 75px;" src="<?= $imageUrl ?>">
    </div><!-- /.login-logo -->