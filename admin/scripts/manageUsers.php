<?php

    require_once('../../includes/database.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'createUser') {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['password'] = $_POST['password'];
        
        $checkData = $mysqli->prepare("SELECT COUNT(email) FROM `users` WHERE email = ?");
        $checkData->bind_param('s', $_POST['email']);
        $checkData->execute();
        $result = $checkData->get_result()->fetch_row();
        
        if($result[0] >= 1) {
            $_SESSION['message'] = [0, 'A user with this email address already exists'];
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $checkData = $mysqli->prepare("SELECT COUNT(username) FROM `users` WHERE username = ?");
        $checkData->bind_param('s', $_POST['username']);
        $checkData->execute();
        $result = $checkData->get_result()->fetch_row();
        
        if($result[0] >= 1) {
            $_SESSION['message'] = [0, 'A user with this username already exists'];
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $insertUser = $mysqli->prepare("INSERT INTO `users` (first_name, last_name, email, username, password) VALUES(?, ?, ?, ?, ?)");
        $insertUser->bind_param('sssss', ucwords($_POST['firstName']), ucwords($_POST['lastName']), $_POST['email'], $_POST['username'], $password);
        $ex = $insertUser->execute();
        
        if($ex === false) {
            $_SESSION['message'] = [0, 'Could not create user'];
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        //Email user
        $to = $_POST['email'];
        $subject = 'You\'ve been setup as a new user - Snap CMS';
        $message =
            '<p>Hi ' . $_POST['firstName'] . ', </p>
            <p>You have been set up as a new user for the Snap CMS installation at ' . $_SERVER['SERVER_NAME'] . '.</p>
            <p>You can login <a href="//' . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'admin-login">here</a> with the username <strong>' . $_POST['username'] . '</strong> and password <strong>' . $_POST['password'] . '</strong>. It is recommended you change your password as soon as possible.</p>
            <p>--</p>
            <p>Snap CMS</p>';
        $headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8';

        mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME']);
        
        unset($_SESSION['firstName']);
        unset($_SESSION['lastName']);
        unset($_SESSION['email']);
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        
        $_SESSION['message'] = [1, 'User has been created successfully'];
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    elseif($_POST['method'] == 'editUser') {
        $formData = [];
        parse_str($_POST['formData'], $formData);
        
        if($_SESSION['adminid'] == 1 || $_SESSION['adminid'] == $formData['userId']) {
            $checkData = $mysqli->prepare("SELECT COUNT(email) FROM `users` WHERE email = ? AND id <> ?");
            $checkData->bind_param('si', $formData['email'], $formData['userId']);
            $checkData->execute();
            $result = $checkData->get_result()->fetch_row();

            if($result[0] >= 1) {
                echo json_encode([0, 'A user with this email address already exists']);
                exit();
            }

            $checkData = $mysqli->prepare("SELECT COUNT(username) FROM `users` WHERE username = ? AND id <> ?");
            $checkData->bind_param('si', $formData['username'], $formData['userId']);
            $checkData->execute();
            $result = $checkData->get_result()->fetch_row();

            if($result[0] >= 1) {
                echo json_encode([0, 'A user with this username already exists']);
                exit();
            }
            
            if($formData['password'] != '' && $formData['password'] != null && $formData['password'] == $formData['passwordConf']) {
                $password = password_hash($formData['password'], PASSWORD_BCRYPT);
                
                $updateUser = $mysqli->prepare("UPDATE `users` SET first_name = ?, last_name = ?, email = ?, username = ?, password = ? WhERE id = ?");
                $updateUser->bind_param('sssssi', ucwords($formData['firstName']), ucwords($formData['lastName']), $formData['email'], $formData['username'], $password, $formData['userId']);
            }
            else {
                $updateUser = $mysqli->prepare("UPDATE `users` SET first_name = ?, last_name = ?, email = ?, username = ? WhERE id = ?");
                $updateUser->bind_param('ssssi', ucwords($formData['firstName']), ucwords($formData['lastName']), $formData['email'], $formData['username'], $formData['userId']);
            }
            
            $ex = $updateUser->execute();
            
            if($ex === false) {
                echo json_encode([0, 'Could not update this user']);
                exit();
            }
            
            echo json_encode([1, 'User has been updated successfully']);
        }
        else {
            echo json_encode([0, 'You do not have permission to edit this user']);
        }
    }
    elseif($_POST['method'] == 'deleteUser') {
        if($_SESSION['adminid'] == 1 && $_POST['userId'] != $_SESSION['adminid']) {
            $deleteUser = $mysqli->prepare("DELETE FROM `users` WHERE id = ? AND id <> ?");
            $deleteUser->bind_param('ii', $_POST['userId'], $_SESSION['adminid']);
            $ex = $deleteUser->execute();
            
            if($ex === false) {
                echo json_encode([0, 'Could not delete this user']);
                exit();
            }
        
            echo json_encode([1, '']);
        }
        else {
            echo json_encode([0, 'You do not have permission to delete this user']);
        }
    }
    elseif($_POST['method'] == 'pullUser') {
        $pullUser = $mysqli->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
        $pullUser->bind_param('i', $_POST['userId']);
        $pullUser->execute();
        $result = $pullUser->get_result();
        
        if($result->num_rows == 1) {
            echo json_encode([1, $result->fetch_assoc()]);
        }
        else {
            echo json_encode(0);
        }
    }

?>