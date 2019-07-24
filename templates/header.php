<!DOCTYPE html>

<?php 
    session_start(); 

    include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/functions.php');
?>

<html>
    <head>
        <link href="/templates/style.css" rel="stylesheet" type="text/css">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php 
            if(isset($_GET['url'])) {
                $checkPosts = $mysqli->query("SELECT * FROM `posts` WHERE url = '{$_GET['url']}' LIMIT 1");
                $checkPages = $mysqli->query("SELECT * FROM `pages` WHERE url = '{$_GET['url']}' LIMIT 1");
                
                if($checkPosts->num_rows > 0) {
                    $row = $checkPosts->fetch_assoc();
                    $author = $mysqli->query("SELECT first_name, last_name FROM `users` WHERE username = '{$row['author']}'")->fetch_assoc();
                    
                    echo '<title>' . $row['name'] . '</title>';
                    echo '<meta name="description" content="' . $row['description'] . '">';
                    echo '<meta name="author" content="' . $author['first_name'] . ' ' . $author['last_name'] . '">';
                }
                elseif($checkPages->num_rows > 0) {
                    $row = $checkPages->fetch_assoc();
                    
                    echo '<title>' . $row['name'] . '</title>';
                    echo '<meta name="description" content="' . $row['description'] . '">';
                    echo '<meta name="author" content="' . $author['first_name'] . ' ' . $author['last_name'] . '">';
                }
            }
        ?>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        
        <link href="/scripts/lightbox.css" rel="stylesheet" type="text/css">
        <script src="/scripts/lightbox.js"></script>
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
            