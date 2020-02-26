<?php
    
    session_start();

    $hostname = $_POST['hostname'];
    $database = $_POST['database'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $_SESSION['hostname'] = $hostname;
    $_SESSION['database'] = $database;
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    
    $docRoot = $_POST['docRoot'];

    $_SESSION['docRoot'] = $docRoot;

    $adminEmail = $_POST['adminEmail'];
    $adminPassword = $_POST['adminPassword'];
    $adminPasswordConf = $_POST['adminPasswordConf'];

    $_SESSION['adminEmail'] = $adminEmail;
    $_SESSION['adminPassword'] = $adminPassword;
    $_SESSION['adminPasswordConf'] = $adminPasswordConf;

    if($hostname == null || $hostname == '') {
        $_SESSION['setupmessage'] = 'Hostname is missing.';
    }
    elseif($database == null || $database == '') {
        $_SESSION['setupmessage'] = 'Database name is missing.';
    }
    elseif($username == null || $username == '') {
        $_SESSION['setupmessage'] = 'Database username is missing.';
    }

    if(isset($_SESSION['setupmessage'])) {
        header('Location: index');
            
        exit();
    }
    
    //Connect to the database
    $mysqli = new mysqli($hostname, $username, $password, $database);

    if($mysqli->connect_error) {
        $_SESSION['setupmessage'] = 'Error: Could not connect to database, check your details are correct.';
        
        header('Location: index');
            
        exit();
    }

    //Check admin details are correct
    if($adminEmail == null || $adminEmail == '') {
        $_SESSION['setupmessage'] = 'Admin email is missing.';
    }
    elseif($adminPassword == null || $adminPassword == '') {
        $_SESSION['setupmessage'] = 'Admin password name is missing.';
    }
    elseif($adminPassword != $adminPasswordConf) {
        $_SESSION['setupmessage'] = 'Admin password does not match.';
    }

    if(isset($_SESSION['setupmessage'])) {
        header('Location: index');
            
        exit();
    }
    
    $adminPassword = password_hash($adminPassword, PASSWORD_BCRYPT);
    
    //Create the required tables
    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `users` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            username VARCHAR(100) UNIQUE,
            password VARCHAR(60),
            email VARCHAR(191) UNIQUE
        )"
    );

    $mysqli->query("TRUNCATE TABLE `users`");

    $adminUser = $mysqli->prepare(
        "INSERT IGNORE INTO `users` (
            first_name,
            last_name,
            username,
            password,
            email
        ) VALUES (
            'Admin',
            'User',
            'admin',
            ?,
            ?
        )"
    );
    $adminUser->bind_param('ss', $adminPassword, $adminEmail);
    $adminUser->execute();
    $adminUser->close();

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `password_reset` (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(191),
            token VARCHAR(191) UNIQUE,
            date_generated DATETIME DEFAULT CURRENT_TIMESTAMP(),
            expired TINYINT(4) DEFAULT 0
        )"
    );

    $mysqli->query("TRUNCATE TABLE `password_reset`");

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `company_info` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200),
            address_1 VARCHAR(255),
            address_2 VARCHAR(255),
            address_3 VARCHAR(255),
            address_4 VARCHAR(255),
            postcode VARCHAR(50),
            county VARCHAR(100),
            country VARCHAR(100),
            phone VARCHAR(50),
            email VARCHAR(100),
            fax VARCHAR(50),
            vat_number VARCHAR(20),
            registration_number VARCHAR(20),
            logo VARCHAR(255)
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `social_links` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE,
            url VARCHAR(255)
        )"
    );

    $mysqli->query(
        "INSERT IGNORE INTO `social_links` (
            name
        ) VALUES (
            'facebook'
        ), (
            'twitter'
        ), (
            'linkedin'
        ), (
            'instagram'
        ), (
            'youtube'
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `posts` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_type_id INT,
            name VARCHAR(255),
            short_description VARCHAR(500),
            content TEXT,
            url VARCHAR(191) UNIQUE,
            gallery JSON DEFAULT NULL,
            specifications JSON DEFAULT NULL,
            category_id INT,
            author VARCHAR(100),
            date_posted DATETIME DEFAULT CURRENT_TIMESTAMP(),
            last_edited DATETIME DEFAULT CURRENT_TIMESTAMP(),
            last_edited_by INT,
            visible TINYINT(4),
            custom_content VARCHAR(255)
        )"
    );

    $mysqli->query("CREATE TABLE `post_history` LIKE `posts`");
    $mysqli->query("ALTER TABLE `post_history` ADD COLUMN post_id INT AFTER id");
    $mysqli->query("ALTER TABLE `post_history` DROP INDEX url");

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `post_types` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE,
            title VARCHAR(255),
            content TEXT,
            image_url VARCHAR(500),
            has_options TINYINT(4) DEFAULT 0
        )"
    );

    $mysqli->query(
        "INSERT IGNORE INTO `post_types` (
            name
        ) VALUES (
            'posts'
        ), (
            'pages'
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `categories` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_type_id INT,
            name VARCHAR(100),
            description VARCHAR(500),
            image_url VARCHAR(255),
            parent_id INT,
            level INT
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `navigation_menus` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(191) UNIQUE
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `navigation_structure` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            menu_id INT,
            parent_id INT,
            position INT,
            name VARCHAR(255),
            url VARCHAR(191) UNIQUE,
            image_url VARCHAR(255),
            level INT DEFAULT 0
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `sliders` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_type_id INT DEFAULT 0,
            post_id INT DEFAULT 0,
            name VARCHAR(191) UNIQUE,
            animation_in VARCHAR(50) DEFAULT 'flipInX',
            animation_out VARCHAR(50) DEFAULT 'slideOutDown',
            speed INT DEFAULT 5000,
            visible TINYINT(4) DEFAULT 0
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `slider_items` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slider_id INT,
            position INT DEFaULT 0,
            image_url VARCHAR(255),
            content TEXT
        )"
    );

    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS `settings` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            settings_name VARCHAR(100) UNIQUE,
            settings_value VARCHAR(100)
        )"
    );

    $mysqli->query("TRUNCATE TABLE `settings`");

    $mysqli->query(
        "INSERT IGNORE INTO `settings` (
            settings_name,
            settings_value
        ) VALUES (
            'setup complete',
            '1'
        ), (
            'homepage',
            '0'
        ), (
            'hide posts',
            '0'
        ), (
            'google analytics',
            ''
        )"
    );
    
    //Email admin user
    $to = $adminEmail;
    $subject = 'You\'ve been setup as a new user - Snap CMS';
    $message =
        '<p>Hi, </p>
        <p>You have been set up as a new user for Snap CMS at ' . $_SERVER['SERVER_NAME'] . '.</p>
        <p>You can login <a href="//' . $_SERVER['SERVER_NAME'] . $docRoot . 'login">here</a> with the username <strong>admin</strong> and your chosen password.</p>
        <br>
        <p>--</p>
        <p>Snap CMS</p>';
    $headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8';

    mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME']);

    //Create the connection file to be used by the rest of the site
    if(!is_writable('../includes/settings.php')) {
        $_SESSION['setupmessage'] = 'Error cannot write settings file in includes/settings.php';
        $_SESSION['messagecolour'] = 'red';
        
        header('Location: index');
        exit();    
    }

    $settings = fopen('../includes/settings.php', 'w');

    fwrite($settings, 
        '<?php
            $hostname = \'' . $hostname . '\';
            $database = \'' . $database . '\';
            $username = \'' . $username . '\';
            $password = \'' . $password . '\';
            
            define(\'ROOT_DIR\', \'' . $docRoot . '\');
        ?>'
    );

    fclose($settings);
    
    $_SESSION['setupmessage'] = 'Setup has completed successfully, you can now access the <a href="../login">admin dashboard</a> with the username <span style="color: #ff0000;">admin</span> and your chosen password.';
    $_SESSION['messagecolour'] = 'green';

    $mysqli->close();

    header('Location: index');

?>