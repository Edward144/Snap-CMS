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
            
            <?php echo $pagination->display(); ?>
        </div>
    <?php else : ?>
        <?php goto p404; ?>
    <?php endif; ?>
<?php elseif($homepage > 0) : ?>
    <?php $page = $mysqli->query("SELECT * FROM `posts` WHERE id = {$homepage} AND visible = 1"); ?>

    <?php if($page->num_rows > 0) : ?>
        <?php $page = $page->fetch_assoc(); ?>
        
        <?php if($page['main_image'] != null) : ?>
            <div class="hero">
                <img class="heroImage" src="<?php echo $page['main_image']; ?>">
                
                <?php if($page['gallery_images'] != null) : ?>
                    <div class="gallery owl-carousel">
                        <?php 
                            $images = explode(';', rtrim($page['gallery_images'], ';'));
                            
                            foreach($images as $image) :
                            $image = ltrim($image, '"');
                            $image = rtrim($image, '"');
                        ?>
                            <div>
                                <img src="<?php echo $image; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <script>
                        $(".gallery.owl-carousel").owlCarousel({
                            responsive: {
                                0 : {
                                    items: 3
                                },
                                600: {
                                    items: 5
                                },
                                1000: {
                                    items: 10
                                }
                            },
                            rtl: true,
                            margin: 10
                        });

                        var active = false;

                        $(".gallery img").click(function() {                                
                            var src = $(this).attr("src");

                            if(active == false && src != $(".heroImage").attr("src")) {
                                active = true;

                                if($("#heroBlur").length > 0) {
                                    $("#heroBlur").animate({
                                        "opacity" : 0
                                    }, 350, function() {
                                        $("#heroBlur").remove();

                                        changeImage(src);
                                    });
                                }
                                else {
                                    changeImage(src);
                                }

                            }
                            else {
                                return;
                            }
                        });

                        function changeImage(src) {
                            $(".heroImage").after("<img class='heroImage' src='" + src + "' style='z-index: -1; position: absolute; top: 0; left: 0; right: 0; bottom: 0;'>");

                            $(".hero").append("<div id='heroBlur' style='opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; backdrop-filter: blur(10px);'><img src='" + src + "' style='position: absolute; top: 0; bottom: 0; left: 0; right: 0; object-fit: contain; width: 100%; height: 100%;'></div>");

                            $(".heroImage:first-child").animate({
                                "opacity" : "0" 
                            }, 1000, function() {
                                $(".heroImage:first-child").remove();
                                $(".heroImage").css("z-index", "");

                                active = false;
                                console.log(active);
                            });

                            $("#heroBlur").animate({
                                "opacity" : 1
                            }, 1000);
                        }
                    </script>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="content home single">
            <?php echo ($page['name'] != null && $page['name'] != '' ? '<h1 class="title">' . $page['name'] . '</h1>' : ''); ?>
            
            <?php if($page['content'] != null && $page['content'] != '') : ?>
                <div class="userContent">
                    <?php echo $page['content']; ?>
                </div>
            <?php endif; ?>
            
            <?php if($page['custom_content'] != null && $page['custom_content'] != '') : ?>
                <div class="customContent">
                    <?php include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . $page['custom_content']); ?>
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