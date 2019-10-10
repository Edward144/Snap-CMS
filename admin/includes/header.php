<?php 
    require_once(dirname(__DIR__, 2) . '/includes/database.php');
    require_once(dirname(__DIR__, 2) . '/includes/functions.php');
    
    //Redirect to login if user is not logged in
    if(!isset($_SESSION['adminusername'])) {
        header('Location: ' . ROOT_DIR . 'login');
        
        exit();
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Admin | Snap CMS</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="<?php echo ROOT_DIR; ?>admin/includes/adminStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/default.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinymce.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/plugins/moxiemanager/js/moxman.loader.min.js"></script>
        
        <script>
            tinymce.init({
                selector:'textarea:not(.noTiny):not(.tinyBanner)',
                plugins: 'paste image imagetools table code save link moxiemanager media',
                menubar: 'file edit format insert table ',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code',
                relative_urls: false,
                remove_script_host: false,
                image_title: true,
                height: 400
            });
        </script>
    </head>
    
    <body>
        <header id="adminHeader">
            <div class="headerInner">
                <div class="logoWrap">
                    <h2>Snap CMS</h2>
                </div>
                
                <div class="detailsWrap">
                    <h1><?php echo adminBreadcrumbs(); ?></h1>
                </div>
            </div>
            
            <?php include_once(dirname(__DIR__) . '/includes/navigation.php'); ?>
        </header>
        
        <div class="adminWrap">
            <div class="content">