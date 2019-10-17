<?php 
    $uri = explode('/category-', $_SERVER['REQUEST_URI'])[0];
    $uri = explode('/page-', $uri)[0];
    $uri = explode('/id-', $uri)[0];
?>

<div class="navToggle" id="hidden"></div>

<nav id="adminNav">
    <ul>
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/' ? 'id="active"' : ''); ?>>Dashboard</a>
        </li>
        
        <li class="hasChildren">
            <a href="<?php echo ROOT_DIR; ?>admin/content-manager/posts">Content Manager</a>
            
            <?php 
                $postEditors = $mysqli->query("SELECT name FROM `post_types` ORDER BY id ASC"); 
                
                if($postEditors->num_rows > 0) : 
            ?>
            <ul class="subMenu">
                <?php while($row = $postEditors->fetch_assoc()) : ?>
                    <li><a href="<?php echo ROOT_DIR; ?>admin/content-manager/<?php echo $row['name'];?>" <?php echo ($uri == ROOT_DIR . 'admin/content-manager/' . $row['name'] ? 'id="active"' : ''); ?>><?php echo ucwords(str_replace('-', ' ', $row['name'])); ?></a></li>
                <?php endwhile; ?>
            </ul>
            <?php endif; ?>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/categories" <?php echo (strpos($uri, ROOT_DIR . 'admin/categories') !== false ? 'id="active"' : ''); ?>>Categories</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/navigation" <?php echo (strpos($uri, ROOT_DIR . 'admin/navigation') !== false ? 'id="active"' : ''); ?>>Navigation</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/sliders" <?php echo (strpos($uri, ROOT_DIR . 'admin/sliders') !== false ? 'id="active"' : ''); ?>>Sliders</a>
        </li>
        
        <li>
            <a id="mediaBrowser">Media Browser</a>
        </li>
        
        <li class="hasChildren">
            <a href="<?php echo ROOT_DIR; ?>admin/general-settings">Settings</a>
            
            <ul class="subMenu">
                <li><a href="<?php echo ROOT_DIR; ?>admin/user-management" <?php echo ($uri == ROOT_DIR . 'admin/user-management' ? 'id="active"' : ''); ?>>User Management</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/company-details" <?php echo ($uri == ROOT_DIR . 'admin/company-details' ? 'id="active"' : ''); ?>>Company Details</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/general-settings" <?php echo ($uri == ROOT_DIR . 'admin/general-settings' ? 'id="active"' : ''); ?>>General Settings</a></li>
            </ul>
        </li>
    </ul>
</nav>