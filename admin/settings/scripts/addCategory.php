<?php

    session_start();

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
    
    $postType = $_POST['postType'];
    $name = $_POST['name'];
    $desc = $_POST['desc'];
    $image = $_POST['image'];
    $parent = $_POST['parent'];
    $url = slugify($name);

    if($parent == 0) {
        $level = 0;
    }
    else {
        $parentLevel = $mysqli->query("SELECT level FROM `categories` WHERE id = '{$parent}' LIMIT 1")->fetch_array()[0];
        
        $level = $parentLevel + 1;
    }

    $addCategory = $mysqli->prepare("INSERT INTO `categories` (post_type, name, description, image, url, parent_id, level) VALUES(?, ?, ?, ?, ?, ?, ?)");
    $addCategory->bind_param('sssssii', $postType, $name, $desc, $image, $url, $parent, $level);
    $addCategory->execute();

    if(!$addcategory->error) {
        echo json_encode([1, 'Category added successfully']);
        
        $_SESSION['categoryMessage'] = 'Category added successfully.';
    }
    else {
        echo json_encode([0, 'Error: Could not create category.']);
    }
    
?>