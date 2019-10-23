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
            SELECT posts.name, posts.content, posts.url, posts.main_image, posts.gallery_images, posts.specifications,
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
                        options
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
    list page
<?php endif; ?>


<?php require_once('includes/footer.php'); ?>