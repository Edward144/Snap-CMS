<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>

    <?php $tokenCheck = $mysqli->query("SELECT COUNT(*) FROM `password_reset` WHERE token = '{$_GET['token']}'")->fetch_array()[0]; ?>

    <div class="content">        
        <?php if(isset($_GET['token']) && $tokenCheck > 0) : ?>
            <?php
                $dateCheck = $mysqli->query("SELECT date_generated, expired FROM `password_reset` WHERE token = '{$_GET['token']}'")->fetch_assoc();
        
                $generatedDate = strtotime($dateCheck['date_generated']);
                $currDate = strtotime(date('Y-m-d H:i:s'));
                $difference = ($currDate - $generatedDate)/60/60/24;
        
                if($difference >= 1 && $dateCheck['expired'] == 0) {
                    $mysqli->query("UPDATE `password_reset` SET expired = 1 WHERE token = '{$_GET['token']}'");
                }
            ?>
        
            <?php $expired = $mysqli->query("SELECT expired FROM `password_reset` WHERE token = '{$_GET['token']}'")->fetch_array()[0]; ?>
        
            <?php if($expired == 0) : ?>        
                <?php 
                    $email = $mysqli->query("SELECT email FROM `password_reset` WHERE token = '{$_GET['token']}'")->fetch_array()[0]; 
        
                    $mysqli->query("UPDATE `password_reset` SET expired = 1 WHERE token = '{$_GET['token']}'");
                ?>
        
                <div class="formBlock">
                    <form id="newPassword" autocomplete="off">
                        <p>
                            <label>Email: </label>
                            <input type="text" name="email" value="<?php echo $email; ?>" disabled>
                        </p>

                        <p>
                            <label>New Password: </label>
                            <input type="password" name="password">
                        </p>

                        <p>
                            <label>Confirm Password: </label>
                            <input type="password" name="passwordConf">
                        </p>

                        <p>
                            <input type="submit" value="Reset Password">
                        </p>

                        <p class="message"></p>
                    </form>
                </div>
            <?php else : ?>
                <div class="formBlock">
                    <form id="newPassword" autocomplete="off">
                        <p>This link has expired. Please use the forgot password form again.</p>
                        <a href="/forgotPassword">Forgot Password?</a>
                    </form>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <?php header('Location: /login'); ?>
        <?php endif; ?>
    </div>

    <script src="scripts/loginValidation.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>