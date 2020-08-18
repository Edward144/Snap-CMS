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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password | <?php echo companyName(); ?></title>
        
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/adminStyle.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-xxzQGERXS00kBmZW/6qxqJPyxW3UR0BPsL4c8ILaIWXva5kFi7TxkIIaMiKtqV1Q" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="../js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <div class="wrapper d-block">
            <div class="bg-primary p-3">
                <span><a href="../" class="text-white">Return to site</a></span>
            </div>
            
            <div class="container-fluid overflow-auto">                
                <div class="row d-flex h-100 align-items-center; justify-content-center">
                    <div class="col p-4 py-lg-5" style="max-width: 768px;">
                        <h1 class="bg-primary text-white m-0 p-3">Reset Your Password</h1>
                        
                        <form class="bg-light p-3" action="doReset.php" method="post">
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="email" class="col-sm-3">Your Email</label>
                                    <input type="text" class="form-control col-sm" name="email" value="<?php echo $email; ?>" disabled>
                                    <input type="hidden"name="email" value="<?php echo $email; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="password" class="col-sm-3">New Password</label>
                                    <input type="password" class="form-control col-sm" name="password" required autofocus>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="passwordConfirmation" class="col-sm-3">Confirm Password</label>
                                    <input type="password" class="form-control col-sm" name="passwordConfirmation" required>
                                </div>
                            </div>
                            
                            <div class="form-group form-row m-0 d-flex align-items-center">
                                <input type="submit" class="btn btn-primary" value="Reset Password">
                                
                                <small class="ml-auto"><a href="./">Return to login</a></small>
                            </div>
                            
                            <?php if(isset($_SESSION['message'])) : ?>
                                <div class="text-danger"><?php echo $_SESSION['message']; ?></div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
                $("form").submit(function() {
                    $(this).find(".is-invalid").removeClass("is-invalid");
                    $(this).find(".invalid-feedback").remove();
                    
                    var valid = true;
                    var password = $(this).find("input[name='password']");
                    var passwordConf = $(this).find("input[name='passwordConfirmation']");
                    
                    //Validate password
                    if(password.val().length < 8) {
                        password.addClass("is-invalid");
                        $("<div class='invalid-feedback'>Password must be at least 8 characters</div>").insertAfter(password);
                        valid = false;
                    }
                    else if(!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/.test(password.val())) {
                        password.addClass("is-invalid");
                        $("<div class='invalid-feedback'>Password must contain at least one upper, one lower, and one digit</div>").insertAfter(password);
                        valid = false;
                    }
                    else if(password.val() != passwordConf.val()) {
                        passwordConf.addClass("is-invalid");
                        $("<div class='invalid-feedback'>Passwords do not match</div>").insertAfter(passwordConf);
                        valid = false;
                    }
                    
                    if(valid == true) {
                        $(this).find(":submit").prop("disabled", true);
                        $("<div class='spinner-border ml-1'><span class='sr-only'>Logging you in...</span></div>").insertAfter($(this).find(":submit"));
                    }
                    else {
                        event.preventDefault(); 
                    }
                });
            </script>
        </div>
    </body>
</html>