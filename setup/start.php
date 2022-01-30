<?php require_once('header.php'); ?>

    <div class="content">
        <div class="formBlock">
            <form id="setup" autocomplete="off">
                <p>
                    <label>Hostname: </label>
                    <input type="text" name="hostname" value="localhost">
                </p>
                
                <p>
                    <label>Database: </label>
                    <input type="text" name="database">
                </p>
                
                <p>
                    <label>Username: </label>
                    <input type="text" name="username">
                </p>
                
                <p>
                    <label>Password: </label>
                    <input type="password" name="password">
                </p>
                
                <p>
                    <input type="submit" value="Submit">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="setupValidation.js"></script>

<?php require_once('footer.php'); ?>