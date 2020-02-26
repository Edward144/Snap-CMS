<?php

    require_once('../../includes/database.php');
    
    $mysqli->query("INSERT INTO `sliders`VALUES ()");

    $newId = $mysqli->insert_id;

    $mysqli->query("UPDATE `sliders` SET name = 'Slider {$newId}' WHERE id = {$newId}");
    
    if($mysqli->error) {
        $_SESSION['createmessage'] = 'Error: Could not create slider';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    else {
        header('Location: ../sliders?id=' . $newId);
        exit();
    }

?>