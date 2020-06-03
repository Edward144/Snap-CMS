<?php 
    ob_start();

    require_once(dirname(__DIR__, 2) . '/includes/database.php');
    require_once(dirname(__DIR__, 2) . '/includes/functions.php');
    
    $classes = scandir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/includes/classes');

    foreach($classes as $class) {
        if(strpos($class, '.class') !== false) {
            include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/includes/classes/' . $class);
        }
    }

    //Redirect to login if user is not logged in
    if(!isset($_SESSION['adminusername'])) {
        header('Location: ' . ROOT_DIR . 'login');
        
        exit();
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <title><?php echo adminTitle() . ' | ' . cmsName($mysqli); ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="<?php echo ROOT_DIR; ?>admin/includes/colourScheme.css" rel="stylesheet" type="text/css">
        <link href="<?php echo ROOT_DIR; ?>admin/includes/adminStyles.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        
        <!-- Ensure docRoot.js is the first script after jQuery -->
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/docRoot.min.js"></script>
        
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/default.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinymce.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/plugins/moxiemanager/js/moxman.loader.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinyConfig.min.js"></script>
    </head>
    
    <body>
        <header id="adminHeader">
            <div class="headerInner">
                <div class="logoWrap">
                    <h2><?php echo cmsName($mysqli); ?></h2>
                </div>
                
                <div class="detailsWrap">
                    <h1><?php echo adminTitle(); ?></h1>
                </div>
            </div>
            
            <?php include_once(dirname(__DIR__) . '/includes/navigation.php'); ?>
        </header>
        
        <div class="adminWrap">
            <div class="content">