<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $bannerId = $_POST['bannerId'];
    $position = $_POST['position'];

    $delete = $mysqli->prepare("DELETE FROM `banners_slides` WHERE banner_id = ? AND position = ?");
    $delete->bind_param('ii', $bannerId, $position);
    $delete->execute();
    $delete->close();

    if(!$delete->error) {
        echo json_encode([1, 'Slide has been deleted.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete slide.']);
    }

?>