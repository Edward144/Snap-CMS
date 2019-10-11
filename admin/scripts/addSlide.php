<?php

    require_once('../../includes/database.php');

    $addSlide = $mysqli->prepare("INSERT INTO `slider_items` (slider_id, position) VALUES (?, ?)");
    $addSlide->bind_param('ii', $_POST['sliderId'], $_POST['position']);
    $ex = $addSlide->execute();
    
    if($ex === false) {
        echo json_encode(0);
    }
    else {
        echo json_encode(1);
    }

?>