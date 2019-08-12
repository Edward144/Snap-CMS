<!DOCTYPE html>

<?php
    //ini_set('display_errors', 0);
    //error_reporting(E_ERROR | E_WARNING | E_PARSE); 
?>

<?php 
    session_start(); 

    include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/functions.php');
?>

<html>
    <head>
        <link href="/admin/templates/admin-style.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto&display=swap" rel="stylesheet">
        <link href="/scripts/OwlCarousel2-2.3.4/owl.carousel.min.css" rel="stylesheet" type="text/css">
        <link href="/scripts/OwlCarousel2-2.3.4/owl.theme.default.min.css" rel="stylesheet" type="text/css">
        <link href="/scripts/animate.min.css" rel="stylesheet">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>        
        <script src="/admin/tinymce/tinymce.min.js"></script>
        <script src="/scripts/OwlCarousel2-2.3.4/owl.carousel.min.js"></script>
        
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
        
        <script src="/admin/tinymce/plugins/moxiemanager/js/moxman.loader.min.js"></script>
    </head>
    
    <body>
        <div class="header">
            <div class="headerInner">
                <div class="sidebarToggle" id="hidden"></div>
                
                <div class="left">
                    <?php 
                        $logo = $mysqli->query("SELECT logo FROM `company_info`")->fetch_array()[0];
                    ?>
                        
                    <?php if($logo) : ?>
                        <a href="/admin/dashboard"><img class="logo" src="<?php echo $logo; ?>" alt="Logo"></a>
                    <?php else : ?>
                        <a href="/admin/dashboard"><img class="logo" src="/admin/images/default-logo.png" alt="Logo"></a>
                    <?php endif; ?>
                </div>
                
                <div class="right">
                    <?php
                        $breadcrumbs = ltrim($_SERVER['REQUEST_URI'], '/');
                        $breadcrumbs = str_replace('/', ' > ', $breadcrumbs);
                        $breadcrumbs = str_replace('_', ' ', $breadcrumbs);
                        $breadcrumbs = explode('?', $breadcrumbs)[0];
                        $breadcrumbs = ucwords($breadcrumbs);
                    
                        $companyName = $mysqli->query("SELECT company_name FROM `company_info`");
                    
                        if($companyName->num_rows > 0) {
                            $companyName = ucwords($mysqli->query("SELECT company_name FROM `company_info`")->fetch_array()[0]);
                            
                            if($companyName == '') {
                                $companyName = 'CMS';
                            }
                            
                            $breadcrumbs = $companyName . ': ' . $breadcrumbs;
                        }
                    ?>
                    
                    <h2 class="breadcrumbs"><?php echo $breadcrumbs; ?></h2>
                </div>
                
            </div>
        </div>
        
        <div class="main">