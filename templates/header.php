<!DOCTYPE html>

<?php 
    session_start(); 
    ob_start();
    include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/functions.php');
?>

<html>
    <head>        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php 
            $companyName = $mysqli->query("SELECT company_name FROM `company_info` WHERE company_name <> '' AND company_name IS NOT NULL")->fetch_array()[0];
            
            if($companyName != null && $companyName != '') {
                $companyName = ' | ' . $companyName;
            }
        
            if(isset($_GET['url'])) {
                if(!isset($_GET['postType'])) {
                    $checkPosts = $mysqli->query("SELECT * FROM `posts` WHERE url = '{$_GET['url']}' LIMIT 1");
                }
                else {
                    $checkPosts = $mysqli->query("SELECT * FROM `{$_GET['postType']}` WHERE url = '{$_GET['url']}' LIMIT 1");
                }
                
                if($checkPosts->num_rows > 0) {
                    $row = $checkPosts->fetch_assoc();
                    
                    echo '<title>' . $row['name'] . $companyName . '</title>';
                    echo '<meta name="description" content="' . $row['description'] . '">';
                    echo '<meta name="author" content="' . $row['author'] . '">';
                }
            }
            elseif($_SERVER['REQUEST_URI'] == '/') {
                echo '<title>Welcome' . $companyName . '</title>';
            }
            elseif(isset($_GET['postType'])) {
                echo '<title>' . ucwords(str_replace('-', ' ', $_GET['postType'])) . $companyName . '</title>';
            }
            else {
                $url = explode('/post-type/', $_SERVER['REQUEST_URI'])[1];
                $count = count($url) - 1;
                
                echo '<title>' . ucwords(explode('/', str_replace('-', ' ', $url))[0]) . $companyName . '</title>';
            }
        ?>
        
        <link href="/templates/style.css" rel="stylesheet" type="text/css">
        <link href="/templates/custom.css" rel="stylesheet" type="text/css">
        <link href="/scripts/lightbox.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/scripts/OwlCarousel2-2.3.4/owl.carousel.min.css">
        <link rel="stylesheet" href="/scripts/OwlCarousel2-2.3.4/owl.theme.default.min.css">
        <link href="/scripts/animate.min.css" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="/scripts/lightbox.js"></script>
        <script src="/scripts/color-thief.min.js"></script>
        <script src="/scripts/OwlCarousel2-2.3.4/owl.carousel.min.js"></script>
        
        <?php
            $analyticsCode = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'analytics'")->fetch_array()[0];
        
            if($analyticsCode != null && $analyticsCode != '') {
                echo
                    '<!-- Global site tag (gtag.js) - Google Analytics -->
                    <script async src="https://www.googletagmanager.com/gtag/js?id=' . $analyticsCode . '"></script>
                    <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag("js", new Date());

                    gtag("config", "' . $analyticsCode . '");
                    </script>';
            }
        ?>
    </head>
    
    <body>
        <header>
            <div class="headerInner">
                <div class="left">
                    <?php if($logo = $mysqli->query("SELECT logo FROM `company_info`")->fetch_array()[0]) : ?>
                        <a href="/"><img src="<?php echo $logo; ?>" alt="logo" class="logo"></a>
                    <?php endif; ?>
                </div>
                
                <div class="right">
                    <?php include_once('navigation.php'); ?>
                </div>
            </div>
        </header>
        
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/navigation.php'); ?>

        <div class="main">
            
