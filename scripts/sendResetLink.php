<?php
    
    function randomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }

    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $email = $_POST['email'];

    $verify = $mysqli->prepare("SELECT email FROM `users` WHERE email = ?");
    $verify->bind_param('s', $email);
    $verify->execute();
    $results = $verify->get_result();

    if($results->num_rows > 0) {
        $token = randomString(191);
        
        $emailCheck = $mysqli->prepare("SELECT email FROM `password_reset` WHERE email = ?");
        $emailCheck->bind_param('s', $email);
        $emailCheck->execute();
        $results = $emailCheck->get_result();
        
        if($results->num_rows > 0) {
            $generateToken = $mysqli->prepare("UPDATE `password_reset` SET token = ?, date_generated = NOW() WHERE email = ?");
            $generateToken->bind_param('ss', $token, $email);
            $generateToken->execute();
            $generateToken->close();
        }
        else {
            $generateToken = $mysqli->prepare("INSERT INTO `password_reset` (email, token) VALUES (?, ?)");
            $generateToken->bind_param('ss', $email, $token);
            $generateToken->execute();
            $generateToken->close();
        }
        
        $link = 'http://' . $_SERVER['SERVER_NAME'] . '/reset?token=' . $token;
        
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        
        $from = "no-reply@" . gethostbyaddr('127.0.0.1');
        $to = $email;
        $subject = "Password Reset";
        $message = 
            "<h2>Use this link to reset your password:</h2>
            <a href='" . $link . "'>" . $link . "</a>
            ";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "From: noreply@" . gethostbyaddr('127.0.0.1') . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        if(mail($to, $subject, $message, $headers)) {
            echo json_encode('A reset link has been sent to your email.');
        }
        else {
            echo json_encode('Error: Reset link could not be sent.');
        }
    }
    else {
        echo json_encode('Email does not exist.');
    }
    
    $mysqli->close();

?>