<?php

    session_start();

    require_once('../includes/database.php');
    
    $user = $_POST['username'];
    $password = $_POST['password'];

    $_SESSION['username'] = $user;
    
    if($user == null || $user == '') {
        $_SESSION['message'] = 'Username is missing.';
        
        header('Location: ./');
        
        exit();
    }
    elseif($password == null || $password == '') {
        $_SESSION['message'] = 'Password is missing.';
        
        header('Location: ./');
        
        exit();
    }

    $checkDetails = $mysqli->prepare("SELECT * FROM `users` WHERE username = ?");
    $checkDetails->bind_param('s', $user);
    $checkDetails->execute();
    $result = $checkDetails->get_result();
    
    if($result->num_rows > 0) {
        $result = $result->fetch_assoc();
        
        if(password_verify($password, $result['password'])) {
            $_SESSION['adminusername'] = $user;
            $_SESSION['adminid'] = $result['id'];
            
            unset($_SESSION['username']);
            unset($_SESSION['password']);
            
            header('Location: ../admin/');

            exit();
        }
        else {
            $_SESSION['message'] = 'Username or password is incorrect.';

            header('Location: ./');

            exit();
        }
    }
    else {
        $_SESSION['message'] = 'Username or password is incorrect.';

        header('Location: ./');
        
        exit();
    }
    
?>