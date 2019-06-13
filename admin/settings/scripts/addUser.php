<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $access = $_POST['access'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $addUser = $mysqli->prepare("INSERT INTO `users` (username, email, first_name, last_name, access_level, password) VALUES(?, ?, ?, ?, ?, ?)");
    $addUser->bind_param('ssssis', $username, $email, $firstName, $lastName, $access, $password);
    $addUser->execute();
    $addUser->close();

    if(!$mysqli->error) {
        echo json_encode([1, $username . ' has been created.']);
    }
    else {
        echo json_encode([0, 'Error: User could not be created.']);
    }

?>