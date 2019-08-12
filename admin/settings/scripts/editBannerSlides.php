<?php
    
    header('Content-type: application/json');
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $bannerId = $_POST['id'];
    $position = $_POST['position'];
    $image = $_POST['image'];
    $content = $_POST['content'];
    $live = $_POST['live'];

    if($live === 'true') {
        $type = '(Live) ';
    }
    else {
        $type = '(Preview) ';
    }

    $checkSlide = $mysqli->prepare("SELECT COUNT(*) FROM `banners_slides` WHERE banner_id = ? AND position = ?");
    $checkSlide->bind_param('ii', $bannerId, $position);
    $checkSlide->execute();
    $checkResult = $checkSlide->get_result()->fetch_row()[0];
    $checkSlide->close();
    
    if(($content == null || $content == '') && ($image == null || $image == '')) {
        echo json_encode($type . 'Skipped slide ' . $position . ', no content or image set.');
        exit();
    }

    if($checkResult == 0) {
        if($live === 'true') {
            $updateSlide = $mysqli->prepare("INSERT INTO `banners_slides` (banner_id, position, live_background, live_content) VALUES(?, ?, ?, ?)");
        }
        else {
            $updateSlide = $mysqli->prepare("INSERT INTO `banners_slides` (banner_id, position, preview_background, preview_content) VALUES(?, ?, ?, ?)");
        }
        
        $updateSlide->bind_param('iiss', $bannerId, $position, $image, $content);
        $updateSlide->execute();
    }
    else {
        if($live === 'true') {
            $updateSlide = $mysqli->prepare("UPDATE `banners_slides` SET live_background = ?, live_content = ? WHERE banner_id = ? AND position = ?");
        }
        else {
            $updateSlide = $mysqli->prepare("UPDATE `banners_slides` SET preview_background = ?, preview_content = ? WHERE banner_id = ? AND position = ?");
        }
        
        $updateSlide->bind_param('ssii', $image, $content, $bannerId, $position);
        $updateSlide->execute();
    }

    if(!$updateSlide->error) {
        echo json_encode($type . 'Slide ' . $position . ' updated sucessfully.');
    }
    else {
        echo json_encode($type . 'Error: Could not update slide ' . $position . '.');
    }

?>