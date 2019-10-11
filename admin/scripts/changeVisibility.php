<?php

    require_once('../../includes/database.php');

    $visibility = $mysqli->prepare("UPDATE `{$_POST['tableName']}` SET visible = ? WHERE id = ?");
    $visibility->bind_param('ii', $_POST['visibility'], $_POST['id']);
    $ex = $visibility->execute();

    if($ex === false) {
        echo json_encode(0);
    }
    else {
        echo json_encode(1);
    }

?>