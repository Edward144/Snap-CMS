<?php

    include_once($_SERVER['DOCUMENT_ROOT'] . '/setup/setupArrays.php');
        
    $adminPass = 'changeme';
    $hashedPass = password_hash($adminPass, PASSWORD_BCRYPT);

    $host = $_POST['host'];
    $database = $_POST['database'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $mysqli = new mysqli($host, $user, $pass);
    
    if($mysqli->connect_errno) {
        echo json_encode('Error ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error);
        
        exit();
    }
    
    //Create Database
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$database}`");
    
    $mysqli->close();

    $mysqli = new $mysqli($host, $user, $pass, $database);

    if($mysqli->connect_errno) {
        echo json_encode('Error ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error);
        
        exit();
    }

    //Create Tables
    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `settings` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_name VARCHAR(191) UNIQUE,
            setting_value VARCHAR(191)
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `users` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            username VARCHAR(100) UNIQUE,
            password VARCHAR(60),
            email VARCHAR(200),
            access_level TINYINT
        )"
    );

    $createAdmin = $mysqli->prepare(
        "INSERT IGNORE INTO `users` (
            first_name, 
            last_name, 
            username,
            password,
            access_level
        ) VALUES (
            'Admin',
            'User',
            'admin',
            ?,
            100
        )"
    );
    
    $createAdmin->bind_param('s', $hashedPass);
    $createAdmin->execute();
    $createAdmin->close();

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `company_info` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(255),
            address_1 VARCHAR(100),
            address_2 VARCHAR(100),
            address_3 VARCHAR(100),
            address_4 VARCHAR(100),
            postcode VARCHAR(20),
            country VARCHAR(20),
            phone VARCHAR(50),
            fax VARCHAR(50),
            email VARCHAR(100),
            vat_number VARCHAR(20),
            reg_number VARCHAR(20),
            logo VARCHAR(100)
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `countries` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            iso_code VARCHAR(20) UNIQUE,
            country VARCHAR(200)
        )"
    );

    if(isset($countries)) {
        $addCountry = $mysqli->prepare(
            "INSERT IGNORE INTO `countries` (
                iso_code, 
                country
            ) VALUES (
                ?,
                ?
            )"
        );
        
        foreach($countries as $country => $code) {
            $addCountry->bind_param('ss', $code, $country);
            $addCountry->execute();
        }
        
        $addCountry->close();
    }

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `navigation` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_id INT,
            parent_id INT DEFAULT 0,
            position INT,
            level INT DEFAULT 0,
            custom_id INT,
            nav_name VARCHAR(255) DEFAULT null,
            custom_url VARCHAR(255) DEFAULT null
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `posts` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            description VARCHAR(255),
            url VARCHAR(191) UNIQUE,
            author VARCHAR(200),
            date_posted DATETIME,
            visible TINYINT(1),
            content TEXT,
            category_id INT DEFAULT 0,
            image_url VARCHAR(255)
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `pages` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            description VARCHAR(255),
            url VARCHAR(191) UNIQUE,
            author VARCHAR(200),
            date_posted DATETIME,
            visible TINYINT(1),
            content TEXT,
            image_url VARCHAR(255)
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `categories` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200),
            description VARCHAR(255),
            image_url VARCHAR(255),
            parent_id INT DEFAULT 0,
            position INT DEFAULT 0,
            level INT DEFAULT 0,
            custom_id INT DEFAULT 1
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `password_reset` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255),
            token VARCHAR(191) UNIQUE,
            date_generated DATETIME DEFAULT CURRENT_TIMESTAMP,
            expired TINYINT DEFAULT 0
        )"
    );

    //CREATE Database Connection File
    $dbConn = fOpen($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php', 'w');

    fWrite($dbConn, 
        '<?php
            $hostname = \'' . $host .'\';
            $database = \'' . $database . '\';
            $username = \'' . $user . '\';
            $password = \'' . $pass . '\';
            
            $mysqli = new mysqli($hostname, $username, $password, $database);   
            
            if($mysqli->connect_errno) {
                echo \'Error \' . $mysqli->connect_errno . \': \' . $mysqli->connect_error;
                
                unlink($_SERVER[\'DOCUMENT_ROOT\'] . \'/templates/database_connect.php\');
                
                $_SESSION[\'setupcomplete\'] = 0;
                
                header(\'Location: /setup/start.php\');
                
                exit();
            }
            
            $setupComplete = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = \'setupcomplete\'")->fetch_array()[0];
            
            if($setupComplete == 1) {
                $_SESSION[\'setupcomplete\'] = 1;
            }
            else {
                $_SESSION[\'setupcomplete\'] = 0;
            }
        ?>'
    );

    fClose($dbConn);

    //Create Default Settings
    $mysqli->query("INSERT IGNORE INTO `settings` (setting_name, setting_value) VALUE('setupcomplete', '1')");

    $mysqli->query("INSERT IGNORE INTO `settings` (setting_name, setting_value) VALUE('homepage', '')");

    $mysqli->query("INSERT IGNORE INTO `settings` (setting_name, setting_value) VALUE('hide_posts', '0')");

    //write Output
    if(!$mysqli->error) {
        echo json_encode('Setup is complete.<br>An admin user has been created with the username "Admin" and the password "' . $adminPass . '".<br>You can now <a href="/login">Login</a>.');
    }
    else {
        echo json_encode('Error ' . $mysqli->errno . ': ' . $mysqli->error);
    }

    $mysqli->close();

?>
