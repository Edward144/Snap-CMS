<?php

    require_once('../includes/database.php');
    
    $checkUser = $mysqli->prepare("SELECT id, username, password, access_level FROM `users` WHERE username = ? LIMIT 1");
    $checkUser->bind_param('s', $_POST['username']);
    $checkUser->execute();
    $checkUser = $checkUser->get_result();

    if($checkUser->num_rows > 0) {
        $user = $checkUser->fetch_assoc();
        
        if(password_verify($_POST['password'], $user['password'])) {
            $_SESSION['adminusername'] = $user['username'];
            $_SESSION['adminid'] = $user['id'];
            $_SESSION['adminlevel'] = $user['access_level'];
        }
        else {
            $_SESSION['message'] = 'Username or password is incorrect';
        }
    }
    else {
        $_SESSION['message'] = 'Username or password is incorrect';
    }

    header('Location: ./');

?>