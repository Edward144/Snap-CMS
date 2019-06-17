<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <?php $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0]; ?>

    <?php 
        $hidePosts = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'hide_posts'")->fetch_array()[0];

        if($hidePosts == 1 && ($homepage != '' && $homepage != null)) {
            header('Location: /');
        }
    ?>
     
    <?php if(isset($_GET['url'])) : ?>        
        <?php $post = $mysqli->query("SELECT * FROM `posts` WHERE url = '{$_GET['url']}'"); ?>

        <?php if($post->num_rows > 0) : ?>
            <?php while($row = $post->fetch_assoc()) : ?>        
                <?php if($row['visible'] == 1) : ?>
                    <?php
                        $author = $row['author'];
                        $authorF = $mysqli->query("SELECT first_name FROM `users` WHERE username = '{$author}'")->fetch_array()[0];
                        $authorL = $mysqli->query("SELECT last_name FROM `users` WHERE username = '{$author}'")->fetch_array()[0];
                        $author = $authorF . ' ' . $authorL;

                        $catId = $row['category_id'];
                        $category = $mysqli->query("SELECT name FROM categories where id = {$catId}")->fetch_array()[0];
                    ?>

                    <div class="hero" style="<?php echo ($row['image_url'] != null && $row['image_url'] != '' ? 'background-image: url(\'' . $row['image_url'] . '\')' : ''); ?>">
                        <div class="postDetails">
                            <h1><?php echo $row['name']; ?></h1>

                            <?php if($category != null && $category != '') : ?>
                                <h3><?php echo $category; ?></h3>
                            <?php endif; ?>

                            <div class="author">
                                <p>
                                    <strong>By: </strong><span><?php echo ucwords($author); ?></span> 
                                    <strong>On: </strong><span><?php echo date('d/m/Y - H:i:s', strtotime($row['date_posted'])); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mainInner">
                        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php'); ?>

                        <div class="content">
                            <div class="postContent">
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
        <?php else : ?>
            <?php 
                http_response_code(404); 
                header("Location: /404"); 
            ?>
        <?php endif; ?>
    <?php else : ?>
        <?php
            if(isset($homepage) && ($homepage == null || $homepage == '')) {
                header('HTTP/1.1 301 Moved Permenantly');
                header('Location: /');
                exit();
            }
        ?>
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
                            <p><?php echo $row['description']; ?><br><a href="/posts/<?php echo $row['url']; ?>">View More</a></p>
                            <?php else : ?>
                                <p><?php echo substr($row['description'], 0, 200) . '...'; ?><br><a href="/posts/<?php echo $row['url']; ?>">View More</a></p>
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