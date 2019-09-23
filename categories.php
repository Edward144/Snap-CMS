<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <?php
        $categories = new category($_GET['postType']);
        $categories->display();
    ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>