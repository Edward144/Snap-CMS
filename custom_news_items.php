<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <?php
        
        $post = new postUser('news_item');
        $post->getPost();

    ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>