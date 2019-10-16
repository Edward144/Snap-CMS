<?php

    require_once('../../includes/database.php');
    
    //var_dump($_POST); exit();

    $delete = $mysqli->prepare("DELETE FROM `navigation_menus` WHERE id = ?");
    $delete->bind_param('i', $_POST['menus']);
    $ex = $delete->execute();

    if($ex === false) {
        $_SESSION['deletemessage'] == 'Error: Could not delete menu';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

    $delete = $mysqli->prepare("DELETE FROM `navigation_structure` WHERE menu_id = ?");
    $delete->bind_param('i', $_POST['menus']);
    $ex = $delete->execute();

    if($ex === false) {
        $_SESSION['deletemessage'] == 'Error: Menu could only be partially deleted';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

    header('Location: ' . $_POST['returnUrl']);

?>