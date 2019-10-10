<?php

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');
    
    $name = rtrim(strtolower(preg_replace("![^a-z0-9]+!i", "-", $_POST['socialName'])), '-');
    $url = despace($_POST['socialUrl']);

    $addSocial = $mysqli->prepare("INSERT INTO `social_links` (name, url) VALUES(?, ?)");
    $addSocial->bind_param('ss', $name, $url);
    $ex = $addSocial->execute();

    if($ex === false) {
        $_SESSION['addmessage'] = 'Error: Could not create social media';
        
        header('Location: ../company-details');
        exit();
    }

    $_SESSION['addmessage'] = 'Added ' . ucwords(str_replace('-', ' ', $name));
    header('Location: ../company-details');
    
    exit();
    
?>