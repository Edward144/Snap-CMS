<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $id = $_GET['id'];
    $type = $_GET['type'] . 's';
    
    $delete = $mysqli->prepare("DELETE FROM `{$type}` WHERE id = ?");
    $delete->bind_param('i', $id);
    $delete->execute();
    $delete->close();

    if(!$mysqli->error) {
        echo json_encode([1, $_GET['type'] . 'has been deleted.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete ' . $_GET['type'] . '.']);
    }

?>