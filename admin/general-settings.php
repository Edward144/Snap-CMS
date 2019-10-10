<?php require_once('includes/header.php'); ?>

<div id="generalSettings">
    <div class="column-60 formBlock settings">
        <h2 class="greyHeader">Settings</h2>
        
        <div>            
            <form id="editSettings" method="POST" action="scripts/editSettings.php">
                <p>
                    <?php $hidePosts = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'hide posts'")->fetch_array()[0]; ?>
                    
                    <label>Hide Posts</label>
                    <input type="checkbox" name="hide posts" <?php echo ($hidePosts == 1 ? 'checked' : ''); ?>>
                </p>
                
                <p>
                    <?php $homepage = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'homepage'")->fetch_array()[0]; ?>
                    
                    <label>Homepage</label>
                    <select name="homepage">
                        <option value="0">No Homepage</option>
                        
                        <?php $posts = $mysqli->query("SELECT `posts`.id, `posts`.name, `post_types`.name as post_type FROM `posts` INNER JOIN `post_types` ON `post_types`.id = `posts`.post_type_id ORDER BY post_type, name ASC"); ?>
                        
                        <?php if($posts->num_rows > 0) : ?>
                            <?php while($row = $posts->fetch_assoc()) : ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $homepage ? 'selected' : '');?>><?php echo ucwords(str_replace('-', ' ', $row['post_type'])) . ': ' . $row['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </p>
                
                <p>
                    <?php $analyticsCode = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'google analytics'")->fetch_array()[0]; ?>
                    
                    <label>Google Analytics</label>
                    <input type="text" name="google analytics" placeholder="UA-12345678-9" value="<?php echo $analyticsCode; ?>">
                </p>
                
                <input type="submit" value="Save">
                
                <p id="message"><?php 
                    if(isset($_SESSION['genmessage'])) {
                        echo $_SESSION['genmessage'];
                        unset($_SESSION['genmessage']);
                    }
                ?></p>
            </form>
        </div>
    </div>
    
    <div class="column-40 formBlock customPosts">
        <h2 class="greyHeader">Custom Post Types</h2>
        
        <div>
            <?php 
                $postTypes = $mysqli->query("SELECT id, name, has_options FROM `post_types` WHERE name <> 'posts' AND name <> 'pages' ORDER BY name ASC");
            
                if($postTypes->num_rows > 0) : 
            ?>
                <div class="hasTable">
                    <form id="editPostTypes" method="POST" action="scripts/deletePostType.php">
                        <table class="formattedTable">
                            <thead>
                                <th>Name</th>
                                <th>Has Options</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php while($row = $postTypes->fetch_assoc()) : ?>
                                    <tr>                                    
                                        <td style="text-align: left;">
                                            <span><?php echo ucwords(str_replace('-', ' ', $row['name'])); ?></span>
                                        </td>

                                        <td>
                                            <span><?php echo ($row['has_options'] == 1 ? 'TRUE' : 'FALSE'); ?></span>
                                        </td>

                                        <td>
                                            <input type="submit" name="delete-<?php echo $row['id']; ?>" value="Delete" class="redButton">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <p id="message" style="margin-top: 1em;"><?php 
                            if(isset($_SESSION['pdmessage'])) {
                                echo $_SESSION['pdmessage'];
                                unset($_SESSION['pdmessage']);
                            }
                        ?></p>
                    </form>
                </div>
            <?php else : ?>
                <h3 style="color: red;">You don't have any custom post types</h3>
                <?php unset($_SESSION['pdmessage']); ?>
            <?php endif; ?>
        </div>
        
        <h2 class="greyHeader" style="margin-top: 1em;">Create New Post Type</h2>
        
        <div>
            <p>Setting <code>Has Options</code> to true will display additional product related options for that post editor, such as specifications.</p>
            
            <form id="addPostTypes" method="POST" action="scripts/addPostType.php">
                <p>
                    <label>Post Type Name</label>
                    <input type="text" name="postName" value="<?php echo (isset($_SESSION['postName']) ? $_SESSION['postName'] : ''); ?>">
                </p>
                
                <p>
                    <label>Has Options</label>
                    <input type="checkbox" name="hasOptions">
                </p>
                
                <input type="submit" value="Submit">
                
                <p id="message"><?php 
                        if(isset($_SESSION['pamessage'])) {
                            echo $_SESSION['pamessage'];
                            unset($_SESSION['pamessage']);
                        }
                    ?></p>
                
                <?php unset($_SESSION['postName']); ?>
            </form>
            
            <script>
                $("#addPostTypes").submit(function() {
                    if($("#addPostTypes input[name='postName']").val() == "") {
                        $("#addPostTypes #message").text("Post type name is required");
                        
                        event.preventDefault();
                        return;
                    }
                });
            </script>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>