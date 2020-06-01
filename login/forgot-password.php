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
        <title>Forgotten Password | Snap CMS</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="../setup/setupStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    
    <body>
        <div id="siteReturn">
            <p><a href="./">Return To CMS Login</a></p>
        </div>
        
        <div class="formWrap">
            <form class="blueForm" id="forgotForm" method="POST" action="sendReset.php">
                <h1>Reset Password</h1>

                <div>
                    <p>
                        <label>Email</label>
                        <input type="text" name="email">
                    </p>

                    <p>
                        <input type="submit" value="Send Reset Link">
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
        
        <script>            
            $("#forgotForm").submit(function() {
                var email = $("#forgotForm input[name='email']").val();
                
                if(email == "") {
                    $("#message").text("Email is required.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                
                if(email.indexOf("@") < 0) {
                    $("#message").text("Email is invalid.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                else if(email.split("@")[1].indexOf(".") < 0) {
                    $("#message").text("Email is invalid.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
            });
        </script>
    </body>
</html>