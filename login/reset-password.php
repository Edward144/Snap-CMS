<?php
    require_once('../includes/database.php');
    require_once('../includes/functions.php');

    //Redirect if no token is set or token is incorrect
    if(!isset($_GET['token'])) {
        header('Location: ./');
        
        exit();
    }

    $checkToken = $mysqli->prepare("SELECT token, date_generated, email FROM `password_reset` WHERE token = ? AND expired = 0");
    $checkToken->bind_param('s', $_GET['token']);
    $checkToken->execute();
    $result = $checkToken->get_result();

    if($result->num_rows <= 0) {
        header('Location: ./');
        
        exit();
    }
    else {        
        //Expire token so page will redirect next time it is used
        $expireToken = $mysqli->prepare("UPDATE `password_reset` SET expired = 1 WHERE token = ?");
        $expireToken->bind_param('s', $_GET['token']);
        $expireToken->execute();
        $expireToken->close();
        
        $tokenDetails = $result->fetch_assoc();
        
        //Redirect if 24 hours have passed
        $generatedDate = strtotime($tokenDetails['date_generated']);
        $currDate = strtotime(date('Y-m-d H:i:s'));
        $difference = ($currDate - $generatedDate)/60/60/24;
        
        if($difference >= 1) {
            header('Location: ./');
            
            exit();
        }
    }

    $email = $tokenDetails['email'];
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Reset Password | Snap CMS</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="../setup/setupStyles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    
    <body>
        <div id="siteReturn">
            <p><a href="../">Return To CMS Login</a></p>
        </div>
        
        <div class="formWrap">
            <form class="blueForm" id="resetForm" method="POST" action="doReset.php">
                <h1>Update Password</h1>

                <div>
                    <input type="hidden" name="email" value="<?php echo $email; ?>">

                    <p>
                        <label>New Password</label>
                        <input type="password" name="password">
                    </p>
                    
                    <p>
                        <label>Confirm Password</label>
                        <input type="password" name="passwordConfirmation">
                    </p>

                    <p>
                        <input type="submit" value="Submit">
                    </p>
                    
                    <p id="message" style="display: none;"></p>
                </div>
            </form>
        </div>
        
        <script>
            $("#resetForm").submit(function() {
                var password = $("#resetForm input[name='password']").val();
                var passwordConf = $("#resetForm input[name='passwordConfirmation']").val();
                
                if(password.length < 8) {
                    $("#message").text("Password must be at least 8 characters.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
                else if(password != passwordConf) {
                    $("#message").text("Passwords do not match.");
                    $("#message").css("display", "block");
                    
                    event.preventDefault();
                    return;
                }
            });
        </script>
    </body>
</html>