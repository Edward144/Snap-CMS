<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $id = $_GET['id'];
    
    $delete = $mysqli->prepare("DELETE FROM `banners` WHERE id = ?");
    $delete->bind_param('i', $id);
    $delete->execute();
    $delete->close();

    $delete = $mysqli->prepare("DELETE FROM `banners_slides` WHERE banner_id = ?");
    $delete->bind_param('i', $id);
    $delete->execute();
    $delete->close();

    if(!$mysqli->error) {
        echo json_encode([1, 'Banner has been deleted.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete banner.']);
    }

?>