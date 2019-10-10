<?php 

    session_start();

    if(isset($_SESSION['setupcomplete']) && $_SESSION['setupcomplete'] == 1) {
        header('Location: ../');
    }

?>

<!DOCTYPE html>

<html>
    <head>
        <title>Setup | Snap CMS</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="setupStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    
    <body>
        <div class="formWrap">
            <form class="blueForm" id="setupForm" method="POST" action="setup.php">
                <h1>SNAP CMS Setup</h1>
                
                <div>
                    <h3>Database Details</h3>
                    <p>Enter the details required to connect to your database.</p>
                    
                    <p>
                        <label>Hostname</label>
                        <input type="text" name="hostname" value="<?php echo (isset($_SESSION['hostname']) ? $_SESSION['hostname'] : 'localhost'); ?>">
                    </p>

                    <p>
                        <label>Database Name</label>
                        <input type="text" name="database" value="<?php echo (isset($_SESSION['database']) ? $_SESSION['database'] : ''); ?>">
                    </p>

                    <p>
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo (isset($_SESSION['username']) ? $_SESSION['username'] : ''); ?>">
                    </p>

                    <p>
                        <label>Password</label>
                        <input type="password" name="password" value="<?php echo (isset($_SESSION['password']) ? $_SESSION['password'] : ''); ?>">
                    </p>
                    
                    <h3>Website Details</h3>
                    <p>Enter the path to the folder where your website is installed, relative to the root directory. Include a leading and trailing slash.</p>
                    
                    <p>
                        <label>Document Root</label>
                        <input type="text" name="docRoot" value="<?php echo (isset($_SESSION['docRoot']) ? $_SESSION['docRoot'] : ''); ?>">
                    </p>
                    
                    <h3>Admin User Details</h3>
                    <p>These details will be used to allow you to login to you admin dashboard.</p>
                    
                    <p>
                        <label>Email Address</label>
                        <input type="text" name="adminEmail" value="<?php echo (isset($_SESSION['adminEmail']) ? $_SESSION['adminEmail'] : ''); ?>">
                    </p>

                    <p>
                        <label>Password</label>
                        <input type="password" name="adminPassword" value="<?php echo (isset($_SESSION['adminPassword']) ? $_SESSION['adminPassword'] : ''); ?>">
                    </p>

                    <p>
                        <label>Confirm Password</label>
                        <input type="password" name="adminPasswordConf" value="<?php echo (isset($_SESSION['adminPasswordConf']) ? $_SESSION['adminPasswordConf'] : ''); ?>">
                    </p>

                    <p>
                        <input type="submit" value="Submit">
                    </p>
                    
                    <?php if(isset($_SESSION['setupmessage'])) : ?>
                        <p id="message" <?php echo (isset($_SESSION['messagecolour']) ? 'style="color: ' . $_SESSION['messagecolour'] . '"' : ''); ?>><?php echo $_SESSION['setupmessage']; ?></p>
                        <?php unset($_SESSION['setupmessage']); ?>
                        <?php unset($_SESSION['setupcolour']); ?>
                    
                        <?php (isset($_SESSION['messagecolour']) && $_SESSION['messagecolour'] == 'green' ? $_SESSION['setupcomplete'] = 1 : $_SESSION['setupcomplete'] = 0); ?>
                    <?php else : ?>
                        <p id="message" style="display: none;"></p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <script>
            $("#setupForm").submit(function() {                
                var adminEmail = $("#setupForm input[name='adminEmail']").val();
                var adminPass = $("#setupForm input[name='adminPassword']").val();
                var adminPassConf = $("#setupForm input[name='adminPasswordConf']").val();
                
                if(adminEmail == "") {
                    $("#message").text("Admin email is required.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                
                if(adminEmail.indexOf("@") < 0) {
                    $("#message").text("Admin email is invalid.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                else if(adminEmail.split("@")[1].indexOf(".") < 0) {
                    $("#message").text("Admin email is invalid.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                
                if(adminPass.length < 8) {
                    $("#message").text("Admin password must be at least 8 characters.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                else if(adminPass != adminPassConf) {
                    $("#message").text("Admin password does not match.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
            });
        </script>
    </body>
</html>