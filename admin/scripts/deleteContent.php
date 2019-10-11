<?php

    require_once('../../includes/database.php');

    $visibility = $mysqli->prepare("DELETE FROM `{$_POST['tableName']}` WHERE id = ?");
    $visibility->bind_param('i', $_POST['id']);
    $ex = $visibility->execute();

    if($ex === false) {
        echo json_encode(0);
    }
    else {
        echo json_encode(1);
    }

?>