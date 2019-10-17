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
    <h2><?php echo $_GET['post-type'] . ': ' . $_GET['id']; ?></h2>
<?php else : ?>
    <div class="flexContainer" id="contentManager">
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
                    
                    <p>You can search content by name, url, category and author.</p>
                    
                    <p>
                        <label>Search Term</label>
                        <input type="text" name="searchTerm">
                    </p>
                    
                    <input type="submit" value="Search">
                </form>
            </div>
        </div>

        <div class="column column-70 formBlock contentList">
            <h2 class="greyHeader"><?php echo ucwords(str_replace('-', ' ', $postTypeName)); ?></h2>

            <?php 
                $itemCount = $mysqli->query("SELECT * FROM `posts` WHERE post_type_id = {$postTypeId}")->num_rows;
                $pagination = new pagination($itemCount);
                $pagination->prefix = explode('/page-', $_SERVER['REQUEST_URI'])[0] . '/';
                $pagination->load();
                
                $searchTerm = (isset($_GET['search']) ? $_GET['search'] : '');
                   
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
                        WHERE posts.post_type_id = {$postTypeId} AND (posts.name LIKE '%{$searchTerm}%' OR posts.url LIKE '%{$searchTerm}%' OR categories.name LIKE '%{$searchTerm}%' OR posts.author LIKE '%{$searchTerm}%') 
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