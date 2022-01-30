<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>

    <div class="content">
        <div class="formBlock">
            <form id="login" autocomplete="off">
                <p>
                    <label>Username: </label>
                    <input type="text" name="username">
                </p>
                
                <p>
                    <label>Password: </label>
                    <input type="password" name="password">
                </p>
                
                <p>
                    <input type="submit" value="Login">
                </p>
                
                <a href="/forgotPassword">Forgot Password?</a>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="scripts/loginValidation.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>