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
        <title>Forgot Password | <?php echo companyName(); ?></title>
        
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
                        <h1 class="bg-primary text-white m-0 p-3">Forgot Password</h1>
                        
                        <form class="bg-light p-3" action="sendReset.php" method="post">
                            <div class="form-group">
                                <div class="form-row m-0 align-items-center">
                                    <label for="email" class="col-sm-3">Email Address</label>
                                    <input type="email" class="form-control col-sm" name="email" required autofocus>
                                </div>
                            </div>
                            
                            <div class="form-group form-row m-0 d-flex align-items-center">
                                <input type="submit" class="btn btn-primary" value="Request Reset">
                                
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
                    var email = $(this).find("input[name='email']");
                    
                    if(email.val().split("@")[1].indexOf(".") < 0 || email.val().split("@")[1].split(".")[1].length <= 0) {
                    email.addClass("is-invalid");
                        $("<div class='invalid-feedback'>Email does not appear to include a domain extension</div>").insertAfter(email);
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