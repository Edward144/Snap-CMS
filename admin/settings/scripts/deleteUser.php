<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    

    $username = $_POST['username'];

    $deleteUser = $mysqli->prepare("DELETE FROM `users` WHERE username = ?");
    $deleteUser->bind_param('s', $username);
    $deleteUser->execute();
    $deleteUser->close();

    if(!$mysqli->error) {
        echo json_encode([1, $username . ' has been deleted.']);
    }
    else {
        echo json_encode([0, 'Error: Could not delete ' . $username . '.']);
    }

?>