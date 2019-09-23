<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $name = $_POST['name'];
    $names = $_POST['name'] . 's';

    $checkExisting = $mysqli->prepare("SELECT COUNT(*) FROM `custom_posts` WHERE name = ?");
    $checkExisting->bind_param('s', $name);
    $checkExisting->execute();
    $checkResult = $checkExisting->get_result();

    if($checkResult->fetch_array()[0] > 0) {
        //Delete From custom_posts
        $deletePost = $mysqli->prepare("DELETE FROM `custom_posts` WHERE name = ?");
        $deletePost->bind_param('s', $name);
        $deletePost->execute();
        
        //Delete Custom Tables
        $mysqli->query("DROP TABLE `{$name}`");
        $mysqli->query("DROP TABLE `{$name}_options`");
        
        echo json_encode([1, $name . ' has been deleted.']);
    }
    else {
        echo json_encode([0, 'Error: ' . $name . ' does not exist.']);
    }

?>