<!DOCTYPE html>

<?php
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE); 
?>

<?php 
    session_start(); 

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/functions.php');
?>

<html>
    <head>
        <link href="/admin/templates/admin-style.css" rel="stylesheet" type="text/css">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>        
        <script src="/admin/tinymce/tinymce.min.js"></script>
    </head>
    
    <body>
        <div class="header">
            <div class="headerInner">
                <div class="sidebarToggle" id="hidden"></div>
                
                <div class="left">
                    <a href="/admin/dashboard"><img class="logo" src="/admin/images/default-logo.png" alt="Logo"></a>
                </div>
                
                <div class="right">
                    <?php
                        $breadcrumbs = ltrim($_SERVER['REQUEST_URI'], '/');
                        $breadcrumbs = str_replace('/', ' > ', $breadcrumbs);
                        $breadcrumbs = str_replace('_', ' ', $breadcrumbs);
                        $breadcrumbs = explode('?', $breadcrumbs)[0];
                        $breadcrumbs = ucwords($breadcrumbs);
                        $breadcrumbs = 'CMS: ' . $breadcrumbs;
                    ?>
                    
                    <h2 class="breadcrumbs"><?php echo $breadcrumbs; ?></h2>
                </div>
                
            </div>
        </div>
        
        <div class="main">