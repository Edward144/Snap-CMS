<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    $name = slugify(strtolower($_POST['name']));
    $name = str_replace('-', '_', $name);
    $type = $_POST['type'];

    $checkExisting = $mysqli->prepare("SELECT COUNT(*) FROM `custom_posts` WHERE name = ?");
    $checkExisting->bind_param('s', $name);
    $checkExisting->execute();
    $checkResult = $checkExisting->get_result();

    if($checkResult->fetch_array()[0] > 0) {
        echo json_encode($name . ' skipped, already Exists.<br>');
    }
    else {
        //Insert Into Custom Posts Table
        $addCustom = $mysqli->prepare("INSERT IGNORE INTO `custom_posts` (name) VALUES(?)");
        $addCustom->bind_param('s', $name);
        $addCustom->execute();
        
        //Create Post Type Table
        $mysqli->query("CREATE TABLE `{$name}s` LIKE `posts`");
        
        //Create Post Type Categories
        $mysqli->query("CREATE TABLE `{$name}s_categories` LIKE `categories`");
        
        //Create Post Type Options
        if($type == 'product') {
            $mysqli->query(
                "CREATE TABLE IF NOT EXISTS `{$name}s_options` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    post_type_id INT UNIQUE,
                    gallery_images TEXT DEFAULT NULL,
                    gallery_main VARCHAR(255) DEFAULT NULL,
                    features VARCHAR(255) DEFAULT NULL,
                    specifications TEXT DEFAULT NULL,
                    output VARCHAR(255) DEFAULT NULL,
                    options VARCHAR(255) DEFAULT NULL
                )"
            );
        }
        
        //Echo Output
        echo json_encode($name . ' has been added.<br>');
    }
    
?>
