<?php require_once('includes/header.php'); ?>

<?php 
    if(!isset($_GET['post-type']) || $_GET['post-type'] == '') {
        header('Location: ' . ROOT_DIR . 'admin/categories/posts');
        exit();
    }

    $postTypeId = $mysqli->query("SELECT id, name FROM `post_types` WHERE name = '{$_GET['post-type']}'");
    
    if($postTypeId->num_rows <= 0) {
        header('Location: ' . ROOT_DIR . 'admin/categories/posts');
        exit();
    }
    else {
        $postTypeId = $postTypeId->fetch_assoc()['id'];
    }
?>

<div class="flexContainer" id="categoriesManager">    
    <div class="column column-30 formBlock categoryDetails">
        <h2 class="greyHeader">Category Controls</h2>
        
        <div>
            <form id="changeCategory">
                <p>
                    <label>Manage Categories For </label>
                    <select name="postType">
                        <?php $postTypes = $mysqli->query("SELECT id, name FROM `post_types` ORDER BY name ASC"); ?>
                        <?php while($row = $postTypes->fetch_assoc()) : ?>
                            <option value="<?php echo $row['name']; ?>" <?php echo ($row['id'] == $postTypeId ? 'selected' : ''); ?>><?php echo ucwords(str_replace('-', ' ', $row['name'])); ?></option>
                        <?php endwhile; ?>
                    </select>
                </p>
            </form>
            
            <hr>
            
            <form id="createCategory" method="POST" action="<?php echo ROOT_DIR; ?>admin/scripts/createCategory.php">
                <h3>Create New Category</h3>
                
                <input type="hidden" name="postTypeId" value="<?php echo $postTypeId; ?>">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <p>
                    <label>Name </label>
                    <input type="text" name="catName">
                </p>
                
                <p>
                    <label>Description </label>
                    <input type="text" name="catDesc">
                </p>
                
                <p>
                    <label>Image </label>
                    <input type="hidden" name="catImage">
                    <input type="button" name="imageSelector" value="Choose File" style="padding: 0.5em;">
                </p>
                
                <p>
                    <label>Parent Category </label>
                    <select name="catParent">
                        <option value="0" selected>No Parent</option>
                        
                        <?php $categories = $mysqli->query("SELECT id, name FROM `categories` WHERE level < 3 AND post_type_id = {$postTypeId} ORDER BY id ASC"); ?>
                        <?php if($categories->num_rows > 0) : ?>
                            <?php while($row = $categories->fetch_assoc()) : ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </p>
                
                <input type="submit" value="Create Category">
                
                <p id="message" class="createMessage"><?php if(isset($_SESSION['createmessage'])) {
                    echo $_SESSION['createmessage'];
                    unset($_SESSION['createmessage']); 
                }?></p>
            </form>
        </div>
    </div>
    
    <div class="column column-70 formBlock categoryTreeWrap">
        <h2 class="greyHeader"><?php echo ucwords(str_replace('-', ' ', $_GET['post-type'])); ?>: Category Tree</h2>
        
        <div>
            <?php new categoryEditor($postTypeId); ?>
        </div>
    </div>
</div>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/categories.js"></script>

<?php require_once('includes/footer.php'); ?>