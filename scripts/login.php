<?php
    
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $verify = $mysqli->prepare("SELECT username, password, access_level FROM `users` WHERE username = ?");
    $verify->bind_param('s', $user);
    $verify->execute();
    $results = $verify->get_result();

    if($results->num_rows > 0) {
        $details = $results->fetch_assoc();
        
        if(password_verify($pass, $details['password'])) {
            echo json_encode([1, 'Login successful.']);
            
            $_SESSION['loggedin'] = 1;
            $_SESSION['username'] = $details['username'];
            $_SESSION['accesslevel'] = $details['access_level'];
        }
        else {
            echo json_encode([0, 'Username or Password is incorrect.']);
        }
    }
    else {
        echo json_encode([0, 'Username or Password is incorrect.']);
    }
    
    $mysqli->close();

?>