<?php
    
    header('Content-type: application/json');
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $id = $_POST['id'];
    $name = $_POST['name'];
    $postType = $_POST['postType'];
    $postName = $_POST['postName'];
    $animIn = $_POST['animIn'];
    $animOut = $_POST['animOut'];
    $speed = $_POST['speed'];

    if($animIn == null || $animIn == '') {
        $animIn = 'flipInX';
    }

    if($animOut == null || $animOut == '') {
        $animOut = 'slideOutDown';
    }

    $checkBanner = $mysqli->prepare("SELECT COUNT(*) FROM `banners` WHERE id = ?");
    $checkBanner->bind_param('i', $id);
    $checkBanner->execute();
    $checkResult = $checkBanner->get_result()->fetch_row()[0];
    $checkBanner->close();

    if($checkResult == 0) {
        $updateBanner = $mysqli->prepare("INSERT INTO `banners` (post_type, post_type_id, name, animation_in, animation_out, speed) VALUES(?, ?, ?, ?, ?, ?)");
        $updateBanner->bind_param('sisssi', $postType, $postName, $name, $animIn, $animOut, $speed);
    }
    else {
        $updateBanner = $mysqli->prepare("UPDATE `banners` SET post_type = ?, post_type_id = ?, name = ?, animation_in = ?, animation_out = ?, speed = ? WHERE id = ?");
        $updateBanner->bind_param('sisssii', $postType, $postName, $name, $animIn, $animOut, $speed, $id);
    }

    $updateBanner->execute();
    $updateBanner->close();
    
    if(!$updateBanner->error) {
        echo json_encode('Banner has been updated sucessfully.');
    }
    else {
        echo json_encode('Error: Could not update banner.');
    }

?>