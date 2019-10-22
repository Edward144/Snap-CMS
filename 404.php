<?php http_response_code(404); ?>

<?php require_once(dirname(__FILE__) . '/includes/header.php'); ?>

<div class="content">
    <h1>404 - The page you have requested does not exist.</h1>
    
    <a href="<?php echo ROOT_DIR; ?>">Return home</a> or 
    <a href="javascript:history.back()">Go back</a>
</div>

<?php require_once(dirname(__FILE__) . '/includes/footer.php'); ?>