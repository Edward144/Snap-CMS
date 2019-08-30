<div class="sidebar">    
    <div class="sidebarInner">
        <a class="sidebarLink" href="/admin/dashboard">Dashboard</a>
        
        <?php $editors = $mysqli->query("SELECT * FROM `custom_posts` ORDER BY name ASC"); ?>
        
        <ul>
            <li><a class="sidebarLink" href="/admin/editor/pages">Pages</a></li>
            <li><a class="sidebarLink" href="/admin/editor/posts">Posts</a></li>
            
            <?php if($editors->num_rows > 0) : ?>
                <?php while($editor = $editors->fetch_assoc()) : ?>
                    <li><a href="/admin/editor/<?php echo $editor['name']; ?>s"><?php echo ucwords(str_replace('_', ' ', $editor['name'])); ?>s</a></li>
                <?php endwhile; ?>
            <?php endif; ?>
            
            <li><a class="sidebarLink" href="/admin/categories">Categories</a></li>
            <li><a class="sidebarLink" href="/admin/navigation">Navigation</a></li>
            <li><a class="sidebarLink" href="/admin/media">Media</a></li>
            <li><a class="sidebarLink" href="/admin/banners">Sliding Banners</a></li>
        </ul>
        
        <?php
            sidebarFolder('settings');
        ?>
    </div>
</div>
