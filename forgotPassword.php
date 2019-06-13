<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>

    <div class="content">
        <div class="formBlock">
            <form id="reset" autocomplete="off">
                <p>
                    <label>Email: </label>
                    <input type="text" name="email">
                </p>
                
                <p>
                    <input type="submit" value="Send Reset Link">
                </p>
                
                <a href="/login">Return to login</a>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="scripts/loginValidation.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>