<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTitle(); ?></h1>
        <div class="formBlock">
            <form id="generalSettings">
                <p>
                    <label id="helper">Hide Posts: <span id="helperText">Hiding posts will prevent users from accessing the posts page or any individual posts.</span></label>
                    
                    <?php $postsHidden = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'hide_posts'")->fetch_array()[0]; ?>
                    
                    <input type="checkbox" name="hidePosts" <?php echo ($postsHidden == 1 ? 'checked' : ''); ?>>
                </p>
                
                <p>
                    <label id="helper">Homepage: <span id="helperText">The posts page will be used if no homepage is selected.</span></label>
                    
                    <select name="homepage">
                        <option value="" selected>No Homepage</option>
                        
                        <?php 
                            $pages = $mysqli->query("SELECT id, name FROM `pages` Order By name ASC"); 
                            $currPage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0]; 
                        ?>
                        <?php while($page = $pages->fetch_assoc()) : ?>
                            <option value="<?php echo $page['id']; ?>" <?php echo ($page['id'] == $currPage ? 'selected' : ''); ?>><?php echo $page['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </p>
                
                <p>
                    <input type="submit" value="Submit">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
        
        <div class="formBlock">            
            <form id="customPosts">
                <h2>Custom Post Types</h2>
                
                <table>
                    <tr class="headers">
                        <td>Name</td>
                        <td>Actions</td>
                    </tr>
                    
                    <?php $customs = $mysqli->query("SELECT name FROM `custom_posts` ORDER BY name ASC"); ?>
                    
                    <?php while($row = $customs->fetch_assoc()) : ?>
                        <tr>
                            <td>
                                <input type="text" name="postTypeName" placeholder="Name" value="<?php echo $row['name']; ?>">
                            </td>

                            <td>
                                <input type="button" class="badButton" value="Delete" name="delete"> 
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                
                <p>
                    <input type="submit" value="Update Post Types">
                    <input type="button" value="Add Row" name="addRow">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="scripts/generalSettings.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>