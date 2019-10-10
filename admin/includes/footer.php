            </div>
        </div>

        <footer id="adminFooter">
            <p id="currUser">Logged in as <?php echo ucwords($_SESSION['adminusername']); ?>; <a id="logout" href="<?php echo ROOT_DIR; ?>admin/scripts/logout">Logout</a></p>
        </footer>
    </body>
</html>