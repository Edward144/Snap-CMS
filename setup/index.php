<?php 

    session_start();

    if(isset($_SESSION['setupcomplete']) && $_SESSION['setupcomplete'] == 1) {
        header('Location: ../');
    }

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup | Snap CMS</title>
        
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/adminStyle.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-xxzQGERXS00kBmZW/6qxqJPyxW3UR0BPsL4c8ILaIWXva5kFi7TxkIIaMiKtqV1Q" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="../js/bootstrap.min.js"></script>
    </head>
    
    <body>
        <div class="wrapper">
            <div class="container-fluid overflow-auto">
                <div class="row d-flex h-100 align-items-center; justify-content-center">
                    <div class="col p-4 py-lg-5" style="max-width: 768px;">
                        <h1 class="bg-primary text-white m-0 p-3">Setup Snap CMS</h1>
                        
                        <form class="bg-light p-3" action="setup.php" method="post">
                            <?php if(!isset($_SESSION['success']) && $_SESSION['success'] != 1) : ?>
                                <h4>Database Details</h4>
                                <p>Create a new database within your MySQL installation and enter the relavant details below. 
                                All required tables will be created automatically.</p>

                                <div class="form-group">
                                    <label for="hostname">Hostname</label>
                                    <input type="text" class="form-control" name="hostname" placeholder="localhost" value="<?php echo (isset($_SESSION['hostname']) ? $_SESSION['hostname'] : 'localhost'); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="database">Database Name</label>
                                    <input type="text" class="form-control" name="database" value="<?php echo (isset($_SESSION['database']) ? $_SESSION['database'] : ''); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" value="<?php echo (isset($_SESSION['username']) ? $_SESSION['username'] : ''); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>

                                <h4>Website Details</h4>
                                <p>If your website is installed in a directory other than root enter the path to the subdirectory here.
                                This will ensure all links and other functionality work correctly. <br><small>You may also need to adjust the .htaccess file accordingly.</small></p>

                                <div class="form-group">
                                    <label for="rootDirectory">Root Directory</label>
                                    <input type="text" class="form-control" name="rootDirectory" placeholder="/path/to/subdirectory/" value="<?php echo (isset($_SESSION['rootDirectory']) ? $_SESSION['rootDirectory'] : '/'); ?>" required>
                                    <small class="form-text text-muted">Include leading and trailing slashes</small>
                                </div>

                                <h4>Admin User Details</h4>
                                <p>Enter details for the main admin user for Snap CMS. This user is required and cannot be deleted.</p>

                                <div class="form-group">
                                    <label for="adminEmail">Email Address</label>
                                    <input type="email" class="form-control" name="adminEmail" value="<?php echo (isset($_SESSION['adminEmail']) ? $_SESSION['adminEmail'] : ''); ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="adminPassword">Password</label>
                                            <input type="password" class="form-control" name="adminPassword" required>
                                        </div>
                                    </div>

                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="adminPasswordConf">Confirm Password</label>
                                            <input type="password" class="form-control" name="adminPasswordConf" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group d-flex align-items-center">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                            <?php else : ?>
                                <h2>Setup Successful</h2>
                            <?php endif; ?>
                            
                            <?php if(isset($_SESSION['setupmessage'])) : ?>
                                <div class="text-danger"><?php echo $_SESSION['setupmessage']; ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $("form").submit(function() {                
                $(this).find(".is-invalid").removeClass("is-invalid");
                $(this).find(".invalid-feedback").remove();
                
                var valid = true;
                var email = $(this).find("input[name='adminEmail']");
                var password = $(this).find("input[name='adminPassword']");
                var passwordConf = $(this).find("input[name='adminPasswordConf']");
                
                //Validate email
                if(email.val().split("@")[1].indexOf(".") < 0 || email.val().split("@")[1].split(".")[1].length <= 0) {
                    email.addClass("is-invalid");
                    $("<div class='invalid-feedback'>Email does not appear to include a domain extension</div>").insertAfter(email);
                    valid = false;
                }
                
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
                    
                    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
                }
                else {
                    event.preventDefault();
                }
            });
        </script>
    </body>
</html>

<?php session_destroy(); ?>