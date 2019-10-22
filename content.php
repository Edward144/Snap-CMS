<?php require_once('includes/header.php'); ?>

<div class="content">
    <?php echo 'type: ' . $_GET['post-type'] . '<br>'; ?>
    <?php echo 'url: ' . $_GET['url'] . '<br>'; ?>
    <?php echo 'category: ' . $_GET['category'] . '<br>'; ?>
    <?php echo 'page: ' . $_GET['page'] . '<br>'; ?>
</div>

<?php require_once('includes/footer.php'); ?>