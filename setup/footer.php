    </div>

    <div class="footer">
        <div class="footerInner">
            <div class="left">
                
            </div>
            
            <div class="right">
                <p class="logout">
                    <?php if($_SESSION['loggedin'] == 1) : ?>
                        <span id="username"><?php echo ucfirst($_SESSION['username']); ?></span>
                        <a id="logout" href="/scripts/logout.php">Logout</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <script src="/scripts/functions.js"></script>
</html>