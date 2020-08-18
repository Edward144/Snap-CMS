<?php

    require_once('../../includes/database.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'saveStructure') {
        $insert = $mysqli->prepare("INSERT INTO `checklist_structure` (structure) VALUES(?)");
        $insert->bind_param('s', json_encode($_POST['sections']));
        $ex = $insert->execute();
        
        if($ex === false) {
            echo json_encode(['danger', 'Could not save checklist']);
            exit();
        }
        
        echo json_encode(['success', 'Checklist saved successfully']);
        exit();
    }

?>