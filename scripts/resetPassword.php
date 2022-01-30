<?php

    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $pass = PASSWORD_HASH($_POST['pass'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    
    $update = $mysqli->prepare("UPDATE `users` SET password = ? WHERE email = ?");
    $update->bind_param('ss', $pass, $email);
    $update->execute();
    $update->close();

    $deleteToken = $mysqli->prepare("DELETE FROM `password_reset` WHERE email = ?");
    $deleteToken->bind_param('s', $email);
    $deleteToken->execute();
    $deleteToken->close();

    echo json_encode('Password has been reset. You can now login.');

?>