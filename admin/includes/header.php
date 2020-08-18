<?php 
    ob_start();

    require_once('../includes/database.php'); 
    require_once('../includes/functions.php');

    //Redirect to login if user is not logged in
    if(!isset($_SESSION['adminusername'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
        
        exit();
    }

    $classes = scandir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/includes/classes');

    foreach($classes as $class) {
        if(strpos($class, '.class') !== false) {
            include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/includes/classes/' . $class);
        }
    }

    $pageName = ucwords(str_replace('-', ' ', explode('.php', basename($_SERVER['PHP_SELF']))[0])); 
    $pageName = ($pageName == 'Index' ? 'Dashboard' : $pageName);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageName . ' | '  . companyName(); ?></title>
        
        <link rel="stylesheet" href="<?php echo ROOT_DIR; ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo ROOT_DIR; ?>css/adminStyle.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-xxzQGERXS00kBmZW/6qxqJPyxW3UR0BPsL4c8ILaIWXva5kFi7TxkIIaMiKtqV1Q" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="<?php echo ROOT_DIR; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>js/docRoot.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinymce.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/plugins/moxiemanager/js/moxman.loader.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/tinyConfig.min.js"></script>
    </head>

    <body>
        <div class="wrapper">
            <?php require_once('sidebar.php'); ?>
            
            <div class="content d-flex flex-column">
                <div class="container-fluid">
                    <div class="row bg-primary">
                        <div class="col-xl d-flex flex-wrap align-items-center py-1 text-light">
                            <h1>Snap CMS <small>v3</small>&nbsp;</h1>
                            
                            <h2 class="font-weight-normal ml-sm-auto mb-0"><?php echo $pageName; ?></h2>
                        </div>
                        
                        <div class="col-xl-4 d-flex align-items-center justify-content-end py-1 bg-secondary">
                            <h6 class="text-right text-light m-1">
                                Welcome <?php echo $_SESSION['adminusername']; ?>; <a href="<?php echo ROOT_DIR; ?>admin/scripts/logout" class="text-light"><wbr>
                                Logout <span class="fa fa-sign-out-alt"></span></a>
                            </h6>
                        </div>
                    </div>
                </div>