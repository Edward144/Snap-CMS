<?php

    require_once('../../includes/database.php');

    $level = $mysqli->prepare("SELECT level FROM `categories` WHERE id = ?");
    $level->bind_param('i', $_POST['catParent']);
    $level->execute();
    $level = $level->get_result();
    
    if($level->num_rows > 0) {
        $level = $level->fetch_array()[0];
        
        if($level > 3) {
            $level = 3;
        }
        else {
            $level = $level + 1;
        }
    }
    else {
        $level = 0;
    }
    
    $create = $mysqli->prepare("INSERT INTO `categories` (post_type_id, name, description, image_url, parent_id, level) VALUES (?, ?, ?, ?, ?, ?)");
    $create->bind_param('isssii', $_POST['postTypeId'], $_POST['catName'], $_POST['catDesc'], $_POST['catImage'], $_POST['catParent'], $level);
    $ex = $create->execute();

    if($ex === false) {
        $_SESSION['createmessage'] == 'Error: Could not create category';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

    $_SESSION['createmessage'] = 'Category has been created';

    header('Location: ' . $_POST['returnUrl']);
    exit();

?>