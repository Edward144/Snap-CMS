<?php

    require_once('../../includes/database.php');

    //Update Slider
    $slider = $mysqli->prepare("UPDATE `sliders` SET name = ?, post_type_id = ?, post_id = ?, animation_in = ?, animation_out = ?, speed = ? WHERE id = ?");
    $slider->bind_param('siissii', $_POST['name'], $_POST['postType'], $_POST['postName'], $_POST['animationIn'], $_POST['animationOut'], $_POST['speed'], $_POST['id']);
    $ex = $slider->execute();

    if($ex === false) {
        echo json_encode('Error: Could not save slider');
        
        exit();
    }

    //Update Slides
    $delete = $mysqli->prepare("DELETE FROM `slider_items` WHERE slider_id = ?");
    $delete->bind_param('i', $_POST['id']);
    $delete->execute();

    $slides = $mysqli->prepare("INSERT INTO `slider_items` (slider_id, position, image_url, content) VALUES (?, ?, ?, ?)");

    foreach($_POST['slides'] as $index => $value) {
        
        $position = $value['position'];
        $image = $value['image'];
        $content = $value['content'];
        
        $slides->bind_param('iiss', $_POST['id'], $position, $image, $content);
        $ex = $slides->execute();
        
        if($ex === false) {
            echo json_encode('Error: Could not save all slides');
            
            exit();
        }
    }
    
    echo json_encode('Slider has been saved');

?>