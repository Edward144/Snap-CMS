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
        
        <link href="<?php echo ROOT_DIR; ?>admin/includes/adminStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        
        <!-- Ensure docRoot.js is the first script after jQuery -->
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/docRoot.js"></script>
        
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/default.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinymce.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/plugins/moxiemanager/js/moxman.loader.min.js"></script>
        
        <script>            
            tinymce.init({
                selector:'textarea:not(.noTiny):not(.tinySlider)',
                plugins: 'paste image imagetools table code save link moxiemanager media fullscreen lists',
                menubar: 'file edit format insert table',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code fullscreen',
                relative_urls: false,
                remove_script_host: false,
                image_title: true,
                height: 260,
                content_css: root_dir + "includes/custom.css",
                extended_valid_elements: 'span',
            });
        </script>
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