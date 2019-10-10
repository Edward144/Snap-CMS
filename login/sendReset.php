<?php

    session_start();

    require_once('../includes/database.php');

    $email = $_POST['email'];
    
    $checkEmail = $mysqli->prepare("SELECT email, first_name FROM `users` WHERE email = ?");
    $checkEmail->bind_param('s', $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if($result->num_rows > 0) {
        function randomString($length) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);

            for($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            return $randomString;
        }
        
        $token = randomString(191);
        $date = date('Y-m-d H:i:s');
        
        $removePastTokens = $mysqli->prepare("UPDATE `password_reset` SET expired = 1 WHERE email = ?");
        $removePastTokens->bind_param('s', $email);
        $removePastTokens->execute();
        $removePastTokens->close();
        
        $insertToken = $mysqli->prepare("INSERT INTO `password_reset` (email, token, date_generated, expired) VALUES (?, ?, ?, 0)");
        $insertToken->bind_param('sss', $email, $token, $date);
        $insertToken->execute();
        $insertToken->close();
        
        if(!$insertToken->error) {
            $resetLink = '//' . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'login/reset-password?token=' . $token;
            
            $to = $email;
            $subject = 'Reset your password - Snap CMS';
            $message =
                '<p>Hi ' . $result->fetch_assoc()['first_name'] . '</p>
                <p>Please use the link below to reset your password. This link will expire in 24 hours.</p>
                <br>
                <p style="word-break: break-all;"><a href="' . $resetLink . '" target="_blank">Click here to reset your password</a></p>
                <br>
                <p>--</p>
                <p>Snap CMS</p>';
            $headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . '\r\n';
            $headers .= 'MIME-Version: 1.0 \r\n';
            $headers .= 'Content-Type: text/html; charset=UTF-8';
            
            mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME']);
        }
    }

    $_SESSION['message'] = 'A reset link will be sent to the email address provided if it exists within the system.';

    header('Location: forgot-password');
    
?>