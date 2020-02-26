<?php require_once('includes/header.php'); ?>

<?php 
    if(!isset($_GET['menu']) || $_GET['menu'] < 0 || !is_numeric($_GET['menu'])) {
        header('Location: ' . ROOT_DIR . 'admin/navigation/0');
        exit();
    }

    $menuId = $mysqli->query("SELECT id, name FROM `navigation_menus` WHERE id = '{$_GET['menu']}'");
    
    if($menuId->num_rows <= 0 && $_GET['menu'] != 0) {
        header('Location: ' . ROOT_DIR . 'admin/navigation/0');
        exit();
    }
    elseif($_GET['menu'] == 0) {
        $menuId = 0;
        $menuName = 'Main Menu';
    }
    else {
        $menu = $menuId->fetch_assoc();
        $menuId = $menu['id'];
        $menuName = $menu['name'];
    }
?>

<div class="flexContainer" id="navigationManager">    
    <div class="column column-30 formBlock navigationDetails">
        <h2 class="greyHeader">Navigation Controls</h2>
        
        <div>
            <form id="changeMenu" method="POST" action="../scripts/deleteNavigation.php">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <p>
                    <label>Manage Menu </label>
                    <select name="menus">
                        <option value="0" <?php echo ($menuId == 0 ? 'selected' : ''); ?>>Main Menu</option>
                        <?php $menus = $mysqli->query("SELECT id, name FROM `navigation_menus` ORDER BY name ASC"); ?>
                        <?php while($row = $menus->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $menuId ? 'selected' : ''); ?>><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </p>
                
                <?php if($menuId != 0) : ?>
                    <input type="submit" value="Delete Menu" name="deleteMenu" class="redButton">
                    <p id="message" class="deletemessage"><?php if(isset($_SESSION['deletemessage'])) {
                    echo $_SESSION['deletemessage'];
                    unset($_SESSION['deletemessage']); 
                }?></p>
                <?php endif; ?>
            </form>
            
            <hr>
            
            <form id="createMenu" method="POST" action="../scripts/createNavigation.php">
                <h3>Create New Menu</h3>
                
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <p>
                    <label>Name</label>
                    <input type="text" name="menuName">
                </p>
                
                <input type="submit" value="Submit">
                
                <p id="message" class="createmessage"><?php if(isset($_SESSION['createmessage'])) {
                    echo $_SESSION['createmessage'];
                    unset($_SESSION['createmessage']); 
                }?></p>
            </form>
            
            <hr>
            
            <form id="addItem" method="POST" action="../scripts/addNavItem.php">
                <h3></h3>
                
                <input type="hidden" name="menuId" value="<?php echo $menuId; ?>">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <p>
                    <label>Choose Existing Post</label>
                    <select name="posts">
                        <option>--Select Post--</option>
                        <?php 
                            $posts = $mysqli->query("
                                SELECT posts.name, posts.url, post_types.name AS post_type, posts.post_type_id FROM `posts` AS posts 
                                LEFT OUTER JOIN `post_types` AS post_types ON posts.post_type_id = post_types.id
                                ORDER BY posts.post_type_id, posts.name ASC
                            "); ?>
                        
                        <?php if($posts->num_rows > 0) : ?>
                            <?php while($post = $posts->fetch_assoc()) : ?>
                                <option data-name="<?php echo $post['name']; ?>" data-url="<?php echo $post['url']; ?>" data-type="<?php echo $post['post_type']; ?>"><?php echo ucwords(str_replace('-', ' ', $post['post_type'])) . ': ' . $post['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </p>
                
                <p>
                    <label>Name</label>
                    <input type="text" name="itemName">
                </p>
                
                <p>
                    <label>URL Slug</label>
                    <input type="text" name="itemSlug">
                </p>
                
                <p>
                    <label>Image</label>
                    <input type="hidden" name="itemImage">
                    <input type="button" name="imageSelector" value="Choose File" style="padding: 0.5em;">
                </p>
                
                <p>
                    <label>Parent Item</label>
                    <select name="itemParent">
                        <option value="0">No Parent</option>
                        
                        <?php $menus = $mysqli->query("SELECT id, name FROM `navigation_structure` WHERE level < 2 AND menu_id = {$menuId} ORDER BY id ASC"); ?>
                        <?php if($menus->num_rows > 0) : ?>
                            <?php while($row = $menus->fetch_assoc()) : ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </p>
                
                <input type="submit" value="Add Item">
                
                <p id="message" class="addmessage"><?php if(isset($_SESSION['addmessage'])) {
                    echo $_SESSION['addmessage'];
                    unset($_SESSION['addmessage']); 
                }?></p>
            </form>
        </div>
    </div>
    
    <div class="column column-70 formBlock navigationTreeWrap">
        <h2 class="greyHeader"><?php echo $menuName; ?>: Navigation Tree</h2>
        
        <div>
            <?php new navigationEditor($menuId); ?>
        </div>
    </div>
</div>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/navigation.js"></script>

<?php require_once('includes/footer.php'); ?>