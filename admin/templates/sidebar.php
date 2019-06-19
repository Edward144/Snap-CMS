<div class="sidebar">    
    <div class="sidebarInner">
        <a class="sidebarLink" href="/admin/dashboard">Dashboard</a>
        
        <?php 
            $files = $mysqli->query("SELECT name, link FROM `admin_sidebar` WHERE type = 0 ORDER BY name ASC"); 
            $folders = $mysqli->query("SELECT name, link FROM `admin_sidebar` WHERE type = 1 ORDER BY name ASC"); 
        ?>
        
        <ul>
            <li><a class="sidebarLink" href="/admin/pages">Pages</a></li>
            <li><a class="sidebarLink" href="/admin/posts">Posts</a></li>
            <li><a class="sidebarLink" href="/admin/navigation">Navigation</a></li>
            <li><a class="sidebarLink" href="/admin/categories">Categories</a></li>
            <li><a class="sidebarLink" href="/admin/media">Media</a></li>
            
            <?php while($file = $files->fetch_assoc()) : ?>
                <li><a class="sidebarLink" href="/admin/<?php echo $file['link']; ?>"><?php echo ucwords(str_replace('_', ' ', $file['name'])); ?></a></li>
            <?php endwhile; ?>
        </ul>
        
        <?php
            while($folder = $folders->fetch_assoc()) {
                sidebarFolder($folder['name']);
            }
        
            sidebarFolder('appearance');
            sidebarFolder('settings');
        ?>
    </div>
</div>
