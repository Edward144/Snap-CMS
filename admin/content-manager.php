<?php require_once('includes/header.php'); ?>

<?php 
    if(!isset($_GET['post-type'])) {
        header('Location: ' . ROOT_DIR . 'admin/content-manager/posts');
        exit();
    }

    $postTypeId = $mysqli->query("SELECT id, name FROM `post_types` WHERE name = '{$_GET['post-type']}'");
    
    if($postTypeId->num_rows <= 0) {
        header('Location: ' . ROOT_DIR . 'admin/content-manager/posts');
        exit();
    }
    else {
        $postType = $postTypeId->fetch_assoc();
        $postTypeId = $postType['id'];
        $postTypeName = $postType['name'];
    }
?>

<?php if(isset($_GET['id'])) : ?>
    <?php 
        //Check if post exists
        $post = $mysqli->query(
            "SELECT * FROM `posts` WHERE id = {$_GET['id']} LIMIT 1"
        );

        if($post->num_rows <= 0) {
            header('Location: ./');
            exit();
        }

        $post = $post->fetch_assoc();
    ?>

    <form id="contentManage" method="POST" action="../scripts/contentManage.php">
        <div class="flexContainer" id="contentManager">
            <div class="column column-30 formBlock contentControls">
                <h2 class="greyHeader"><?php echo ucwords(str_replace('-', ' ', rtrim($postTypeName, 's'))) . ' ' . $_GET['id']; ?>: General Details</h2>
                
                <div>
                    <p>
                        <label>Post Title</label>
                        <input type="text" name="postName" value="<?php echo $post['name']; ?>">
                    </p>
                    
                    <p>
                        <label>URL Slug</label>
                        <input type="text" name="postUrl" value="<?php echo $post['url']; ?>">
                    </p>
                    
                    <p>
                        <label>Category</label>
                        <select name="postCategory">
                        
                        </select>
                    </p>
                    
                    <p>
                        <label>Date Posted</label>
                        <input type="datetime-local" name="postDate" value="<?php echo date('Y-m-d\TH:i', strtotime($post['date_posted'])); ?>">
                    </p>
                    
                    <p>
                        <label>Author</label>
                        <input type="text" name="postAuthor" value="<?php echo $post['author']; ?>">
                    </p>
                    
                    <br>
                    
                    <p>
                        <label>Link Custom File</label>
                        <input type="text" name="postCustom" value="<?php echo $post['custom_content']; ?>">
                    </p>
                    
                    <p>
                        <?php if($post['visible'] == 1) : ?>
                            <input type="button" name="hide" value="Visible" data-id="<?php echo $post['id']; ?>">
                        <?php else : ?>
                            <input type="button" name="show" value="Hidden" data-id="<?php echo $post['id']; ?>">
                        <?php endif; ?>
                        
                        <input type="button" name="delete" value="Delete" class="redButton" data-id="<?php echo $post['id']; ?>">
                    </p>
                    
                    <input type="submit" value="Save Post">
                    
                    <p id="message" class="contentMessage"></p>
                    
                    <hr>
                    
                    <h3>Past Revisions</h3>
                    
                    <p>
                        <select>
                        
                        </select>
                    </p>
                </div>
            </div>
            
            <div class="column column-70 formBlock contentControls">
                <h2 class="greyHeader">Content</h2>
                
                <div>
                    <p>
                        <label>Short Description</label>
                        <textarea class="noTiny" name="postDesc" maxlength="500"><?php echo $post['short_description']; ?></textarea>
                    </p>
                    
                    <textarea name="postContent"><?php echo $post['content']; ?></textarea>
                </div>
                
                <h2 class="greyHeader" style="margin-top: 1em;">Gallery</h2>
                
                <div class="imageUploader">
                    <div class="images">
                        <?php
                            $images = explode(';', rtrim($post['gallery_images'], ';'));
                            
                            foreach($images as $image) : 
                                $image = ltrim($image, '"');
                                $image = rtrim($image, '"');
                        ?>                            
                            <div class="image existingImage" id="<?php echo ($post['main_image'] == $image ? 'main' : ''); ?>">
                                <div class="imageWrap">
                                    <img src="<?php echo $image; ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="newImages">
                            <div class="image addImage">
                                <span>+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php else : ?>
    <div class="flexContainer">
        <div class="column column-30 formBlock contentDetails">
            <h2 class="greyHeader">Content Controls</h2>

            <div>
                <form id="createContent" method="POST" action="<?php echo ROOT_DIR; ?>admin/scripts/createPost.php">
                    <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <input type="hidden" name="postType" value="<?php echo $postTypeName; ?>">
                    <input type="hidden" name="postTypeId" value="<?php echo $postTypeId; ?>">
                    <input type="submit" value="Create New Post">
                    
                    <p id="message"><?php 
                        if(isset($_SESSION['createmessage'])) {
                            echo $_SESSION['createmessage'];
                            unset($_SESSION['createmessage']);
                        }
                    ?></p>
                </form>
                
                <hr>
                
                <form id="searchContent" method="POST" action="<?php echo ROOT_DIR; ?>admin/scripts/searchPost.php">
                    <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    
                    <p>You can search content by name, url and author.</p>
                    
                    <p>
                        <label>Search Term</label>
                        <input type="text" name="searchTerm" value="<?php echo(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                    </p>
                    
                    <input type="submit" value="Search" style="margin-top: 0.5em;">
                    
                    <?php if(isset($_GET['search'])) : ?>
                        <input type="button" name="clearSearch" value="Clear Search" class="redButton" style="margin-top: 0.5em;">
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="column column-70 formBlock contentList">
            <h2 class="greyHeader"><?php echo ucwords(str_replace('-', ' ', $postTypeName)); ?></h2>

            <?php 
                $searchTerm = (isset($_GET['search']) ? $_GET['search'] : '');
            
                $itemCount = $mysqli->query("SELECT * FROM `posts` WHERE post_type_id = {$postTypeId} AND (name LIKE '%{$searchTerm}%' OR url LIKE '%{$searchTerm}%' OR author LIKE '%{$searchTerm}%')")->num_rows;
                $pagination = new pagination($itemCount);
                $pagination->prefix = explode('/page-', $_SERVER['REQUEST_URI'])[0] . '/';
                $pagination->load();
                   
                $posts = $mysqli->query(
                    "SELECT 
                        posts.id, 
                        posts.name, 
                        posts.url,
                        categories.name AS category,
                        posts.author,
                        posts.date_posted,
                        posts.last_edited,
                        posts.visible FROM `posts` AS posts 
                            LEFT OUTER JOIN `categories` AS categories ON posts.category_id = categories.id AND posts.post_type_id = categories.post_type_id
                        WHERE posts.post_type_id = {$postTypeId} AND (posts.name LIKE '%{$searchTerm}%' OR posts.url LIKE '%{$searchTerm}%' OR posts.author LIKE '%{$searchTerm}%') 
                        ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}"
                ); 
            ?>

            <?php if($posts->num_rows > 0) : ?>
                <div>
                    <div class="hasTable">
                        <table class="formattedTable" id="contentList">
                            <thead>
                                <th>ID</th>
                                <th>Details</th>
                                <th>Author</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php while($row = $posts->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <span><?php echo $row['id']; ?></span>
                                        </td>

                                        <td>
                                            <span><strong><?php echo $row['name']; ?></strong></span>
                                            <span><?php echo ($row['url'] != null && $row['url'] != '' ? '<br>URL: ' . $row['url'] : ''); ?></span>
                                            <span><?php echo ($row['category'] != null && $row['category'] != '' ? '<br>Category: ' . $row['category'] : ''); ?></span>
                                        </td>

                                        <td>
                                            <span><strong>Author: </strong><?php echo ($row['author'] != null && $row['author'] != '' ? $row['author'] : 'Unknown'); ?></span>
                                            <br>
                                            <span><strong>Date Posted: </strong><?php echo date('d/m/Y', strtotime($row['date_posted'])); ?></span>
                                            <br>
                                            <span><strong>Last Edited: </strong><?php echo date('d/m/Y', strtotime($row['last_edited'])); ?></span>
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
                    <h3 class="noContent">You don't have any posts in this post type</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/content.js"></script>

<?php require_once('includes/footer.php'); ?>