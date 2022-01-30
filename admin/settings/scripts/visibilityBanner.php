<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if($action == 'view') {
        $action = 0;
    }
    elseif($action == 'hide') {
        $action = 1;
    }

    $visibility = $mysqli->prepare("UPDATE `banners` SET visible = ? WHERE id = ?");
    $visibility->bind_param('ii', $action, $id);
    $visibility->execute();
    $visibility->close();

    if(!$mysqli->error) {
        echo json_encode($action);
    }
    else {
        echo json_encode('Error: Could not amend banner.');
    }

?>