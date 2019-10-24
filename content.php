<?php require_once('includes/header.php'); ?>

<?php 
    $homepage = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'homepage'")->fetch_array()[0]; 
    $hidePosts = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'hide posts'")->fetch_array()[0]; 
    
    //Go to posts if no type set
    if(!isset($_GET['post-type'])) {
        include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');
        
        exit();
    }

    //Go to home if accessing page list or post when posts are hidden
    if(($hidePosts == 1 && $_GET['post-type'] == 'posts') || ($_GET['post-type'] == 'pages' && !isset($_GET['url']))) {
        header('Location: ' . ROOT_DIR);
        
        exit();
    }

    $postDetails = $mysqli->query("SELECT * FROM `post_types` WHERE name = '{$_GET['post-type']}'");
    
    //Go to posts if type does not exist
    if($postDetails->num_rows <= 0) {
        http_response_code(404);
        include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');
        
        exit();
    }
    else {
        $postDetails = $postDetails->fetch_assoc();
    }
?>

<?php if(isset($_GET['url'])) : ?>
    <?php 
        $post = $mysqli->query("
            SELECT posts.id, posts.name, posts.content, posts.url, posts.main_image, posts.gallery_images, posts.specifications,
            posts.author, posts.date_posted, categories.name AS category FROM `posts` AS posts 
            LEFT OUTER JOIN `categories` AS categories ON categories.id = posts.category_id
            WHERE url = '{$_GET['url']}' AND visible = 1
        "); 
    ?>

    <?php 
        if($post->num_rows <= 0) {
            http_response_code(404);
            include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');
            
            exit();
        }
        else {
            $post = $post->fetch_assoc();
            $slider = $mysqli->query("
                SELECT slider_items.id, slider_items.slider_id, sliders.post_id, slider_items.position, slider_items.image_url, slider_items.content, sliders.animation_in, sliders.animation_out, sliders.speed, sliders.visible FROM slider_items
                LEFT OUTER JOIN sliders ON sliders.id = slider_items.slider_id
                WHERE sliders.post_id = {$post['id']} AND visible = 1
            ");
        }

        if($post['id'] == $homepage) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: ' . ROOT_DIR);
            
            exit();
        }
    ?>

    <?php if($post['main_image'] != null && $slider->num_rows <= 0) : ?>
        <div class="hero <?php echo $postDetails['name']; ?>">
            <img class="heroImage" src="<?php echo $post['main_image']; ?>">

            <?php if($post['gallery_images'] != null) : ?>
                <div class="gallery owl-carousel">
                    <?php 
                        $images = explode(';', rtrim($post['gallery_images'], ';'));

                        foreach($images as $image) :
                        $image = ltrim($image, '"');
                        $image = rtrim($image, '"');
                    ?>
                        <div>
                            <img src="<?php echo $image; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <script src="<?php echo ROOT_DIR; ?>scripts/gallery.js"></script>
            <?php endif; ?>
        </div>
    <?php elseif($slider->num_rows > 0) : ?>
        <div class="hero slider owl-carousel <?php echo $postDetails['name']; ?>">
            <?php while($slide = $slider->fetch_assoc()) : ?>
                <div class="slide" style="background-image: url('<?php echo $slide['image_url']; ?>');">
                    <div class="slideInner">
                        <?php echo $slide['content']; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php $sliderSettings = $mysqli->query("SELECT * FROM `sliders` WHERE post_id = {$post['id']}")->fetch_assoc(); ?>

        <script>
            $(".slider.owl-carousel").owlCarousel({
                items: 1,
                center: true,
                loop: true,
                dots: true,
                nav: true,
                <?php 
                    echo ($sliderSettings['speed'] != null && $sliderSettings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $sliderSettings['speed'] . ',' : 'autoplay: false,');
                    echo ($sliderSettings['animation_in'] != null && $sliderSettings['animation_in'] != '' ? 'animateIn: "' . $sliderSettings['animation_in'] . '", ' : '');
                    echo ($sliderSettings['animation_out'] != null && $sliderSettings['animation_out'] != '' ? 'animateOut: "' . $sliderSettings['animation_out'] . '", ' : '');
                ?>
            });
        </script>
    <?php endif; ?>

    <div class="content single <?php echo $postDetails['name']; ?>">
        <?php 
            echo ($post['name'] != null && $post['name'] != '' ? '<h1 class="title">' . $post['name'] . '</h1>' : ''); 
            echo ($post['category'] != null && $post['category'] != '' ? '<h3 class="category"><span class="label">Category: </span>' . $post['category'] . '</h3>' : ''); 
            echo ($post['author'] != null && $post['author'] != '' ? '<h4 class="posted"><span class="author">' . $post['author'] . '</span>' . ($post['date_posted'] != null && $post['date_posted'] != '' ? ' <span class="datetime"><span class="date">' . date('d/m/Y', strtotime($post['date_posted'])) . '</span> <span class="time">' . date('h:i', strtotime($post['date_posted'])) . '</span></span>': '') . '</h4>': ''); 
        ?>

        <?php if($post['content'] != null && $post['content'] != '') : ?>
            <div class="userContent">
                <?php if($postDetails['has_options'] == 1) : ?>
                    <div class="additionalOptions">
                        <?php if($post['specifications'] != null) : ?>
                            <div class="optionTab specifications">
                                <h3>Specifications</h3>

                                <div>
                                    <div>
                                        <table>
                                            <?php
                                                $specifications = explode(';', rtrim($post['specifications'], ';'));

                                                foreach($specifications as $specRow) :
                                                    $specName = explode('":"', ltrim($specRow, '"'))[0];
                                                    $specValue = explode('":"', rtrim($specRow, '"'))[1];
                                            ?>
                                                <tr>
                                                    <td><?php echo $specName; ?></td>
                                                    <td><?php echo $specValue; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <script src="<?php echo ROOT_DIR; ?>scripts/additionalOptions.js"></script>
                    </div>
                <?php endif; ?>
                
                <?php echo $post['content']; ?>
            </div>
        <?php endif; ?>

        <?php if($post['custom_content'] != null && $post['custom_content'] != '') : ?>
            <div class="customContent">
                <?php include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . $post['custom_content']); ?>
            </div>
        <?php endif; ?>
    </div>
<?php else : ?>
    <?php 
        $postCount = $mysqli->query("
            SELECT posts.id, post_types.name AS post_type FROM `posts` AS posts 
                LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = posts.post_type_id
            WHERE post_types.name = '{$_GET['post-type']}' AND visible = 1
        ")->num_rows;
        $pagination = new pagination($postCount); 
        $pagination->prefix = explode('page-', $_SERVER['REQUEST_URI'])[0] . '/';
        $pagination->load();

        $getCat = (isset($_GET['category']) ? 'AND category_id = ' . $_GET['category'] : ''); 
        
        $posts = $mysqli->query("
            SELECT posts.id, posts.name, posts.content, posts.url, posts.main_image, posts.author, posts.date_posted, posts.short_description, posts.category_id, categories.name AS category, post_types.name AS post_type FROM `posts` AS posts 
                LEFT OUTER JOIN `categories` AS categories ON categories.id = posts.category_id
                LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = posts.post_type_id
            WHERE visible = 1 AND post_types.name = '{$_GET['post-type']}' {$getCat}
            LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}
        "); 
    ?>

    <?php if($postDetails['image_url'] != null && $postDetails['image_url'] != '') : ?>
        <div class="hero <?php echo $postDetails['name']; ?>">
            <img class="heroImage" src="<?php echo $postDetails['image_url']; ?>">
        </div>
    <?php endif; ?>

    <div class="content list <?php echo $postDetails['name']; ?>">        
        <?php if($postDetails['title'] != null && $postDetails['title'] != '') : ?>
            <h1 class="title"><?php echo $postDetails['title']; ?></h1>
        <?php elseif($postDetails['name'] != null && $postDetails['name'] != '') : ?>
            <h1 class="title"><?php echo ucwords(str_replace('-', ' ', $postDetails['name'])); ?></h1>
        <?php endif; ?>
        
        <?php echo ($postDetails['content'] != null && $postDetails['content'] != '' ? '<div class="listContent">' . $postDetails['content'] . '</div>' : ''); ?>
        
        <?php if($posts->num_rows > 0) : ?>
            <div class="postList">
                <?php while($post = $posts->fetch_assoc()) : ?>
                    <div class="listItem">
                        <div class="imageWrap">
                            <?php echo ($post['main_image'] != null ? '<img src="' . $post['main_image'] . '">' : ''); ?>
                        </div>

                        <div class="itemDetails">
                            <?php
                                echo ($post['name'] != null && $post['name'] != '' ? '<h3>' . $post['name'] . '</h3>' : ''); 
                                echo ($post['category'] != null && $post['category'] != '' ? '<h4>' . $post['category'] . '</h4>' : '');
                                echo ($post['author'] != null && $post['author'] != '' ? '<h5>' . $post['author'] . ' <span id="dateTime">' . date('d/m/Y H:i', strtotime($post['date_posted'])) . '</span></h5>' : '');

                                echo ($post['short_description'] != null && $post['short_description'] != '' ? '<p>' . $post['short_description'] . '</p>' : '');
                            ?>
                            <a href="<?php echo ROOT_DIR . 'post-type/' . $postDetails['name'] . '/' . $post['url']; ?>">Read More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php echo $pagination->display(); ?>
        <?php else : ?>        
            <h3 style="margin-top: 2em;">There are currently no items</h3>
        <?php endif; ?>
    </div>
<?php endif; ?>


<?php require_once('includes/footer.php'); ?>