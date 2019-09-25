<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>
    
    <?php 

        $post = new post($_GET['postType']);
        $post->display();

    ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>