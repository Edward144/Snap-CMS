<?php require_once('includes/header.php'); ?>

<?php $postTypes = $mysqli->query("SELECT id, name FROM `post_types` ORDER BY name ASC"); ?>
<?php if($postTypes->num_rows > 0) : ?>
    <div class="dashboardBlocks">
        <?php while($row = $postTypes->fetch_assoc()) : ?>
            <?php 
                $postCount = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE post_type_id = '{$row['id']}'");
                
                $postCount = ($postCount == false ? 0 : $postCount->fetch_array()[0]);
        
                $posts = $mysqli->query("
                    SELECT posts.name, posts.last_edited, CONCAT(users.first_name, ' ', users.last_name) AS author FROM `posts` 
                    LEFT OUTER JOIN `users` ON posts.last_edited_by = users.id
                    WHERE post_type_id = '{$row['id']}' ORDER by last_edited DESC LIMIT 5
                ");
            ?>
        
            <div class="dashboardBlock" id="post-type">
                <div class="counter">
                    <h2>
                        <span class="count">
                            <?php echo $postCount; ?>
                        </span> 
                        <?php echo ($postCount == 1 ? rtrim(ucwords(str_replace('-', ' ', $row['name'])), 's') : ucwords(str_replace('-', ' ', $row['name']))); ?>
                    </h2>
                </div>
                
                <?php if($postCount > 0 && $posts->num_rows > 0)  :?>
                    <div class="postList">                        
                        <h3>Latest Edits:</h3>
                        <ul>
                            <?php while($row = $posts->fetch_assoc()) : ?>
                                <?php $row['author'] = ($row['author'] == null || $row['author'] == '' ? 'Unknown' : $row['author']); ?>
                                <li><strong><?php echo $row['name'];?></strong><br> <?php echo date('d/m/Y H:i', strtotime($row['last_edited'])) . ' by ' . $row['author']; ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>