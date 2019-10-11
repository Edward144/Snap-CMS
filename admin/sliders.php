<?php require_once('includes/header.php'); ?>

<?php if(isset($_GET['id'])) : ?>
    <?php 
        //Check if slider exists
        $slider = $mysqli->query(
            "SELECT 
                sliders1.id AS uid,
                sliders.id, 
                sliders.name,
                sliders.post_type_id,
                CASE WHEN sliders.post_type_id = 0 THEN 'unset' ELSE post_types.name END AS post_type, 
                sliders.post_id,
                CASE WHEN sliders.post_id = 0 THEN 'unset' ELSE posts.name END AS post_name, 
                sliders.animation_in, 
                sliders.animation_out, 
                sliders.speed, 
                sliders.visible FROM `sliders` AS sliders 
                    LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = sliders.post_type_id 
                    LEFT OUTER JOIN `posts` AS posts ON posts.id = sliders.post_id
                    LEFT OUTER JOIN `sliders` AS sliders1 ON sliders1.id = sliders.id WHERE sliders1.id = {$_GET['id']}
                LIMIT 1"
        );

        if($slider->num_rows <= 0) {
            header('Location: ./');
            exit();
        }

        $slider = $slider->fetch_assoc();
    ?>

    <form id="sliderManage" method="POST" action="../scripts/sliderManage.php">
        <div class="flexContainer" id="sliderManager">
            <div class="column column-70 formBlock sliderContent">
                <h2 class="greyHeader">Slides</h2>

                <div>
                    <?php                     
                        $slides = $mysqli->query("SELECT * FROM `slider_items` WHERE slider_id = {$_GET['id']} ORDER BY position ASC"); 
                    ?>

                    <div class="hasTable">
                        <table class="formattedTable">
                            <thead>
                                <th>Position</th>
                                <th>Background Image</th>
                                <th>Content</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php if($slides->num_rows > 0) : ?>
                                    <?php while($slide = $slides->fetch_assoc()) : ?>
                                        <tr class="slideRow">
                                            <td style="width: 80px; min-width: 80px;">
                                                <input type="number" step="1" name="position" value="<?php echo $slide['position']; ?>" style="text-align: center;">
                                            </td>

                                            <td>
                                                <input type="text" name="backgroundImage" value="<?php echo $slide['image_url']; ?>" class="hasButton">
                                                <input type="button" name="imageSelector" value="Select Image">
                                            </td>

                                            <td>

                                            </td>

                                            <td style="width: 80px; min-width: 80px;">
                                                <input type="button" name="deleteSlide" value="Delete" class="redButton">
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4"><h3 class="noContent">This slider has no slides</h3></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                
                    <input type="button" name="addSlide" value="Add Slide" data-slider="<?php echo $slider['id']; ?>" style="margin-top: 1em;">
                    
                    <p id="message" class="slidesMessage"></p>
                    
                    <script>
                        $("input[name='addSlide']").click(function() {
                            var position = 0;
                            
                            $(".slideRow").each(function() {
                                if($(this).find("input[name='position']").val() > position) {
                                    position = $(this).find("input[name='position']").val();
                                }
                            });
                            
                            position++;
                            
                            $.ajax({
                                url: "../scripts/addSlide.php",
                                method: "POST",
                                dataType: "json",
                                data: ({sliderId: $(this).attr("data-slider"), position}),
                                success: function(data) {
                                    if(data == 1) {
                                        window.location.reload();
                                    }
                                    else {
                                        $(".slidesMessage").text("Error: Could not add slide");
                                    }
                                }
                            });
                        });
                        
                        $("input[name='imageSelector']").click(function() {
                            var textbox = $(this).closest("tr").find("input[name='backgroundImage']").first();
                            
                            moxman.browse({
                                extensions: 'png, jpg, jpeg, gif, webp, svg',
                                skin: "snapcms",
                                oninsert: function(args) {
                                    var image = args.files[0].url;

                                    textbox.val(image);
                                }
                            });
                        });
                        
                        $(".sliderContent").on("click", "input[name='deleteSlide']", function() {
                            $(this).closest("tr").remove();
                        });
                    </script>
                </div>
            </div>

            <div class="column column-30 formBlock sliderDetails">
                <h2 class="greyHeader">Slider Details</h2>

                <div>
                    <p>
                        <label>Name</label>    
                        <input type="text" name="sliderName" value="<?php echo $slider['name']; ?>">
                    </p>
                    
                    <p>
                        <label>Post Type</label>
                        <select name="postType">
                            <option value="0" selected disabled>--Select Post Type--</option>
                            
                            <?php $postTypes = $mysqli->query("SELECT id, name FROM `post_types`"); ?>
                            
                            <?php if($postTypes->num_rows > 0) : ?>
                                <?php while($postType = $postTypes->fetch_assoc()) : ?>
                                    <option value="<?php echo $postType['id']; ?>" <?php echo ($postType['id'] == $slider['post_type_id'] ? 'selected' : ''); ?>><?php echo ucwords(str_replace('-', ' ', $postType['name'])); ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </p>
                    
                    <p>
                        <label>Assigned Post</label>
                        <select name="postName">
                            <option value="0" selected disabled>--Select Post--</option>
                            
                            <?php $posts = $mysqli->query("SELECT id, name FROM `posts` WHERE post_type_id = {$slider['post_type_id']}"); ?>
                            
                            <?php if($posts->num_rows > 0) : ?>
                                <?php while($post = $posts->fetch_assoc()) : ?>
                                    <option value="<?php echo $post['id']; ?>" <?php echo ($post['id'] == $slider['post_id'] ? 'selected' : ''); ?>><?php echo $post['name']; ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </p>
                    
                    <p>
                        <label>Animation In</label>
                        <input type="text" name="animationIn" value="<?php echo $slider['animation_in']; ?>">
                    </p>
                    
                    <p>
                        <label>Animation Out</label>
                        <input type="text" name="animationOut" value="<?php echo $slider['animation_out']; ?>">
                    </p>
                    
                    <p>
                        <label>Speed (Milliseconds)</label>
                        <input type="number" step="1000" name="speed" value="<?php echo $slider['speed']; ?>">
                    </p>
                    
                    <p>
                        <?php if($slider['visible'] == 1) : ?>
                            <input type="button" name="hide" value="Visible" data-id="<?php echo $slider['id']; ?>">
                        <?php else : ?>
                            <input type="button" name="show" value="Hidden" data-id="<?php echo $slider['id']; ?>">
                        <?php endif; ?>
                        
                        <input type="button" name="delete" value="Delete" class="redButton" data-id="<?php echo $slider['id']; ?>">
                    </p>
                    
                    <input type="submit" value="Save Slider">
                    
                    <p id="message"></p>
                </div>
            </div>
        </div>
    </form>
<?php else : ?>
    <div class="flexContainer">
        <div class="column column-30 formBlock sliderControls">
            <h2 class="greyHeader">Controls</h2>
            
            <div>
                <form id="createSlider" method="POST" action="<?php echo ROOT_DIR; ?>admin/scripts/createSlider.php">
                    <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <input type="submit" value="Create Slider">
                    
                    <p id="message"><?php 
                        if(isset($_SESSION['createmessage'])) {
                            echo $_SESSION['createmessage'];
                            unset($_SESSION['createmessage']);
                        }
                    ?></p>
                </form>
            </div>
        </div>
        
        <div class="column column-70 formBlock sliderList">
            <h2 class="greyHeader">Sliders</h2>
            <?php 
                $itemCount = $mysqli->query("SELECT * FROM `sliders`")->num_rows;
                $pagination = new pagination($itemCount);
                $pagination->prefix = explode('/page-', $_SERVER['REQUEST_URI'])[0] . '/';
                $pagination->load();
            
                $sliders = $mysqli->query(
                    "SELECT 
                        sliders.id, 
                        sliders.name, 
                        CASE WHEN sliders.post_type_id = 0 THEN 'unset' ELSE post_types.name END AS post_type, 
                        CASE WHEN sliders.post_id = 0 THEN 'unset' ELSE posts.name END AS post_name, 
                        sliders.animation_in, 
                        sliders.animation_out, 
                        sliders.speed, 
                        sliders.visible FROM `sliders` AS sliders 
                            LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = sliders.post_type_id 
                            LEFT OUTER JOIN `posts` AS posts ON posts.id = sliders.post_id
                        ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}"
                ); 
            ?>
            
            <?php if($sliders->num_rows > 0) : ?>
                <div>
                    <div class="hasTable">
                        <table class="formattedTable" id="sliderList">
                            <thead>
                                <th>ID</th>
                                <th>Details</th>
                                <th>Settings</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php while($row = $sliders->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <span><?php echo $row['id']; ?></span>
                                        </td>

                                        <td>
                                            <span><strong><?php echo $row['name']; ?></strong></span>
                                            <br>
                                            <span><?php echo($row['post_type'] == 'unset' || $row['post_name'] == 'unset' ? 'Not assigned' : 'Assigned to ' . $row['post_type'] . ': ' . $row['post_name']); ?></span>
                                        </td>

                                        <td>
                                            <span><strong>Anim In: </strong><?php echo $row['animation_in']; ?></span><br>
                                            <span><strong>Anim Out: </strong><?php echo $row['animation_out']; ?></span><br>
                                            <span><strong>Speed: </strong><?php echo $row['speed'] / 1000; ?> Seconds</span><br>
                                        </td>

                                        <td>
                                            <?php if($row['visible'] == 1) : ?>
                                                <span><input type="button" name="hide" value="Visible" data-id="<?php echo $row['id']; ?>"></span>
                                            <?php else : ?>
                                                <span><input type="button" name="show" value="Hidden"  data-id="<?php echo $row['id']; ?>"></span>
                                            <?php endif; ?>

                                            <span class="edit"><input type="button" name="edit" value="Edit" data-id="<?php echo $row['id']; ?>"></span>
                                            <span><input type="button" name="delete" class="redButton" value="Delete" data-id="<?php echo $row['id']; ?>"></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
                <?php echo $pagination->display(); ?>
            <?php else : ?>
                <div>
                    <h3 class="noContent">You don't have any sliders</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/sliders.js"></script>

<?php require_once('includes/footer.php'); ?>