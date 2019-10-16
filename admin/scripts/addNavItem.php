<?php

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');
    
    $level = $mysqli->prepare("SELECT level FROM `navigation_structure` WHERE id = ?");
    $level->bind_param('i', $_POST['itemParent']);
    $level->execute();
    $level = $level->get_result();
    
    if($level->num_rows > 0) {
        $parent = $level->fetch_assoc();
        $level = $parent['level'];
        
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

    $url = slugify($_POST['itemSlug']);

    $items = $mysqli->prepare("SELECT position FROM `navigation_structure` WHERE parent_id = ? ORDER BY position DESC LIMIT 1");
    $items->bind_param('i', $_POST['itemParent']);
    $items->execute();
    $items = $items->get_result();

    if($items->num_rows > 0) {
        $position = $items->fetch_array()[0] + 1;
    }
    else {
        $position = 0;
    }
    
    $create = $mysqli->prepare("INSERT INTO `navigation_structure` (menu_id, name, image_url, url, parent_id, level, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $create->bind_param('isssiii', $_POST['menuId'], $_POST['itemName'], $_POST['itemImage'], $url, $_POST['itemParent'], $level, $position);
    $ex = $create->execute();

    if($ex === false) {
        $_SESSION['addmessage'] = 'Error: Could not create item';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

    $_SESSION['addmessage'] = 'Item has been created';

    header('Location: ' . $_POST['returnUrl']);
    exit();

?>