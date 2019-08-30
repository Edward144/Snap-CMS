<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $id = $_POST['id'];

    $mysqli->query("DELETE FROM `categories` WHERE id = {$id}");

    if(!$mysqli->error) {
        echo json_encode([1, 'Category deleted successfully.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete category.']);
    }
    
?>