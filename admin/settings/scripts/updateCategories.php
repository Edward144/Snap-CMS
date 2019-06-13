<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $name = $_POST['name'];
    $parent = $_POST['parentId'];
    $position = $_POST['position'];
    $level = $_POST['level'];
    $customId = $_POST['customId'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $truncate = $_POST['truncate'];
    
    if($truncate == 1) {
        $mysqli->query("TRUNCATE TABLE `categories`");
        
        echo json_encode('Updating Categories:');
        
        exit();
    }

    if($name != null) {
        $updateNav = $mysqli->prepare("INSERT INTO `categories` (name, description, image_url, parent_id, position, level, custom_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $updateNav->bind_param('sssiiii', $name, $description, $image, $parent, $position, $level, $customId);
        $updateNav->execute();
        $updateNav->close();

        if(!$updateNav->error) {
            echo json_encode('Level ' . $level . ': Added category ID ' . $customId);
        }
        else {
            echo json_encode('Error: Could not add category ID ' . $customId . ' to level ' . $level . '.');
        }
    }
    else {
        echo json_encode('Level ' . $level . ': Could not add null category at position ' . $position . '.');
    }

?>