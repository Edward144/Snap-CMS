<?php
    require_once('../includes/database.php');
    require_once('../includes/functions.php');

    //Redirect to admin if user is logged in
    if(isset($_SESSION['adminusername'])) {
        header('Location: ../admin/');
        
        exit();
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Admin Login | Snap CMS</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="../setup/setupStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    
    <body>
        <div id="siteReturn">
            <p><a href="../">Return To Website</a></p>
        </div>
        
        <div class="formWrap">
            <form class="blueForm" id="loginForm" method="POST" action="doLogin.php">
                <h1>CMS Login</h1>

                <div>
                    <p>
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo (isset($_SESSION['username']) ? $_SESSION['username'] : ''); ?>" autofocus>
                    </p>

                    <p>
                        <label>Password</label>
                        <input type="password" name="password">
                    </p>

                    <p>
                        <input type="submit" value="Login">
                        <span style="float: right; margin: 1em auto;"><a href="forgot-password">Forgotten Password?</a></span>
                    </p>
                    
                    <?php if(isset($_SESSION['message'])) : ?>
                        <p id="message"><?php echo $_SESSION['message']; ?></p>
                        <?php unset($_SESSION['message']); ?>
                    <?php else : ?>
                        <p id="message" style="display: none;"></p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </body>
</html>