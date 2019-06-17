<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>
    
    <?php $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0]; ?>

    <?php if(isset($homepage) && $homepage != '') : ?>
        <?php $page = $mysqli->query("SELECT * FROM `pages` WHERE id = {$homepage}"); ?>

        <?php if($page->num_rows > 0) : ?>
            <?php while($row = $page->fetch_assoc()) : ?>                    
                <?php if($row['visible'] == 1) : ?>

                    <div class="hero" style="<?php echo ($row['image_url'] != null && $row['image_url'] != '' ? 'background-image: url(\'' . $row['image_url'] . '\')' : ''); ?>">
                        <h1><?php echo $row['name']; ?></h1>
                    </div>
                    <div class="mainInner">
                        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php'); ?>
                        
                        <div class="content">
                            <div class="pageContent">
                                <?php echo $row['content']; ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <?php 
                        http_response_code(404); 
                        header("Location: /404"); 
                    ?>
                <?php endif; ?>
            <?php endwhile; ?>                
        <?php endif; ?>
    <?php else : ?>
        <div class="mainInner">
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php'); ?>
            
            <div class="content">
                <h1>Posts</h1>

                <?php 
                    $postCount = $mysqli->query("SELECT COUNT(*) from `posts` WHERE visible = 1")->fetch_array()[0];

                    if(isset($_GET['category'])) {
                        $postCount = $mysqli->query("SELECT COUNT(*) from `posts` WHERE visible = 1 AND category_id = {$_GET['category']}")->fetch_array()[0];
                    }

                    $pagination = new pagination($postCount);
                    $pagination->load();
                    $posts = $mysqli->query("SELECT * FROM `posts` WHERE visible = 1 ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");

                    if(isset($_GET['category'])) {
                        $posts = $mysqli->query("SELECT * FROM `posts` WHERE visible = 1 AND category_id = {$_GET['category']} ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");
                    }
                ?>

                <?php if($posts->num_rows > 0) : ?>
                    <?php while($row = $posts->fetch_assoc()) : ?>
                        <div class="post">
                            <h2><a href="/posts/<?php echo $row['url']?>"><?php echo $row['name']; ?></a></h2>

                            <?php 
                                $length = strlen($row['description']); 

                                if($length <= 200) : 
                            ?>
                            <p><?php echo $row['description']; ?><a href="/posts/<?php echo $row['url']; ?>">View More</a></p>
                            <?php else : ?>
                                <p><?php echo substr($row['description'], 0, 200) . '...'; ?><a href="/posts/<?php echo $row['url']; ?>">View More</a></p>
                            <?php endif; ?>
                        </div>

                        <hr>
                    <?php endwhile; ?>

                    <?php $pagination->display(); ?>
                <?php else : ?>
                    <p>There are currently no posts.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>