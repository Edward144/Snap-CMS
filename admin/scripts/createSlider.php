<?php

    require_once('../../includes/database.php');
    
    $mysqli->query("INSERT INTO `sliders` VALUES ()");
    
    if($mysqli->error) {
        $_SESSION['createmessage'] = 'Error: Could not create slider';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    else {
        header('Location: ../sliders/id-' . $mysqli->insert_id);
        exit();
    }

?>