<div class="navToggle" id="hidden"></div>

<nav id="adminNav">
    <ul>
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin">Dashboard</a>
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
            <a href="<?php echo ROOT_DIR; ?>admin/categories">Categories</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/navigation">Navigation</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/sliders">Sliders</a>
        </li>
        
        <li>
            <a href="<?php echo ROOT_DIR; ?>admin/media">Media</a>
        </li>
        
        <li class="hasChildren">
            <a>Settings</a>
            
            <ul class="subMenu">
                <li><a href="<?php echo ROOT_DIR; ?>admin/user-management">User Management</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/company-details">Company Details</a></li>
                <li><a href="<?php echo ROOT_DIR; ?>admin/general-settings">General Settings</a></li>
            </ul>
        </li>
    </ul>
</nav>