<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $access = $_POST['access'];
    $cPass = $_POST['cPass'];
    $nPass = password_hash($_POST['nPass'], PASSWORD_BCRYPT);
    
    if($cPass == "") {
        $updateUser = $mysqli->prepare("UPDATE `users` SET email = ?, first_name = ?, last_name = ?, access_level = ? WHERE username = ?");
        $updateUser->bind_param('sssis', $email, $firstName, $lastName, $access, $username);
        $updateUser->execute();
        $updateUser->close();
    }
    else {
        $checkPass = $mysqli->prepare("SELECT password FROM `users` WHERE username = ?");
        $checkPass->bind_Param('s', $username);
        $checkPass->execute();
        $password = $checkPass->get_result()->fetch_array()[0];
        
        if(!password_verify($cPass, $password)) {
            echo json_encode('Error: Current password for ' . $username . ' is incorrect.');

            exit();
        }
        
        $updateUser = $mysqli->prepare("UPDATE `users` SET email = ?, first_name = ?, last_name = ?, access_level = ?, password = ? WHERE username = ?");
        $updateUser->bind_param('sssiss', $email, $firstName, $lastName, $access, $nPass, $username);
        $updateUser->execute();
        $updateUser->close();
    }   
    
    if(!$mysqli->error) {
        echo json_encode('Details updated successfullly for ' . $username . '.');
    }
    else {
        echo json_encode('Error: Could not update details for ' . $username . '.');   
    }

?>