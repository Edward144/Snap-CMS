<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $id = $_POST['id'];

    $mysqli->query("DELETE FROM `categories` WHERE id = {$id}");
    $mysqli->query("UPDATE `categories` SET parent_id = 0, level = 0 WHERE parent_id = {$id}");

    if(!$mysqli->error) {
        echo json_encode([1, 'Category deleted successfully.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete category.']);
    }
    
?>