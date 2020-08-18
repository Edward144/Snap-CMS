<?php

    require_once('../../includes/database.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'closeRequest') {
        $_SESSION['status'] = 1;
        
        $message = '----- ' . date('d/m/y H:i') . ' ' . $_SERVER['SERVER_NAME'] . ' has closed this request. -----' . "\r\n\r\n";
        
        $close = $mysqli->prepare("UPDATE `grid_support` Set status = 'Closed', message = concat(?, message) WHERE id = ?");
        $close->bind_param('si', $message, $_POST['requestId']);
        $ex = $close->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['closemessage'] = 'Could not close request';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $request = $mysqli->prepare(
            "SELECT users.email, users.first_name, support.subject FROM `grid_support` AS support
                LEFT OUTER JOIN `grid_users` AS users ON users.id = support.user_id 
            WHERE support.id = ?"
        );
        $request->bind_param('i', $_POST['requestId']);
        $request->execute();
        $request = $request->get_result()->fetch_assoc();
        
        //Send Email to User
        $to = $request['email'];
        $subject = 'Support Request Closed: ' . $request['subject'] . ' - ' . $_SERVER['SERVER_NAME'];
        $headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8';
        $message =
            '<html>
                <head>
                    <title>Closed Support Request - ' . $_SERVER['SERVER_NAME'] . '</title>
                </head>

                <body>
                    <div style="padding: 16px; background: #f3f3f3; max-width: 1000px;">
                        <img src="https://' . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'images/logo.png" style="max-width: 300px; width: 100%; display: block; margin: auto;">

                        <p>Dear ' . $request['first_name'] . ',</p>
                        <p>Your support request has been closed by ' . $_SERVER['SERVER_NAME'] . '.</p>

                        <div style="background: #fff; padding: 16px; box-sizing: border-box;">
                            <h3>' . $request['subject'] . '</h3>
                        </div>
                    </div>
                </body>
            </html>';
        
        mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME']);
        
        $_SESSION['closemessage'] = 'Request has been closed';
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    elseif($_POST['method'] == 'replyRequest') {
        $_SESSION['status'] = 1;
        
        $message = '----- ' . date('d/m/y H:i') .': ' . $_SERVER['SERVER_NAME'] . ' -----' . "\r\n" . $_POST['reply'] . "\r\n\r\n";
        
        $update = $mysqli->prepare("UPDATE `grid_support` SET message = concat(?, message), update_date = now(), status = 'Pending User Response' WHERE id = ?");
        $update->bind_param('si', $message, $_POST['requestId']);
        $ex = $update->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['replymessage'] = 'Could not submit reply';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $request = $mysqli->prepare(
            "SELECT users.email, users.first_name, support.subject FROM `grid_support` AS support
                LEFT OUTER JOIN `grid_users` AS users ON users.id = support.user_id 
            WHERE support.id = ?"
        );
        $request->bind_param('i', $_POST['requestId']);
        $request->execute();
        $request = $request->get_result()->fetch_assoc();
        
        //Send Email to User
        $to = $request['email'];
        $subject = 'Support Request Updated: ' . $request['subject'] . ' - ' . $_SERVER['SERVER_NAME'];
        $headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8';
        $message =
            '<html>
                <head>
                    <title>Updated Support Request - ' . $_SERVER['SERVER_NAME'] . '</title>
                </head>

                <body>
                    <div style="padding: 16px; background: #f3f3f3; max-width: 1000px;">
                        <img src="https://' . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'images/logo.png" style="max-width: 300px; width: 100%; display: block; margin: auto;">

                        <p>Dear ' . $request['first_name'] . ',</p>
                        <p>Your support request has been updated by ' . $_SERVER['SERVER_NAME'] . '.</p>

                        <div style="background: #fff; padding: 16px; box-sizing: border-box;">
                            <h3>' . $request['subject'] . '</h3>
                        </div>
                    </div>
                </body>
            </html>';
        
        mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME']);
        
        $_SESSION['replymessage'] = 'Reply submitted successfully';
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    
?>