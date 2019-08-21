<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>
    
    <?php 
        
        $post = new post(rtrim($_GET['postType'], 's'));
        //$post->debug();
        $post->display();

    ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>