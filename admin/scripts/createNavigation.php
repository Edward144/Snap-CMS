<?php

    require_once('../../includes/database.php');

    $name = ucwords($_POST['menuName']);

    $create = $mysqli->prepare("INSERT INTO `navigation_menus` (name) VALUES (?)");
    $create->bind_param('s', $name);
    $ex = $create->execute();
    
    if($ex === false) {
        $_SESSION['createmessage'] = 'Error: Could not create menu';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    
    $id = $create->insert_id;
    $returnUrl = explode('/', $_POST['returnUrl']);
    $urlCount = count($returnUrl) - 1;
    
    for($i = 0; $i < $urlCount; $i++) {
        $newUrl .= $returnUrl[$i] . '/';
    }

    $newUrl .= $id;

    header('Location: ' . $newUrl);

?>