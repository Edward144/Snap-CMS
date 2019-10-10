<?php require_once('includes/header.php'); ?>

<?php $postTypes = $mysqli->query("SELECT name FROM `post_types` ORDER BY name ASC"); ?>
<?php if($postTypes->num_rows > 0) : ?>
    <div class="dashboardBlocks">
        <?php while($row = $postTypes->fetch_assoc()) : ?>
            <?php 
                $postCount = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE post_type = '{$row['name']}'");
                
                if($postCount == false ? $postCount = 0 : $postCount = $postCount->fetch_array()[0]);
        
                //$posts = $mysqli->query("SELECT name, author, last_edited FROM `posts` WHERE post_type = '{$row['name']}' ORDER by last_edited LIMIT 5"); 
            ?>
        
            <div class="dashboardBlock" id="post-type">
                <div class="counter">
                    <h2><span class="count">
                        <?php echo $postCount; ?></span> <?php echo ucwords(str_replace('-', ' ', $row['name'])); ?></h2>
                </div>
                
                <?php if($postCount > 0)  :?>
                <div class="postList">
                    <h3>Latest Edits:</h3>
                    <ul>
                        <li><strong>Page Name</strong><br> 01/01/2019 09:00 by Admin</li>
                        <li><strong>Page Name</strong><br> 01/01/2019 09:00 by Admin</li>
                        <li><strong>Page Name</strong><br> 01/01/2019 09:00 by Admin</li>
                        <li><strong>Page Name</strong><br> 01/01/2019 09:00 by Admin</li>
                        <li><strong>Page Name</strong><br> 01/01/2019 09:00 by Admin</li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>