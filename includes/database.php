<?php

    session_start();

    require_once(__DIR__ . '/settings.php');

    $mysqli = new mysqli($hostname, $username, $password, $database);
    
    //If database cannot be connected to redirect to setup
    if($mysqli->connect_error) {
        $_SESSION['setupcomplete'] = 0;
        header('Location: ' . ROOT_DIR . 'setup/');
        
        die('Connection Error: ' . $mysqli->connect_error);
    }

    //If connection is successful check for completed setup
    if(!isset($_SESSION['setupcomplete']) || $_SESSION['setupcomplete'] == 0) {
        $setupStatus = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'setup complete'");

        if($setupStatus->num_rows > 0) {
            if($setupStatus->fetch_array()[0] == 0) {
                $_SESSION['setupcomplete'] = 0;
                header('Location: ' . ROOT_DIR . 'setup/');
                exit();
            }
            else {
                $_SESSION['setupcomplete'] = 1;
            }
        }
        else {
            $_SESSION['setupcomplete'] = 0;
            header('Location: ' . ROOT_DIR . 'setup/');
            exit();
        }
    }

?>