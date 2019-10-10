<?php

    session_start();

    require_once('../includes/database.php');
    
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    $updatePassword = $mysqli->prepare("UPDATE `users` SET password = ? WHERE email = ?");
    $updatePassword->bind_param('ss', $password, $email);
    $updatePassword->execute();

    if(!$updatePassword->error) {
        $_SESSION['message'] = 'Your password has been updated.';
    }
    else {
        $_SESSION['message'] = 'Error: Your password could not be updated, try again later.';
    }

    header('Location: ./');

?>