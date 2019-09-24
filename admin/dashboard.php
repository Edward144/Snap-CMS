<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>
    
    <?php

        

    ?>

    <div class="content">
        <div class="dashboardBlocks">
            <?php 
            
                $postTypes = $mysqli->query("SELECT name FROM `custom_posts` ORDER BY name ASC");
            
                new dashboardBlock('pages'); 
                new dashboardBlock('posts');
                //new dashboardBlock('comments');
            
                while($postType = $postTypes->fetch_assoc()) {
                    new dashboardBlock($postType['name']); 
                }
            
            ?>
        </div>
    </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>