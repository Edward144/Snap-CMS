<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <div class="dashboardBlocks">
            <div id="totalPages">
                <?php 
                    $pageCount = $mysqli->query("SELECT COUNT(*) FROM `pages`")->fetch_array()[0]; 
                    $pageLatest = $mysqli->query("SELECT name, date_posted FROM `pages` ORDER BY id DESC LIMIT 5");
                ?>
                
                <div class="totalHeader">
                    <span>
                        <h2><?php echo $pageCount; ?></h2>
                        <h4>Pages</h4>
                    </span>
                </div>
                
                <?php if($pageLatest->num_rows > 0) : ?>
                    <div class="latest">
                        <h4>Latest</h4>
                        
                        <?php $i = 1; while($page = $pageLatest->fetch_assoc()) : ?>
                            <p><strong><?php echo $i++; ?>. </strong><?php echo $page['name']; ?> (<?php echo $page['date_posted']; ?>)</p>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div id="totalPosts">
                <?php 
                    $postCount = $mysqli->query("SELECT COUNT(*) FROM `posts`")->fetch_array()[0];
                    $postLatest = $mysqli->query("SELECT name, date_posted FROM `posts` ORDER BY id DESC LIMIT 5");
                ?>
                
                <div class="totalHeader">
                    <span>
                        <h2><?php echo $postCount; ?></h2>
                        <h4>Posts</h4>
                    </span>
                </div>
                
                <?php if($postLatest->num_rows > 0) : ?>
                    <div class="latest">
                        <h4>Latest</h4>
                        
                        <?php $i = 1; while($post = $postLatest->fetch_assoc()) : ?>
                            <p><strong><?php echo $i++; ?>. </strong><?php echo $post['name']; ?> (<?php echo $post['date_posted']; ?>)</p>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php /* <div id="totalComments">
                <?php 
                    //$commentCount = $mysqli->query("SELECT COUNT(*) FROM `comments`")->fetch_array()[0];
                    //$commentLatest = $mysqli->query("SELECT name, date_posted FROM `comments` ORDER BY id DESC LIMIT 5");
                ?>
                
                <div class="totalHeader">
                    <span>
                        <h2><?php //echo $commentCount; ?>0</h2>
                        <h4>Comments</h4>
                    </span>
                </div>
                
                <?php /*if($commentLatest->num_rows > 0) : ?>
                    <div class="latest">
                        <h4>Latest</h4>
                        
                        <?php $i = 1; while($comment = $commentLatest->fetch_assoc()) : ?>
                            <p><strong><?php echo $i++; ?>. </strong><?php echo $comment['name']; ?> (<?php echo $comment['date_posted']; ?>)</p>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div> */ ?>
        </div>
    </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>