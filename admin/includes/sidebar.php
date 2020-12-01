<?php
    $navItems = [
        [
            'name' => 'Dashboard',
            'link' => '',
            'icon' => 'fa-tachometer-alt'
        ],
        [
            'name' => 'Navigation',
            'link' => 'navigation',
            'icon' => 'fa-directions'
        ],
		[
			'name' => 'Contact Forms',
			'link' => 'contact-forms',
			'icon' => 'fa-envelope-open'
		],
        [
            'name' => 'Users',
            'link' => 'manage-users',
            'icon' => 'fa-users'
        ],
        [
            'name' => 'Website Details',
            'link' => 'manage-website',
            'icon' => 'fa-address-card'
        ],
        [
            'name' => 'Settings',
            'link' => 'settings',
            'icon' => 'fa-cog'
        ]                        
    ];

    $postTypes = $mysqli->query("SELECT id, name FROM `post_types`"); 
    $i = 1;

    if($postTypes->num_rows > 0) {
        while($postType = $postTypes->fetch_assoc()) {
            $content = [    
                'name' => ucwords(str_replace('-', ' ', $postType['name'])),
                'link' => 'manage-content/' . strtolower(str_replace(' ', '-', $postType['name'])),
                'icon' => ($postType['name'] == 'pages' ? 'fa-book-open' : ($postType['name'] == 'posts' ? 'fa-newspaper' : 'fa-link')) 
            ];
                
            array_splice($navItems, $i, 0, array($content));
            
            $i++;
        }
    }
?>

<div class="sidebar bg-dark text-light">
    <ul class="nav flex-column">                    
        <li class="nav-item border-bottom">
            <a class="btn btn-dark" id="sidebarToggle"><span>Toggle Menu</span><span class="fa fa-bars"></span></a>
        </li>

        <?php foreach($navItems as $navItem) : ?>
            <li class="nav-item border-bottom border-secondary">
                <a href="<?php echo 'admin/' . $navItem['link']; ?>" class="btn btn-dark rounded-0"><span><?php echo $navItem['name']; ?></span><span class="fa <?php echo (isset($navItem['icon']) ? $navItem['icon'] : 'fa-link'); ?>"></span></a>
            </li>
        <?php endforeach; ?>
        
        <li class="nav-item border-bottom border-secondary">
            <a href="#" onclick="moxman.browse();" class="btn btn-info rounded-0"><span>Media Browser</span><span class="fa fa-image"></span></a>
        </li>
    </ul>
</div>