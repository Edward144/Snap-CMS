<?php require_once('includes/header.php'); ?>

<?php if(isset($_GET['id']) && $_GET['id'] > 0) : ?>
    <?php 
        //Check if slider exists
        $exists = $mysqli->query("SELECT id FROM `sliders` WHERE id = {$_GET['id']}");

        if($exists->num_rows <= 0) {
            header('Location: ./');
            exit();
        }
    ?>

    <form id="sliderManage" method="POST" action="../scripts/sliderManage.php">
        <div class="flexContainer" id="sliderManager">
            <div class="column column-70 formBlock sliderContent">
                <h2 class="greyHeader">Slides</h2>

                <div>

                </div>
            </div>

            <div class="column column-30 formBlock sliderDetails">
                <h2 class="greyHeader">Slider Details</h2>

                <div>
                    <p>
                        <label>Name</label>    
                        <input type="text">
                    </p>
                    
                    <p>
                        <label>Post Type</label>
                        <select>
                        
                        </select>
                    </p>
                    
                    <p>
                        <label>Assigned Post</label>
                        <select>
                        
                        </select>
                    </p>
                    
                    <p>
                        <label>Animation In</label>
                        <input type="text">
                    </p>
                    
                    <p>
                        <label>Animation Out</label>
                        <input type="text">
                    </p>
                    
                    <p>
                        <label>Speed (Seconds)</label>
                        <input type="number" step="1">
                    </p>
                    
                    <p>
                        <input type="button" value="Visible">
                        <input type="button" value="Delete" class="redButton">
                    </p>
                    
                    <input type="submit" value="Save Slider">
                    
                    <p id="message">test</p>
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