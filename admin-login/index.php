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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login | <?php echo companyName(); ?></title>
        
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
                        <h1 class="bg-primary text-white m-0 p-3">Admin Login</h1>
                        
                        <form class="bg-light p-3" action="doLogin.php" method="post">
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="username" class="col-sm-3">Username</label>
                                    <input type="text" class="form-control col-sm" name="username" required autofocus>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="password" class="col-sm-3">Password</label>
                                    <input type="password" class="form-control col-sm" name="password" required>
                                </div>
                            </div>
                            
                            <div class="form-group form-row m-0 d-flex align-items-center">
                                <input type="submit" class="btn btn-primary" value="Log In">
                                
                                <small class="ml-auto"><a href="forgot-password">Forgot Password?</a></small>
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
                    $(this).find(":submit").prop("disabled", true);
                    $("<div class='spinner-border ml-1'><span class='sr-only'>Logging you in...</span></div>").insertAfter($(this).find(":submit"));
                });
            </script>
        </div>
    </body>
</html>