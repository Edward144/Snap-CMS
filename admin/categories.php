<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>

    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <?php 
        $editor = new categoryEditor($_GET['postType']);
        $editor->display();
    ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>