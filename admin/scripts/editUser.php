<?php 

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    $action = $_POST['action'];

    if(isset($_POST['id'])) {
        $userId = $_POST['id'];
    }

    if(isset($_POST['oUser'])) {
        $oUser = $_POST['oUser'];
    }

    if($action === '0' && $oUser != 'admin') {
        //Delete User
        $delete = $mysqli->prepare("DELETE FROM `users` WHERE id = ? and username != 'admin'");
        $delete->bind_param('i', $userId);
        $delete->execute();
        
        if(!$delete->error) {
            echo json_encode([1, $oUser . ' has been deleted']);
        }
        else {
            echo json_encode([0, 'Error: ' . $oUser . ' could not be deleted']);
        }
    }
    elseif($action === '1') {
        //Update User
        $username = despace($_POST['username']);
        $fName = $_POST['firstName'];
        $lName = $_POST['lastName'];
        $email = despace($_POST['email']);
        $password = despace($_POST['password']);
        $passwordConf = despace($_POST['passwordConf']);
        
        $updateUser = $mysqli->prepare("UPDATE `users` SET first_name = ?, last_name = ?, username = ?, email = ? WHERE id = ?");
        $updateUser->bind_param('ssssi', $fName, $lName, $username, $email, $userId);
        $ex = $updateUser->execute();
        
        if($ex === false) {
            echo json_encode('Error: ' . $oUser . ' could not be updated');
            exit();
        }
        
        $updateUser->close();
        
        if(strlen($password) >= 8 && $password == $passwordConf) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            $updatePass = $mysqli->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $updatePass->bind_param('si', $password, $userId);
            $ex = $updatePass->execute();
            
            if($ex === false) {
                echo json_encode('Error: ' . $oUser . '\'s password could not be updated');
                exit();
            }
            
            $updatePass->close();
        }
        
        echo json_encode($oUser . ' has been updated');
    }
    elseif($action === '2') {
        //Create New User
        $username = despace($_POST['username']);
        $fName = $_POST['firstName'];
        $lName = $_POST['lastName'];
        $email = despace($_POST['email']);
        $password = despace($_POST['password']);
        $passwordConf = despace($_POST['passwordConf']);
        
        if(strlen($password) >= 8 && $password == $passwordConf) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            $createUser = $mysqli->prepare("INSERT INTO `users` (first_name, last_name, username, email, password) VALUES(?, ?, ?, ?, ?)");
            $createUser->bind_param('sssss', $fName, $lName, $username, $email, $password);
            $ex = $createUser->execute();

            if($ex === false) {
                echo json_encode([0, 'Error: ' . $mysqli->error]);
                exit();
            }

            $createUser->close();
            
            echo json_encode([1, $username . ' has been created']);
        }
        else {
            echo json_encode([0, 'Error: Could not create user, password is invalid']);
        }
    }
    else {
        //Throw Error
        echo json_encode('Error: Unknown Action');
    }

?>