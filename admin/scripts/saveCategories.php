<?php

    require_once('../../includes/database.php');

    $update = $mysqli->prepare("UPDATE `categories` SET name = ?, image_url = ?, description = ? WHERE id = ?");

    foreach($_POST['catTree'] as $index => $value) {
        $update->bind_param('sssi', $value['name'], $value['image'], $value['description'], $value['id']);
        $ex = $update->execute();
        
        if($ex === false) {
            echo json_encode([0, 'Error: Could not update all categories']);
            
            exit();
        }
        
        if($value['delete'] == 1) {
            $delete = $mysqli->query("DELETE FROM `categories` WHERE id = {$value['id']}");
        }
    }

    echo json_encode([1, 1]);
    exit();

?>