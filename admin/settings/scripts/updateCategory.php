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
    
    $id = $_POST['id'];
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

    $updateCategory = $mysqli->prepare(
        "UPDATE `categories` SET
            post_type = ?, 
            name = ?, 
            description = ?, 
            image = ?, 
            url = ?, 
            parent_id = ?, 
            level = ?
        WHERE id = ?");

    $updateCategory->bind_param('sssssiii', $postType, $name, $desc, $image, $url, $parent, $level, $id);
    $updateCategory->execute();

    if(!$updatecategory->error) {
        echo json_encode([1, 'Category updated successfully']);
    }
    else {
        echo json_encode([0, 'Error: Could not update category.']);
    }
    
?>