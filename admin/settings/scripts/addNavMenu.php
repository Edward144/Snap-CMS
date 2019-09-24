<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $name = $_POST['menuName'];

    $addMenu = $mysqli->prepare("INSERT INTO `menus` (menu_name) VALUES (?)");
    $addMenu->bind_param('s', $name);
    $addMenu->execute();

    if(!$addMenu->error) {
        echo json_encode([1, $mysqli->insert_id]);
    }
    else {
        echo json_encode([0, 'Error: Could not create menu.']);
    }

?>