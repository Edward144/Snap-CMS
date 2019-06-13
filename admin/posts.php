<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">        
        <?php if(isset($_GET['p'])) : ?>
            <?php 
                $post = $mysqli->prepare("SELECT * FROM `posts` WHERE id = ?");
                $post->bind_param('i', $_GET['p']);
                $post->execute();
                $result = $post->get_result();
            ?>
        
            <?php if($result->num_rows > 0) : ?>
                <?php while($row = $result->fetch_assoc()) : ?>
                    <div class="post contentWrap">
                        <form id="editContent">
                            <div class="details">
                                <div class="left">
                                    <p style="display: none;">
                                        <span class="id"><?php echo $row['id']; ?></span>
                                    </p>
                                    
                                    <p>
                                        <label>Title: </label>
                                        <input type="text" name="title" value="<?php echo $row['name']; ?>">
                                    </p>
                                    
                                    <p>
                                        <label>Description: </label>
                                        <input type="text" name="description" value="<?php echo $row['description']; ?>">
                                    </p>
                                    
                                    <p>
                                        <label>Url: </label>
                                        <input type="text" name="url" value="<?php echo $row['url']; ?>">
                                    </p>
                                    
                                    <p>
                                        <label>Category: </label>
                                        <select name="categories">
                                            <option value="" selected>--Select Category--</option>
                                            
                                            <?php $categories = $mysqli->query("SELECT id, name FROM categories ORDER BY name ASC"); ?>
                                            <?php while($category = $categories->fetch_assoc()) : ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($row['category_id'] == $category['id'] ? 'selected' : ''); ?>>
                                                    <?php echo $category['name']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </p>
                                    
                                    <p class="message"></p>                  
                                </div>
                                
                                <div class="right">
                                    <p>
                                        <label>Author: </label>
                                        <select name="author">
                                            <option value="" selected disabled>--Select Author--</option>
                                            
                                            <?php $authors = $mysqli->query("SELECT username, first_name, last_name FROM `users` ORDER BY username ASC"); ?>
                                            <?php while($author = $authors->fetch_assoc()) : ?>
                                                <option value="<?php echo $author['username']; ?>" <?php echo ($author['username'] == $row['author'] ? 'selected' : ''); ?>><?php echo $author['username'] . ': ' . $author['first_name'] . ' ' . $author['last_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </p>
                                    
                                    <p>
                                        <label>Date Posted: </label>
                                        <input type="datetime-local" step="1" name="date" value="<?php echo str_replace(' ', 'T', $row['date_posted']); ?>">
                                    </p>
                                    
                                    <div class="actions">
                                        <?php if($row['visible'] == 1) : ?>
                                            <p class="icon" id="view"><img src="/admin/images/icons/view.png" alt="Visible"></p>
                                        <?php else : ?>
                                            <p class="icon" id="hide"><img src="/admin/images/icons/hide.png" alt="Hidden"></p>
                                        <?php endif; ?>

                                        <p class="icon" id="apply"><img src="/admin/images/icons/check.png" alt="Save Changes"></p>
                                        <p class="icon" id="delete"><img src="/admin/images/icons/bin.png" alt="Delete"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="editor">
                                <textarea name="content"><?php echo $row['content']; ?></textarea>
                            </div>
                            
                            <div class="featuredImage">
                                <h2>Featured Image</h2>
                                
                                <?php if($row['image_url'] == null || $row['image_url'] == '') : ?>
                                    <div class="noFeatured featuredInner">
                                        <span>Select Image</span>
                                    </div>
                                <?php else: ?>
                                    <div class="featuredInner">
                                        <span class="featuredDelete"><span>X</span></span>
                                        
                                        <img src="/admin/useruploads/Citizen_AT4127-52H.jpg" id="featuredImage">
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                    if(isset($_GET['f'])) {
                                        new mediaTree($_GET['f'], true); 
                                    }
                                    else {
                                        new mediaTree('useruploads', true);
                                    }
                                ?>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <h1>Post <?php echo $_GET['p']; ?> does not exist</h1>
            <?php endif; ?>
        
            <?php $post->close(); ?>
        <?php else : ?>
            <h1><?php adminTitle(); ?></h1>
            
            <?php 
                $postCount = $mysqli->query("SELECT COUNT(*) from `posts`")->fetch_array()[0];
                $pagination = new pagination($postCount);
                $pagination->load();
            ?>
        
            <div class="formBlock">
                <form id="addPost">
                    <p>
                        <input type="submit" value="Add Post">
                    </p>
                    
                    <p class="message"></p>
                </form>
                
                <form id="searchPost">
                    <p>
                        <input type="text" name="search" placeholder="Search..." id="<?php echo $pagination->itemLimit; ?>">
                    </p>
                </form>
            </div>
        
            <table>
                <tr class="headers">
                    <td style="width: 40px;">ID</td>
                    <td style="text-align: left;">Post Details</td>
                    <td style="width: 180px;">Published</td>
                    <td style="width: 100px;">Actions</td>
                </tr>
                
                <?php $posts = $mysqli->query("SELECT * FROM `posts` ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}"); ?>
                
                <?php if($posts->num_rows > 0) : ?>
                    <?php while($post = $posts->fetch_assoc()) : ?>
                        <tr class="postRow contentRow">
                            <td>
                                <span class="id"><?php echo $post['id']; ?></span>
                            </td>
                            
                            <td style="text-align: left;">
                                <h4><?php echo $post['name']; ?></h4>
                                <p><?php echo $post['description']; ?></p>
                                <p style="font-size: 0.75em;">URL: <?php echo $post['url']; ?></p>
                            </td>
                            
                            <td>
                                <p><?php echo $post['author']; ?></p>
                                <p><?php echo $post['date_posted']; ?></p>
                            </td>
                            
                            <td>
                                <?php if($post['visible'] == 1) : ?>
                                    <p class="icon" id="view"><img src="/admin/images/icons/view.png"></p>
                                <?php else : ?>
                                    <p class="icon" id="hide"><img src="/admin/images/icons/hide.png"></p>
                                <?php endif; ?>
                                
                                <p class="icon" id="edit"><img src="/admin/images/icons/edit.png"></p>
                                <p class="icon" id="delete"><img src="/admin/images/icons/bin.png"></p>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">There are currently no posts.</td>
                    </tr>
                <?php endif; ?>
            </table>
            
            <?php $pagination->display(); ?>
        <?php endif;?>
    </div>

    <script src="settings/scripts/postPage.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>