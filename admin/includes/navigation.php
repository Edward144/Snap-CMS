<div class="navToggle" id="hidden"></div>

<nav id="adminNav">
    <ul>
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/' ? 'id="active"' : ''); ?>>Dashboard</a>
        </li>
        
        <li class="hasChildren">
            <a>Content Manager</a>
            
            <?php 
                $postEditors = $mysqli->query("SELECT name FROM `post_types` ORDER BY id ASC"); 
                
                if($postEditors->num_rows > 0) : 
            ?>
            <ul class="subMenu">
                <?php while($row = $postEditors->fetch_assoc()) : ?>
                    <li><a href="#"><?php echo ucwords(str_replace('-', ' ', $row['name'])); ?></a></li>
                <?php endwhile; ?>
            </ul>
            <?php endif; ?>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/categories" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/categories' ? 'id="active"' : ''); ?>>Categories</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/navigation" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/navigation' ? 'id="active"' : ''); ?>>Navigation</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/sliders" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/sliders' ? 'id="active"' : ''); ?>>Sliders</a>
        </li>
        
        <li>
            <a id="mediaBrowser">Media Browser</a>
        </li>
        
        <li class="hasChildren">
            <a>Settings</a>
            
            <ul class="subMenu">
                <li><a href="<?php echo ROOT_DIR; ?>admin/user-management" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/user-management' ? 'id="active"' : ''); ?>>User Management</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/company-details" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/company-details' ? 'id="active"' : ''); ?>>Company Details</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/general-settings" <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR . 'admin/general-settings' ? 'id="active"' : ''); ?>>General Settings</a></li>
            </ul>
        </li>
    </ul>
</nav>