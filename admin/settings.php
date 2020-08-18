<?php require_once('includes/header.php'); ?>

<div class="container-fluid d-block d-xl-flex h-100">                    
    <div class="row flex-grow-1">
        <div class="col-md-4 bg-light">
            <h2 class="py-2">General Settings</h2>
            
            <form id="generalSettings" action="scripts/manageSettings.php" method="post">
                <input type="hidden" name="method" value="updateSettings">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <?php 
                    $settings = $mysqli->query("SELECT * FROM `settings` WHERE settings_name <> 'setup complete'");
                    $settingsArray = [];
                
                    if($settings->num_rows > 0) {
                        while($setting = $settings->fetch_assoc()) {
                            $settingsArray[$setting['settings_name']] = $setting['settings_value'];
                        }
                    }
                
                    $pages = $mysqli->query(
                        "SELECT pages.id, pages.name, post_types.name AS post_type, post_types.id AS post_type_id FROM `posts` AS pages
                        LEFT OUTER JOIN `post_types` ON `post_types`.id = pages.post_type_id
                        ORDER BY post_type_id, id ASC"
                    );
                ?>
                
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" name="hide posts" <?php echo ($settingsArray['hide posts'] == 1 ? 'checked' : ''); ?>>
                    <label for="hide posts">Hide Posts</label>
                    <small class="text-muted d-block" style="margin-left: -1.25rem;">Check this box to prevent access to news article pages, useful if you don't plan on posting news</small>
                </div>
                
                <div class="form-group">
                    <label for="homepage">Homepage</label>
                    
                    <select name="homepage" class="custom-select">
                        <option value="0" selected>-- No Homepage --</option>
                        <?php if($pages->num_rows > 0) : ?>
                            <?php while($page = $pages->fetch_assoc()) : ?>
                                <option value="<?php echo $page['id']; ?>" <?php echo ($page['id'] == $settingsArray['homepage'] ? 'selected' : ''); ?>><?php echo ucwords($page['post_type']) . ': ' . $page['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    
                    <small class="text-muted">Set the page or post you want to use as your homepage</small>
                </div>
                
                <div class="form-group">
                    <label for="google analytics">Google Analytics</label>
                    <input type="text" class="form-control" name="google analytics" placeholder="UA-123456678-9" value="<?php echo $settingsArray['google analytics']; ?>">
                    <small class="text-muted">Add your Google analytics tracking tag, if you have an set up an analytics account</small>
                </div>
                
                <div class="form-group d-flex align-items-center">
                    <input type="submit" class="btn btn-primary" value="Save Settings">
                </div>
                
                <?php if(isset($_SESSION['settingsmessage'])) : ?>
                    <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                        <?php echo $_SESSION['settingsmessage']; ?>
                    </div>    
                <?php endif; ?>
            </form>
        </div>

        <div class="col bg-white" style="max-width: 768px;">
            <h2 class="py-2">Custom Post Types</h2>
            
            <p>Custom post types work identically to pages or posts. They are essentially a new category of page, and are useful for differentiating the types of content shown on the website, by giving each custom post type it's own url.</p>
            
            <p>For example pages use the url structure <span class="badge badge-dark text-left" style="word-brake: brake-all; white-space: normal;">https://example.com/{page-name}</span>
            <br>Posts and custom post types use the url structure <span class="badge badge-dark text-left" style="word-brake: brake-all; white-space: normal;">https://example.com/{post-type-name}/{post-name}</span></p>
            
            <?php $customPosts = $mysqli->query("SELECT id, name FROM `post_types` WHERE name <> 'posts' AND name <> 'pages' AND id > 2 ORDER by name ASC"); ?>

            <?php if($customPosts->num_rows > 0) : ?>
                <ul class="list-group">
                    <?php while($type = $customPosts->fetch_assoc()) : ?>
                        <li class="list-group-item">
                            <form class="row" id="postTypes" action="scripts/manageSettings.php" method="post">
                                <div class="col-sm d-flex align-items-center">
                                    <h4 class="mb-0"><?php echo $type['name']; ?></h4>
                                </div>
                                
                                <div class="col-sm-4 d-flex align-items-center justify-content-end">
                                    <div class="form-group mb-0 ml-sm-auto ml-0 mr-sm-0 mr-auto mt-sm-0 mt-2 d-flex align-items-center">
                                        <input type="hidden" name="method" value="deletePostType">
                                        <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $type['id']; ?>">
                                        
                                        <input type="submit" class="btn btn-danger" value="Delete Post Type">
                                    </div>
                                </div>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <h3 class="alert alert-info my-3">You have not created any custom post types</h3>               
            <?php endif; ?>
            
            <?php if(isset($_SESSION['custommessage'])) : ?>
                <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?> mt-3">
                    <?php echo $_SESSION['custommessage']; ?>
                </div> 
            <?php endif; ?>
            
            <h3 class="py-2">Create New Post Type</h2>
            
            <form id="createPostType" action="scripts/manageSettings.php" method="post">
                <input type="hidden" name="method" value="createPostType">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <div class="form-group">
                    <label for="newType">Name</label>
                    <input type="text" class="form-control" name="newType" required>
                    <small class="text-muted">Post Type names should only contain upper and lower case letters, numbers, and spaces or hyphens. Any other characters will be automatically deleted.</small>
                </div>
                
                <div class="form-group d-flex align-items-center">
                    <input type="submit" class="btn btn-primary" value="Create Post Type">
                </div>
                
                <?php if(isset($_SESSION['createmessage'])) : ?>
                    <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                        <?php echo $_SESSION['createmessage']; ?>
                    </div> 
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="scripts/manageSettings.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php
    unset($_SESSION['settingsmessage']);
    unset($_SESSION['custommessage']);
    unset($_SESSION['createmessage']);
    unset($_SESSION['status']);
?>