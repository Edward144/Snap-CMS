<?php

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    $update = $mysqli->prepare("UPDATE `navigation_structure` SET name = ?, image_url = ?, url = ?, position = ?, parent_id = ? WHERE id = ?");

    foreach($_POST['navTree'] as $index => $value) {
        $url = $value['slug'];
        $update->bind_param('sssiii', $value['name'], $value['image'], $url, $value['position'], $value['parent'], $value['id']);
        $ex = $update->execute();
        
        if($ex === false) {
            echo json_encode([0, 'Error: Could not update all items']);
            
            exit();
        }
        
        if($value['delete'] == 1) {
            $delete = $mysqli->query("DELETE FROM `navigation_structure` WHERE id = {$value['id']}");
        }
    }

    echo json_encode([1, 1]);
    exit();

?>