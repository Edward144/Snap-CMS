<?php require_once(dirname(__FILE__) . '/includes/header.php'); ?>

<?php 
    $homepage = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'homepage'")->fetch_array()[0]; 
    $hidePosts = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'hide posts'")->fetch_array()[0]; 
?>

<?php if($homepage == 0 && $hidePosts != 1) : ?>
    <?php 
        $postCount = $mysqli->query("SELECT * FROM `posts` WHERE post_type_id = 1 AND visible = 1")->num_rows;
        $pagination = new pagination($postCount); 
        $pagination->prefix = ROOT_DIR;
        $pagination->load();

        $posts = $mysqli->query("SELECT * FROM `posts` WHERE post_type_id = 1 AND visible = 1 ORDER BY date_posted DESC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}"); 
    ?>

    <?php if($posts->num_rows > 0) : ?>
        <?php $postDetails = $mysqli->query("SELECT * FROM `post_types` WHERE id = 1")->fetch_assoc(); ?>
        
        <?php if($postDetails['image_url'] != null && $postDetails['image_url'] != '') : ?>
            <div class="hero <?php echo $postDetails['name']; ?>">
                <img class="heroImage" src="<?php echo $postDetails['image_url']; ?>">
            </div>
        <?php endif; ?>

        <div class="content home list <?php echo $postDetails['name']; ?>">
            <?php if($postDetails['title'] != null && $postDetails['title'] != '') : ?>
                <h1 class="title"><?php echo $postDetails['title']; ?></h1>
            <?php elseif($postDetails['name'] != null && $postDetails['name'] != '') : ?>
                <h1 class="title"><?php echo ucwords(str_replace('-', ' ', $postDetails['name'])); ?></h1>
            <?php endif; ?>
            
            <?php echo ($postDetails['content'] != null && $postDetails['content'] != '' ? '<div class="listContent">' . $postDetails['content'] . '</div>' : ''); ?>
            
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
        </div>
    <?php else : ?>
        <?php goto p404; ?>
    <?php endif; ?>
<?php elseif($homepage > 0) : ?>
    <?php 
        $postDetails = $mysqli->query("SELECT * FROM `post_types` WHERE id = 1")->fetch_assoc(); 
        $post = $mysqli->query("SELECT * FROM `posts` WHERE id = {$homepage} AND visible = 1"); 
    ?>

    <?php if($post->num_rows > 0) : ?>
        <?php 
            $post = $post->fetch_assoc(); 
            $slider = $mysqli->query("
                SELECT slider_items.id, slider_items.slider_id, sliders.post_id, slider_items.position, slider_items.image_url, slider_items.content, sliders.animation_in, sliders.animation_out, sliders.speed, sliders.visible FROM slider_items
                LEFT OUTER JOIN sliders ON sliders.id = slider_items.slider_id
                WHERE sliders.post_id = {$post['id']} AND visible = 1
            ");
        ?>
        
        <?php if($post['main_image'] != null && $slider->num_rows <= 0) : ?>
            <div class="hero">
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
            <div class="hero slider owl-carousel">
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
                        echo ($sliderSettings['speed'] != null && $sliderSettings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $sliderSettings['speed'] . ',' : 'autoplay: false, mouseDrag: false, touchDrag: false, pullDrag: false');
                        echo ($sliderSettings['animation_in'] != null && $sliderSettings['animation_in'] != '' ? 'animateIn: "' . $sliderSettings['animation_in'] . '", ' : '');
                        echo ($sliderSettings['animation_out'] != null && $sliderSettings['animation_out'] != '' ? 'animateOut: "' . $sliderSettings['animation_out'] . '", ' : '');
                    ?>
                });
            </script>
        <?php endif; ?>

        <div class="content home single">
            <?php echo ($post['name'] != null && $post['name'] != '' ? '<h1 class="title">' . $post['name'] . '</h1>' : ''); ?>
            
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
        <?php goto p404; ?>
    <?php endif; ?>
<?php else : ?>
    <?php p404: header('Location: ' . ROOT_DIR . '404'); exit(); ?>
<?php endif; ?>

<?php require_once(dirname(__FILE__) . '/includes/footer.php'); ?>