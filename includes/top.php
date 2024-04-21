<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        <?php echo $page_title; ?>
    </title>
    <meta charset="UTF-8">
    <meta name="apple-mobile-web-app-status-bar" content="#aa7700">
    <meta name="theme-color" content="black">
    <link rel="manifest" href="<?php echo DIR; ?>/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:locale" content="<?php if (isset($_COOKIE['lang'])) { echo $_COOKIE['lang']; } else { echo DEFAULTLANG; } ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo SYSTIT; ?>" />
	<meta property="og:url" content="<?php echo DIR; ?>" />
	<meta property="og:site_name" content="<?php echo SYSTIT; ?>" />
	<link rel="canonical" href="<?php echo DIR; ?>" />
	<link rel="shortcut icon" href="<?php echo DIR; ?>/AppImages/favicon.ico" />
    <?php echo $page_css; ?>
    <link rel="stylesheet" href="<?php echo DIR; ?>/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?php echo DIR; ?>/assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="<?php echo DIR; ?>/assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/sidebar.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>