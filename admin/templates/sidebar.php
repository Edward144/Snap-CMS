<div class="sidebar">    
    <div class="sidebarInner">
        <a class="sidebarLink" href="/admin/dashboard">Dashboard</a>
        
        <ul>
            <li><a class="sidebarLink" href="/admin/pages">Pages</a></li>
            <li><a class="sidebarLink" href="/admin/posts">Posts</a></li>
            <li><a class="sidebarLink" href="/admin/navigation">Navigation</a></li>
            <li><a class="sidebarLink" href="/admin/categories">Categories</a></li>
            <li><a class="sidebarLink" href="/admin/media">Media</a></li>
        </ul>
        
        <?php 
            $settings = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/appearance/*');
            
            if($settings) :
        ?>
            <ul>
                <li class="sidebarCategory"><a href="" id="hidden">Appearance</a>
                    <ul class="sub">
                        <?php
                            foreach($settings as $setting) :
                                $setting = explode('/', $setting);
                                $settingCount = count($setting);
                                $setting = explode('.', $setting[$settingCount - 1])[0];
                        ?>

                            <?php if($setting != 'scripts') : ?>
                                <li><a class="sidebarLink" href="/admin/appearance/<?php echo strtolower($setting); ?>"><?php echo ucwords(str_replace('_', ' ', $setting)); ?></a></li>
                            <?php endif; ?> 
                        
                        <?php endforeach; ?>
                    </ul>    
                </li>
            </ul>
        <?php endif; ?>
        
        <?php 
            $settings = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/settings/*');
            
            if($settings) :
        ?>
            <ul>
                <li class="sidebarCategory"><a href="" id="hidden">Settings</a>
                    <ul class="sub">
                        <?php
                            foreach($settings as $setting) :
                                $setting = explode('/', $setting);
                                $settingCount = count($setting);
                                $setting = explode('.', $setting[$settingCount - 1])[0];
                        ?>

                            <?php if($setting != 'scripts') : ?>
                                <li><a class="sidebarLink" href="/admin/settings/<?php echo strtolower($setting); ?>"><?php echo ucwords(str_replace('_', ' ', $setting)); ?></a></li>
                            <?php endif; ?> 
                        
                        <?php endforeach; ?>
                    </ul>    
                </li>
            </ul>
        <?php endif; ?>
    </div>
</div>
