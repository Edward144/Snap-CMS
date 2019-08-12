<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $id = $mysqli->query("SELECT id FROM `banners` ORDER BY id DESC LIMIT 1")->fetch_array()[0] + 1;

    $title = 'Banner ' . $id;

    $add = $mysqli->prepare(
        "INSERT INTO `banners` (
            name
        ) VALUES(
            ?
        )"
    );

    $add->bind_param('s', $title);
    $add->execute();
    $add->close();
    
    $id = $mysqli->insert_id;

    if(!$mysqli->error) {
        echo json_encode([1, $id]);
    }
    else {
        echo json_encode([0, 'Error: Could not amend ' . $_GET['type'] . '.']);
    }

?>