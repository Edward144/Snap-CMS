<?php
            $hostname = 'localhost';
            $database = 'cms1';
            $username = 'root';
            $password = '';
            
            $mysqli = new mysqli($hostname, $username, $password, $database);   
            
            if($mysqli->connect_errno) {
                echo 'Error ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error;
                
                unlink($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
                
                $_SESSION['setupcomplete'] = 0;
                
                header('Location: /setup/start.php');
                
                exit();
            }
            
            $setupComplete = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'setupcomplete'")->fetch_array()[0];
            
            if($setupComplete == 1) {
                $_SESSION['setupcomplete'] = 1;
            }
            else {
                $_SESSION['setupcomplete'] = 0;
            }
        ?>