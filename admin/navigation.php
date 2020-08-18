<?php require_once('includes/header.php'); ?>

<?php 
	if(!isset($_GET['id'])) {
		http_response_code(301);
		header('Location: ' . ROOT_DIR . 'admin/navigation/0');
		exit();
	}
	
	if($_GET['id'] != 0) {
		$checkMenu = $mysqli->prepare("SELECT COUNT(*) FROM `navigation_menus` WHERE id = ?");
		$checkMenu->bind_param('i', $_GET['id']);
		$checkMenu->execute();
		$result = $checkMenu->get_result()->fetch_row();

		if($result[0] == 0) {
			header('Location: ' . ROOT_DIR . 'admin/navigation/0');
			exit();
		}
	}
?>

<div class="container-fluid d-block d-xl-flex h-100">                    
    <div class="row flex-grow-1">
        <div class="col-xl-4 bg-light">
            <h2 class="py-2">Manage Navigation</h2>
			
			<form id="deleteMenu" action="<?php echo ROOT_DIR; ?>admin/scripts/manageNavigation.php" method="post">
                <input type="hidden" name="method" value="deleteMenu">
                <input type="hidden" name="returnurl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
				<div class="form-group">
					<label>Select Menu To Edit</label>
					
					<?php $menus = $mysqli->query("SELECT id, name FROM `navigation_menus` ORDER BY name ASC"); ?>
					
					<select class="form-control" name="selectMenu">
						<option value="0" <?php echo ($_GET['id'] == 0 ? 'selected' : ''); ?>>Main Menu</option>
						
						<?php if($menus->num_rows > 0) : ?>
							<?php while($menu = $menus->fetch_assoc()) : ?>
								<option value="<?php echo $menu['id']; ?>" <?php echo ($menu['id'] == $_GET['id'] ? 'selected' : ''); ?>><?php echo $menu['name']; ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				
				<?php if($_GET['id'] > 0) : ?>
					<div class="form-group d-flex align-items-center">
						<input type="submit" class="btn btn-danger" value="Delete Menu">
					</div>
				<?php endif; ?>
				
                <?php if(isset($_SESSION['deletemessage'])) : ?>
                    <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                        <?php echo $_SESSION['deletemessage']; ?>
                    </div>
                <?php endif; ?>
                
				<hr>
			</form>
				
			<form id="createMenu" action="<?php echo ROOT_DIR; ?>admin/scripts/manageNavigation.php" method="post">
				<input type="hidden" name="method" value="createMenu">
				<input type="hidden" name="returnurl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
				
				<div class="form-group">
					<label>Create New Menu</label>
					<input type="text" class="form-control" name="newMenu" required>
					<small class="text-muted">Code to display the menu must be added in the site files.</small>
				</div>
				
				<div class="form-group d-flex align-items-flex">
					<input type="submit" class="btn btn-primary" value="Create Menu">
				</div>
				
                <?php if(isset($_SESSION['createmessage'])) : ?>
                    <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                        <?php echo $_SESSION['createmessage']; ?>
                    </div>
                <?php endif; ?>
                
				<hr>
			</form>
			
			<form id="updateMenu" action="<?php echo ROOT_DIR; ?>admin/scripts/manageNavigation.php" method="post">
				<h3 class="py-2">Insert Into Menu</h3>
				
				<input type="hidden" name="method" value="updateMenu">
				<input type="hidden" name="returnurl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="menuId" value="<?php echo $_GET['id']; ?>">
				
				<?php $pages = $mysqli->query("SELECT `posts`.id, `posts`.name, `post_types`.name as post_type FROM `posts` LEFT OUTER JOIN `post_types` ON `post_types`.id = post_type_id ORDER BY post_type_id, name ASC"); ?>
				
				<div class="form-group">
					<label>Choose Existing Page</label>
					<select class="form-control" name="existing">
						<option value="">--Select Page--</option>
						
						<?php if($pages->num_rows > 0) : ?>
							<?php while($page = $pages->fetch_assoc()) : ?>
								<option value="<?php echo $page['id']; ?>"><?php echo ucwords(str_replace('-', ' ', $page['post_type'])) . ': ' . $page['name']; ?></option>
							<?php endwhile; ?>
						<?php endif; ?>
					</select>
				</div>
				
				<div class="form-group">
					<label>Name</label>
					<input type="text" class="form-control" name="name" required>
				</div>
				
				<div class="form-group">
					<label>URL Slug</label>
					<input type="text" class="form-control" name="url" required>
				</div>
				
				<div class="form-group imageUrl">
					<label>Image</label>
					<input type="hidden" name="imageUrl">
					
					<div class="clearfix mt-2"></div>
					<input type="button" class="btn btn-info mr-2" name="selectImage" value="Choose Image">
					<input type="button" class="btn btn-secondary mt-2 mt-sm-0" name="clearImage" value="Remove Image" style="display: none;">
				</div>
				
				<div class="form-group">
                    <?php 
                        $menuItems = $mysqli->prepare("SELECT id, name FROM `navigation_structure` WHERE menu_id = ? ORDER BY parent_id, position ASC"); 
                        $menuItems->bind_param('i', $_GET['id']);
                        $menuItems->execute();
                        $result = $menuItems->get_result();
                    ?>
                    
					<label>Choose Parent Item</label>
					<select class="form-control" name="parentId">
						<option value="0">No Parent</option>
                        
                        <?php if($result->num_rows > 0) : ?>
                            <?php while($item = $result->fetch_assoc()) : ?>
                                <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
					</select>
				</div>
				
				<div class="form-group d-flex align-items-center">
					<input type="submit" class="btn btn-primary" value="Insert Item">
				</div>
			</form>
        </div>

        <div class="col bg-white">
            <h2 class="py-2"><?php echo ($_GET['id'] == 0 ? 'Main Menu: ' : 'Other Menu: '); ?>Navigation Tree</h2>
			
			<div>
                <?php new navigationEditor($_GET['id']); ?>
			</div>
        </div>
    </div>
</div>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/manageNavigation.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php
    unset($_SESSION['status']); 
    unset($_SESSION['createmessage']); 
    unset($_SESSION['deletemessage']); 
?>