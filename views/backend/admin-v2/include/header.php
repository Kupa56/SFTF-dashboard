<?php
?>
<!DOCTYPE html>
<html>
<head>

    <base href="<?= base_url() ?>"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= Translate::sprint("Dashboard") ?> | <?= APP_NAME ?></title>

    <link rel="icon" href="<?= adminAssets() ?>/images/favicon.ico" type="image/x-icon">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="<?= adminAssets() ?>/bootstrap/css/bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=adminAssets()?>/plugins/select2/select2.min.css">
    <link rel="stylesheet" href="<?=adminAssets()?>/dist/css/admin.css">
    <link rel="stylesheet" href="<?=adminAssets()?>/plugins/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/iCheck/all.css">
    <link rel="stylesheet" href="<?= adminAssets() ?>/dist/css/skins/skin-light.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet"
          href="<?= adminAssets() ?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/datatables/dataTables.bootstrap.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/minified/themes/default.min.css"
          type="text/css" media="all"/>
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/datatables/jquery.dataTables.min.css"
          type="text/css" media="all"/>
    <link rel="stylesheet" href="<?= adminAssets() ?>/plugins/colorpicker/bootstrap-colorpicker.css">

    <!-- Google Material design icons -->
    <link rel="stylesheet" href="<?=adminAssets()?>/icons/materialdesignicons/css/materialdesignicons.min.css">
    <!-- Font awsome 4.4.0 icons -->
    <link rel="stylesheet" href="<?=adminAssets()?>/icons/font-awsome/css/font-awesome-4.4.0.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?=adminAssets()?>/icons/ionicons/css/ionicons.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= adminAssets() ?>/custom_skin/style.css">
<?php if (Translate::getDir() == "rtl"): ?>
        <link rel="stylesheet" href="<?= adminAssets() ?>/custom_skin/rtl.css">
<?php endif; ?>

    <!-- External libraries -->
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112054244-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?=DASHBOARD_ANALYTICS?>');
    </script>

    <script src="<?= adminAssets("plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
<?php AdminTemplateManager::loadCssLibs() ?>


    <style>
        

        .skin-blue .main-header .logo {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            border: 1px solid var(--primary-color) !important;
        }

        .skin-blue .sidebar-menu > li:hover > a, .skin-blue .sidebar-menu > li.active > a {
            border-left-color: var(--primary-color);
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.hover {
            background-color: var(--primary-color) !important;
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        a {
            color: var(--primary-color);
        }

        .skin-blue .main-header .navbar .sidebar-toggle {
            color: var(--primary-color);
        }

        .skin-blue .main-header .navbar .sidebar-toggle:hover {
            background-color: var(--primary-color);
        }

        .image-uploaded #delete {
            background-color: var(--primary-color);
        }

        #progress {
            border: 1px solid var(--primary-color);
        }

        #progress .percent {
            background: var(--primary-color);
        }

        .direct-chat-primary .right .direct-chat-text {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .nsup-btn {
            background: var(--primary-color);
        }

        .nsup-btn strong{
            color: #ffffff;
        }

        .full-width{
            width: 100%;
        }


    </style>
<?php AdminTemplateManager::loadScriptsLibs() ?>


</head>

<body class="hold-transition skin-blue sidebar-mini skin-custom-sf" dir="<?= Translate::getDir() ?>">
<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
    <?php if ($this->session->userdata('agent') == "mobile") { ?>

    <?php } else { ?>
            <a href="<?= site_url("") ?>" class="logo">

                <span class="logo-lg"> <b style="text-transform: uppercase"><?= strtoupper(APP_NAME) ?></b></span>
                <span class="logo-mini"></span>

            </a>
    <?php } ?>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only"><?= Translate::sprint("Toggle navigation", "") ?></span>
            </a>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">


                <?php CMS_Display::render('subscription_status_v1')?>
                <?php CMS_Display::render('campaigns_pending_list_v1')?>

                    <!-- Control Sidebar Toggle Button -->
                <?php CMS_Display::render('dropdown_v1')?>
                <?php CMS_Display::render('language_dropdown_v1')?>

                <?php CMS_Display::render('user_v1');?>



                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <!-- Sidebar Menu -->
        <?php $this->load->view(AdminPanel::TemplatePath."/include/sidebar"); ?>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>


