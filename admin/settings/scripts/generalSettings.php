<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $homepage = $_POST['homepage'];
    $hidePosts = $_POST['hidePosts'];
    
    if($hidePosts == 0) {
        $hidePosts = '';
    }

    $updateHome = $mysqli->prepare("UPDATE `settings` SET setting_value = ? WHERE setting_name = 'homepage'");
    $updateHome->bind_param('s', $homepage);
    $updateHome->execute();
    $updateHome->close();
    
    $updatePosts = $mysqli->prepare("UPDATE `settings` SET setting_value = ? WHERE setting_name = 'hide_posts'");
    $updatePosts->bind_param('s', $hidePosts);
    $updatePosts->execute();
    $updatePosts->close();
    
    if(!$mysqli->error) {
        echo json_encode('Settings updated successfully.');
    }
    else {
        echo json_encode('Error: Could not update settings.');
    }
    
?>