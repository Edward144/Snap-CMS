<?php

    //Global Variable
    $baseUrl = $_SERVER['DOCUMENT_ROOT'];
    $baseName = $_SERVER['SERVER_NAME'];

    //Check If Database Connect File Exists
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php') != 1) {
        session_destroy();
    }

    //Redirect If Setup Is Incomplete
    if($_SESSION['setupcomplete'] == 0 && $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] !=  $_SERVER['SERVER_NAME'] . '/setup/start') {        
        header('Location: /setup/start');
    }
    elseif($_SESSION['setupcomplete'] == 1 && $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ==  $_SERVER['SERVER_NAME'] . '/setup/start') {
        header('Location: /index');
    }
    
    //Redirect If Not Logged In
    if($_SESSION['loggedin'] == 1 && $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $_SERVER['SERVER_NAME'] . '/login') {
        header('Location: /admin/dashboard');
    }
    elseif($_SESSION['loggedin'] == 0 && $_SERVER['SERVER_NAME'] . '/' . explode('/', $_SERVER['REQUEST_URI'])[1] == $_SERVER['SERVER_NAME'] . '/admin')  {
        header('Location: /login');
    }
    
    //Convert Admin File Name to Page Name
    function adminTitle() {
        $pageTitle = explode('/', $_SERVER['REQUEST_URI']);
        $pageCount = count($pageTitle);
        $pageTitle = $pageTitle[$pageCount - 1];
        $pageTitle = str_replace('_', ' ', $pageTitle);
        $pageTitle = explode('?', $pageTitle)[0];
        $pageTitle = ucwords($pageTitle);
        
        echo $pageTitle;
    }

    //Convert String to URL Slug
    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    //Convert Bytes To Readable Units
    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        }
        else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
    
    //Register Folder To Admin Sidebar
    function sidebarFolder($directory) {
        $settings = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/' . $directory . '/*');

        if($settings) {
            echo 
                '<ul>
                    <li class="sidebarCategory"><a href="" id="hidden">' . ucwords(str_replace('_', ' ', $directory)) . '</a>
                        <ul class="sub">';

                            foreach($settings as $setting) {
                                $setting = explode('/', $setting);
                                $settingCount = count($setting);
                                $setting = explode('.', $setting[$settingCount - 1])[0];
                                
                                if($setting != 'scripts') {
                                    if(strpos($setting, 'custom_') === 0) {
                                        $setting = str_replace('custom_', '', $setting);
                                        
                                        if(strpos($setting, '_categories') !== false) {
                                            echo 
                                                '<li>
                                                    <a class="sidebarLink" href="/admin/categories/' . strtolower(str_replace('_categories', '', $setting)) . '">' .
                                                        ucwords(str_replace('_', ' ', $setting)) .     
                                                    '</a>
                                                </li>';
                                        }
                                        else {
                                            echo 
                                                '<li>
                                                    <a class="sidebarLink" href="/admin/' . $directory . '/' . strtolower('custom_' . $setting) . '">' .
                                                        ucwords(str_replace('_', ' ', $setting)) .     
                                                    '</a>
                                                </li>';
                                        }
                                    }
                                    else {
                                        echo 
                                            '<li>
                                                <a class="sidebarLink" href="/admin/' . $directory . '/' . strtolower($setting) . '">' .
                                                    ucwords(str_replace('_', ' ', $setting)) .     
                                                '</a>
                                            </li>';
                                    }
                                }
                            }
            echo
                        '</ul>
                    </li>
                </ul>';
        }
    }
    
    //Classes
    class pagination {
        public $firstPage = 1;
        public $lastPage;
        public $currentPage;
        public $itemLimit = 10;
        public $pageLimit = 9;
        public $showFirst = true;
        public $showLast = true;
        public $showNext = true;
        public $showPrev = true;
        public $showPageNumbers = true;
        public $i;
        public $offset;
        public $items = 0;
        public $prefix = '';
        
        function __construct($last) {
            if($last != null) {
                $this->lastPage = $last;
                $this->items = $last;
            }
            
            if(isset($_GET['category']) && !isset($_GET['page'])) {
                $this->prefix = $_SERVER['REQUEST_URI'] . '/';
            }
        }
        
        function setFirstPage($page = 1) {
            $this->firstPage = $page;
        }
        
        function setLastPage($page = 1) {
            $this->lastPage = $page;
        }
        
        function setItemLimit($limit = 10) {
            if($limit < 1) {
                $limit = 1;
            }
            
            $this->itemLimit = $limit;
        }
        
        function setPageLimit($limit = 9) {
            if($limit < 1) {
                $limit = 1;
            }
            
            $this->pageLimit = $limit;
        }
        
        function showPageNumbers($show = true) {
            $this->showPageNumbers = $show;
        }
        
        function showFirstLast($show = true) {
            $this->showFirst = $show;
            $this->showLast = $show;
        }
        
        function showNextPrevious($show = true) {
            $this->showNext = $show;
            $this->showPrev = $show;
        }
        
        function load() {
            if(isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] != null) {
                $this->currentPage = $_GET['page'];
            }
            else {
                $this->currentPage = $this->firstPage;
            }
            
            if($this->currentPage < $this->firstPage) {
                $this->currentPage = $this->firstPage;
            }
            
            if($this->currentPage > $this->pageLimit) {
                $this->i = $this->currentPage - $this->pageLimit;
            }
            else {
                $this->i = $this->firstPage;
            }
            
            if($this->lastPage != null) {
                $this->lastPage = ceil($this->lastPage / $this->itemLimit);
            }
            
            if(isset($_GET['page']) && $_GET['page'] > $this->lastPage) {
                $this->currentPage = $this->lastPage;
            }
            
            if($this->currentPage <= $this->firstPage) {
                $this->showFirst = false;
                $this->showPrev = false;
            }
            
            if($this->currentPage >= $this->lastPage) {
                $this->showLast = false;
                $this->showNext = false;
            }
            
            $this->offset = ($this->currentPage * $this->itemLimit) - $this->itemLimit;
        }
        
        function display() {
            if(($this->lastPage != null) && ($this->items > $this->itemLimit)) {
                if($this->currentPage <= $this->firstPage) {
                    $prevPage = $this->firstPage;
                }
                else {
                    $prevPage = $this->currentPage - 1;
                }
                
                if($this->currentPage >= $this->lastPage) {
                    $nextPage = $this->lastPage;
                }
                else {
                    $nextPage = $this->currentPage + 1;
                }
                
                $output = '<div class="pagination">';
                
                    if($this->showFirst == true) {
                        $output .= '<a href="' . $this->prefix . 'page-' . $this->firstPage . '"><< First</a>';
                    }
                
                    if($this->showPrev == true) {
                        $output .= '<a href="' . $this->prefix . 'page-' . $prevPage . '">< Prev</a>';
                    }
                
                    if($this->showPageNumbers == true) {
                        $end = $this->currentPage + $this->pageLimit;
                        
                        if($end >= $this->lastPage) {
                            $end = $this->lastPage;
                        }
                        
                        if($this->i <= $this->firstPage) {
                            $this->i = $this->firstPage;
                        }
                        
                        for($this->i; $this->i <= $end; $this->i++) {
                            $output .= '<a href="' . $this->prefix . 'page-' . $this->i . '">' . $this->i . '</a>';
                        }
                    }
                
                    if($this->showNext == true) {
                        $output .= '<a href="' . $this->prefix . 'page-' . $nextPage . '">Next ></a>';
                    }
                
                    if($this->showLast == true) {
                        $output .= '<a href="' . $this->prefix . 'page-' . $this->lastPage . '">Last >></a>';
                    }
                
                $output .= '</div>';
                
                return $output;
            }
        }
        
        function debug() {
            echo 'First Page: ' . $this->firstPage . '<br>' . 
                 'Last Page: ' . $this->lastPage . '<br>' . 
                 'Current Page: ' . $this->currentPage . '<br>' . 
                 'Items Per Page: ' . $this->itemLimit . '<br>' . 
                 'Page Numbers to Display: ' . $this->pageLimit . '<br>' . 
                 'Offset: ' . $this->offset . '<br>' . 
                 'Integer: ' . $this->i . '<br>' .
                 'Show First: ' . $this->showFirst . '<br>' . 
                 'Show Last: ' . $this->showLast . '<br>';
        }
    }

    class navigation {        
        public function __construct($parent = 0, $level = 0) {            
            $this->createLevel($parent, $level);
        }
        
        public function createLevel($parent, $level) {            
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parent} ORDER BY position ASC");
            $hidePosts = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'hide_posts'")->fetch_array()[0];
            $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0];
            
            echo '<ul class="level' . $level . '">';
                
                if($level == 1) {
                    echo '<div class="levelInner">';
                    
                    $info = $mysqli->query("SELECT nav_name, image FROM `navigation` WHERE custom_id = {$parent}")->fetch_assoc();
                    
                    if($info['image'] != '' && $info['image'] != null) {
                        echo 
                            '<div class="navImage">
                                <h2>' . $info['nav_name'] . '</h2>
                                <img src="' . $info['image'] . '">
                            </div>';
                    }
                }
                
                while($item = $items->fetch_assoc()) {
                    $itemInfo = $mysqli->query("SELECT * FROM `pages` WHERE id = {$item['page_id']}")->fetch_assoc();
                        $name = $itemInfo['name'];
                        $url = 'post-type/pages/' . $itemInfo['url'];
                        $visible = $itemInfo['visible'];
                        
                    $customUrl = $item['custom_url'];
                    
                    if($item['page_id'] == -1) {
                        if($customUrl == '' || $customUrl == null) {
                            $customUrl = '/post-type/posts';
                        }
                        
                        $name = 'Posts';
                    }
                    
                    if($customUrl != '' && $customUrl != null) {
                        $url = $customUrl;                          
                    }
                    else {
                        $url = '/' . $url;
                    }
                    
                    if(($item['page_id'] == 0 && $item['custom_url'] != '' && $item['custom_url'] != '') || ($item['page_id'] = -1 && $hidePosts != '1' && $homepage != '') || ($visible == 1)) {
                        echo '<li>';
                            echo '<a href="' . $url . '">';
                                if($item['nav_name'] != null) {
                                    echo ucwords($item['nav_name']); 
                                }
                                else {
                                    echo $name;
                                } 
                            echo '</a>';
                            $this->checkChildren($item['custom_id'], $item['level']);
                        echo '</li>';
                    }
                }
            
            if($level == 1) {
                echo '</div>';
            }
            
            echo '</ul>';
        }
        
        public function checkChildren($parent, $level) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parent}");
            
            if($items->num_rows > 0) {
                new navigation($parent, $level + 1);
            }
        }
    }

    class navigationTree {
        public function __construct($level = 0, $parent = 0, $prevLevel = 0) {
            $this->createLevel($level, $parent, $prevLevel);
        }
        
        public function createLevel($level, $parent, $prevLevel) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parent} AND level = {$level} ORDER BY position ASC");
            
            if($items->num_rows > 0) {
                echo '<ul class="level navEditor" id="level' . $level . '">';
                    while($item = $items->fetch_assoc()) {
                        $itemInfo = $mysqli->query("SELECT * FROM `pages` WHERE id = {$item['page_id']}")->fetch_assoc();
                            $id = $itemInfo['id'];
                            $name = $itemInfo['name'];
                            $visible = $itemInfo['visible'];
                                                   
                        echo
                            '<li class="pageSelector">
                                <p>
                                    <select name="pages" style="width: 200px;">
                                        <option value="" selected disabled>--Select Item--</option>';
                                        $pages = $mysqli->query("SELECT * FROM `pages`");
                                        while($page = $pages->fetch_assoc()) {
                                            echo '<option value="' . $page['id'] . '"' . ($page['id'] == $item['page_id'] ? 'selected' : '') . '>' . $page['name'] . '</option>';
                                        }
                                    echo '<option value="0"' . ($item['page_id'] == 0 ? 'selected' : '') . '>Custom URL</option>
                                        <option value="-1"' . ($item['page_id'] == -1 ? 'selected' : '') . '>Posts Page</option>
                                    </select>

                                    <input type="text" name="customNav" placeholder="Custom Name" style="width: 200px;" value="' . $item['nav_name'] . '">
                                    
                                    <input type="text" name="customUrl" placeholder="Custom URL" style="width: 200px;" ' . ($item['custom_url'] != '' && $item['custom_url'] != null ? 'value="' . $item['custom_url'] . '"' : '') . '>

                                    <input type="button" class="badButton" style="min-width: 0; width: 24px; height: 24px; border-radius: 100%;" value="X" name="delete">
                                </p>';
                            $this->checkChildren($item['custom_id'], $item['level']);

                            echo '</li>';
                    }
                    
                    echo 
                        '<li id="levelAddition">
                            <p>
                                <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                            </p>
                        </li>';
                echo '</ul>';
            }
            else {
                echo 
                    '<ul class="level navEditor" id="level' . $level . '">
                        <li id="levelAddition">
                            <p>
                                <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                            </p>
                        </li>
                    </ul>';
            }
            
            if($level == 0) {
                echo '<p>
                        <input type="submit" value="Update Navigation">
                    </p>

                    <p class="message"></p>';
            
            
                //Add Hidden Selector To Be Used For Copying
                echo 
                    '<div id="pageSelectorMain" style="display: none;">
                        <li class="pageSelector">
                            <p>
                                <select name="pages" style="width: 200px;">
                                    <option value="" selected disabled>--Select Item--</option>';
                                    $pages = $mysqli->query("SELECT * FROM `pages`");
                                    while($page = $pages->fetch_assoc()) {
                                        echo '<option value="' . $page['id'] . '">' . $page['name'] . '</option>';
                                    }
                            echo '<option value="0">Custom URL</option>
                                 <option value="-1">Posts Page</option>
                                </select>

                                <input type="text" name="customNav" placeholder="Custom Name" style="width: 200px;">
                                
                                <input type="text" name="customUrl" placeholder="Custom URL" style="width: 200px;">
                                
                                <input type="button" class="badButton" style="min-width: 0; width: 24px; height: 24px; border-radius: 100%;" value="X" name="delete">
                            </p>

                            <ul class="level navEditor" id="level1">
                                <li id="levelAddition">
                                    <p>
                                        <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                                    </p>
                                </li>
                            </ul>
                        </li>
                    </div>
                ';
            }
        }
        
        public function checkChildren($parent, $level) {
            $mysqli = $GLOBALS['mysqli'];
            $level++;
            
            $children = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parent} AND level = {$level}");
            
            if($children->num_rows > 0) {
                new navigationTree($level, $parent, ($level - 1));
            }
            else {
                echo 
                    '<ul class="level navEditor" id="level' . $level . '">
                        <li id="levelAddition">
                            <p>
                                <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                            </p>
                        </li>
                    </ul>';
            }
        }
    }

    class dashboardBlock {
        public $postType;
        public $blockId;

        public function __construct($type, $id = '') {
            if($type == null) {
                echo 'No Post Type set.';

                exit();
            }
            else {
                $this->postType = strtolower($type);
            }

            if($id == null) {
                $this->blockId = 'total' . ucfirst($this->postType);
            }
            else {
                $this->blockId = $id;
            }

            $this->displayBlock($this->postType, $this->blockId);
        }

        private function displayBlock($type, $id) {
            $mysqli = $GLOBALS['mysqli'];
            $database = $GLOBALS['database'];

            //Check Tables Exist
            $checkTable = $mysqli->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
            $checkTable->bind_param('ss', $database, $type);
            $checkTable->execute();
            $checkResult = $checkTable->get_result();
            
            $missingTable = false;

            if($checkResult->fetch_array()[0] == 0) {
                $tableError .= 'Error (' . $type . '): ' . $tableName . ' does not exist.<br>';

                $missingTable = true;
            }

            if($missingTable == false) {
                $postCount = $mysqli->query("SELECT COUNT(*) FROM `{$type}`")->fetch_array()[0];
                $postLatest = $mysqli->query("SELECT name, date_posted FROM `{$type}` ORDER BY id DESC LIMIT 5");

                $output =
                    '<div id="' . $id . '">
                        <div class="totalHeader">
                            <span>
                                <h2>' . $postCount . '</h2>
                                <h4>' . ucwords(str_replace('_', ' ', $type)) . '</h4>
                            </span>
                        </div>';

                        if($postLatest->num_rows > 0) {
                            $output .= 
                                '<div class="latest">
                                    <h4>Latest</h4>';

                                    $i = 1; 

                                    while($post = $postLatest->fetch_assoc()) {
                                        $output .=
                                            '<p>
                                                <strong>' . $i++ . '. </strong>' . $post['name'] . ' (' . $post['date_posted'] . ')
                                            </p>';
                                    }
                            $output .= 
                                '</div>';
                        }
                $output .= 
                    '</div>';
            }
            else {
                $output =
                    '<div id="' . $id . '">'
                        . $tableError .
                    '</div>';
            }
            
            echo $output;
        }
    }

    class bannerAdmin {
        public $postType;
        public $postId;
        public $postTitle;
        public $categoryPre;

        public function __construct() {
            if(isset($_GET['p'])) {
                $this->getBannerSingle();
            }
            else {
                $this->getBannerList();
            }
        }

        public function getBannerList() {
            $mysqli = $GLOBALS['mysqli'];

            echo '<h1>Sliding Banners</h1>';

            $bannerCount = $mysqli->query("SELECT COUNT(*) FROM `banners`")->fetch_array()[0];
            $pagination = new pagination($bannerCount);
            $pagination->prefix = '/admin/banners/';
            $pagination->load();

            echo
                '<div class="formBlock">
                    <form class="addContent" id="addBanner">
                        <p>
                            <input type="submit" value="New Banner">
                        </p>

                        <p class="message"></p>
                    </form>

                    <form id="searchBanner">
                        <p>
                            <input type="text" name="search" placeholder="Search..." id="' . $pagination->itemLimit .'">
                        </p>
                    </form>
                </div>';

            echo
                '<table>
                    <tr class="headers">
                        <td style="width: 50px;">ID</td>
                        <td style="text-align: left;">Details</td>
                        <td style="width: 100px;">Actions</td>
                    </tr>';

                    $banners = $mysqli->query("SELECT * FROM `banners` ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");

                    if($banners->num_rows > 0) {
                        while($banner = $banners->fetch_assoc()) {
                            $postName = $mysqli->query("SELECT name FROM `{$banner['post_type']}` WHERE id = {$banner['post_type_id']}");

                            if($postName->num_rows <= 0) {
                                $postName = 'N/A';
                            }
                            else {
                                $postName = ' - ' . $postName->fetch_array()[0];
                            }

                            echo
                                '<tr class="postRow bannerRow contentRow">
                                    <td>
                                        <span class="id">' . $banner['id'] . '</span>
                                    </td>

                                    <td style="text-align: left;">
                                        <h4>' . $banner['name'] . '</h4>
                                        <p>Displayed On: ' . ucwords($banner['post_type']) . $postName  . '</p>
                                    </td>

                                    <td>';

                                        if($banner['visible'] == 1) {
                                            echo '<p class="icon" id="view"><img src="/admin/images/icons/view.png"></p>';
                                        }
                                        else {
                                            echo '<p class="icon" id="hide"><img src="/admin/images/icons/hide.png"></p>';
                                        }

                                    echo
                                        '<p class="icon" id="edit"><img src="/admin/images/icons/edit.png"></p>
                                        <p class="icon" id="delete"><img src="/admin/images/icons/bin.png"></p>
                                    </td>
                                </tr>';
                        }
                    }
                    else {
                        echo
                            '<tr>
                                <td colspan="3">There are currently no banners.</td>
                            </tr>';
                    }
                echo '</table>';

            echo $pagination->display();
        }

        public function getBannerSingle() {
            $mysqli = $GLOBALS['mysqli'];

            $banner = $mysqli->prepare("SELECT * FROM `banners` WHERE id = ?");
            $banner->bind_param('i', $_GET['p']);
            $banner->execute();
            $result = $banner->get_result();

            if($result->num_rows > 0) {
                $postTypes = $mysqli->query("SELECT name FROM `custom_posts`");
                
                
                while($row = $result->fetch_assoc()) {
                    $posts = $mysqli->query("SELECT id, name FROM `{$row['post_type']}`");

                    echo 
                        "<script>
                            tinymce.init({
                                selector:'.tinyBanner',
                                plugins: 'paste image imagetools table code save link moxiemanager media fullscreen',
                                menubar: '',
                                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code fullscreen',
                                relative_urls: false,
                                remove_script_host: false,
                                image_title: true,
                                height: 100
                            });
                        </script>";

                    echo
                        '<div class="banner contentWrap">
                            <form id="editContent">
                                <div class="details">
                                    <div class="left">
                                        <p style="display: none;">
                                            <span class="id">' . $row['id'] . '</span>
                                        </p>

                                        <p>
                                            <label>Title: </label>
                                            <input type="text" name="title" value="' . $row['name'] . '">
                                        </p>

                                        <p>
                                            <label>Post Type: </label>
                                            <select name="postType">
                                                <option value="" selected disabled>--Select Post Type--</option>
                                                <option value="pages" ' . ($row['post_type'] == 'pages' ? 'selected' : '') . '>Pages</options>
                                                <option value="posts" ' . ($row['post_type'] == 'posts' ? 'selected' : '') . '>Posts</options>';
                                                
                                                if($postTypes->num_rows > 0) {
                                                    while($type = $postTypes->fetch_assoc()) {
                                                        echo '<option value="' . $type['name'] . '" ' . ($type['name'] == $row['post_type'] ? 'selected' : '') . '>' . ucwords(str_replace('_', ' ', $type['name'])) . '</option>';
                                                    }
                                                }
                    
                                        echo '</select>
                                        </p>

                                        <p>
                                            <label>Post Name: </label>
                                            <select name="postName">
                                                <option value="" selected disabled>--Select Post Name--</option>';
                                                
                                                if($posts->num_rows > 0) {
                                                    while($post = $posts->fetch_assoc()) {
                                                        echo '<option value="' . $post['id'] . '" ' . ($post['id'] == $row['post_type_id'] ? 'selected' : '') . '>' . $post['name'] . '</option>';
                                                    }
                                                }

                                        echo '</select>
                                        </p>';

                                    echo '<p class="message"></p>
                                        <table class="bannerSlides">
                                            <tr class="headers">
                                                <td>Slide Number</td>
                                                <td>Background Image</td>
                                                <td>Content</td>
                                                <td>Actions</td>
                                            </tr>';
                                            $this->getSlides($row['id']);
                                    echo '</table>

                                        <p>
                                            <input type="button" name="addSlide" value="Add Slide">
                                        </p>
                                    </div>

                                    <div class="right">
                                        <p>
                                            <input type="text" name="animationIn" placeholder="Incoming Animation" value="' .($row['animation_in'] != null && $row['animation_in'] != '' ? $row['animation_in'] : 'flipInX') . '">
                                        </p>

                                        <p>
                                            <input type="text" name="animationOut" placeholder="Outgoing Animation" value="' .($row['animation_out'] != null && $row['animation_out'] != '' ? $row['animation_out'] : 'slideOutDown') . '">
                                        </p>

                                        <p>
                                            <label style="width: 100%; margin-bottom: 0.5em;">Transition Time (Seconds): </label>
                                            <input type="number" name="speed" min="1" max="60" step="1" placeholder="5" value="' . ($row['speed'] / 1000) . '">
                                        </p>

                                        <div class="actions">';

                                        if($row['visible'] == 1) {
                                            echo '<p class="icon" id="view"><img src="/admin/images/icons/view.png" alt="Visible"></p>';
                                        }
                                        else {
                                            echo '<p class="icon" id="hide"><img src="/admin/images/icons/hide.png" alt="Hidden"></p>';
                                        }

                                    echo 
                                        '<p class="icon" id="apply"><img src="/admin/images/icons/check.png" alt="Save Changes"></p>
                                        <p class="icon" id="delete"><img src="/admin/images/icons/bin.png" alt="Delete"></p>
                                        </div>

                                        <p>
                                            <input type="button" name="preview" value="Preview">
                                        </p>
                                        
                                        <p>Preview will only show content changes, animations can only be updated live.</p>
                                    </div>
                                </div>
                            </form>';
                            $this->previewSlider($row['id']);
                        echo '</div>';
                }
            }
            else {
                echo '<h1>Banner ' .  $_GET['p'] . ' does not exist</h1>';
            }
        }

        private function getSlides($id) {
            $mysqli = $GLOBALS['mysqli'];

            $slides = $mysqli->query("SELECT * FROM `banners_slides` WHERE banner_id = {$id} ORDER BY position ASC");

            $bannerSlides = [];

            if($slides->num_rows > 0) {
                while($slide = $slides->fetch_assoc()) {
                    array_push($bannerSlides, 
                        '<tr class="slide" id="slide' . $slide['position'] . '">' .
                            '<td id="position">' . $slide['position'] . '</td>' .
                            '<td id="backgroundImage"><input type="text" id="bannerImage' . $slide['position'] . '" name="bannerImage" value="' . $slide['live_background'] . '"><input type="button" name="bannerBrowse" value="Browse"></td>' .
                            '<td id="content"><textarea class="tinyBanner">' . $slide['live_content'] . '</textarea></td>' .
                            '<td><input type="button" name="deleteSlide" class="badButton" value="Delete Slide"></td>' .
                        '</tr>'
                    );
                }
            }

            echo implode($bannerSlides);
        }

        private function previewSlider($id) {
            $mysqli = $GLOBALS['mysqli'];

            $slides = $mysqli->query("SELECT * FROM `banners_slides` WHERE banner_id = {$id} ORDER BY position ASC");

            if($slides->num_rows > 0) {
                $settings = $mysqli->query("SELECT * FROM `banners` WHERE id = {$id}");

                echo
                    '<div class="owl-carousel previewSlider">';

                    while($slide = $slides->fetch_assoc()) {
                        echo
                            '<div class="previewItem" style="background-image: url(\'' . $slide['preview_background'] . '\')">
                                <div class="previewContent">
                                    <div class="previewContentInner">' .
                                        $slide['preview_content'] . 
                                    '</div>
                                </div>
                            </div>';
                    }

                echo
                    '</div>';

                if($settings->num_rows > 0) {
                    $settings = $settings->fetch_assoc();
                    
                    if($slides->num_rows == 1) {
                        echo
                            '<script>
                                $(document).ready(function() {
                                    $(".owl-carousel").owlCarousel({
                                        ' . ($settings['animation_out'] != null && $settings['animation_out'] != '' ? 'animateOut: "' . $settings['animation_out'] . '", ' : '') . ($settings['animation_in'] != null && $settings['animation_in'] != '' ? 'animateIn: "' . $settings['animation_in'] . '", ' : '') . '                                         
                                        items: 1,
                                        loop: false,
                                        ' . ($settings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $settings['speed'] . ',' : '') . 
                                        ($settings['speed'] == 0 ? 'autoplay: false,' : '') . '
                                        mouseDrag: false, 
                                        touchDrag: false,
                                        pullDrag: false,
                                        freeDrag: false
                                    });
                                });
                            </script>';
                    }
                    else {
                        echo
                            '<script>
                                $(document).ready(function() {
                                    $(".owl-carousel").owlCarousel({
                                        ' . ($settings['animation_out'] != null && $settings['animation_out'] != '' ? 'animateOut: "' . $settings['animation_out'] . '", ' : '') . ($settings['animation_in'] != null && $settings['animation_in'] != '' ? 'animateIn: "' . $settings['animation_in'] . '", ' : '') . '                                         
                                        items: 1,
                                        loop: true,
                                        ' . ($settings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $settings['speed'] . ',' : '') . 
                                        ($settings['speed'] == 0 ? 'autoplay: false,' : '') . '
                                    });
                                });
                            </script>';
                    }
                    
                }
            }
        }
    }
    
    class post {
        private $postType;
        public $showTitle = true;
        public $showAuthor = true;
        public $showCategory = true;
        public $showPosted = true;
        public $showShort = true;
        public $showContent = true;
        public $showThumb = true;
        public $showHero = true;
        public $showSidebar = false;
        public $itemLimit = 12;
        public $customHero;
        private $productTable;
        private $additionalTable;
        private $isHome = false;
        private $homeUrl;

        private $output;

        public function __construct($postType = 'posts') {
            $postType = str_replace('-', '_', strtolower($postType));

            if($postType != '' && $postType != null) {
                $this->postType = $postType;
            }
            else {
                $this->postType = 'posts';
            }

            $this->redirectPost();
        }

        private function redirectPost() {
            $mysqli = $GLOBALS['mysqli'];
            $homeId = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0];
            $homeUrl = $mysqli->query("SELECT url FROM `pages` WHERE id = {$homeId}");
            $hidePosts = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'hide_posts'")->fetch_array()[0];

            //Redirect to / if url matches homepage
            if($homeUrl->num_rows > 0) {
                $homeUrl = $homeUrl->fetch_array()[0];

                if($this->postType == 'pages' && $_SERVER['REQUEST_URI'] == '/post-type/pages/' . $homeUrl) {
                    header('HTTP/1.1 301 Moved Permenantly');
                    header('Location: /');

                    exit();
                }
            }

            //Redirect to / if posts are hidden
            if($this->postType == 'posts' && explode('/', $_SERVER['REQUEST_URI'])[2] == 'posts' && $hidePosts == 1) {
                header('HTTP/1.1 301 Moved Permenantly');
                header('Location: /');

                exit();
            }

            if($_SERVER['REQUEST_URI'] == '/') {
                $this->isHome = true;
                $this->homeUrl = $homeUrl;

                if($this->homeUrl != null) {
                    $this->postType = 'pages';
                }
                elseif($hidePosts != 1) {
                    $this->postType = 'posts';
                }
                else {
                    http_response_code(404);
                    header('Location: /404');

                    exit();
                }
            }
        }
        
        private function postHeader($id, $name, $author, $image, $date, $category) {
            $mysqli = $GLOBALS['mysqli'];
            $slider = $mysqli->query("SELECT * FROM `banners` WHERE post_type = '{$this->postType}' AND post_type_id = {$id} AND visible = 1");
            $catName = $mysqli->query("SELECT name FROM `categories` WHERE id = {$category} AND post_type = '{$this->postType}'");
            $hero = '';            
            
            if($catName->num_rows > 0) {
                $catName = $catName->fetch_array()[0];
            }
            else {
                $category = 0;
            }

            if($slider->num_rows > 0) {
                $slider = $slider->fetch_assoc();
                $sliderId = $slider['id'];
                $slides = $mysqli->query("SELECT * FROM `banners_slides` WHERE banner_id = {$sliderId} ORDER BY position ASC");

                if($slides->num_rows > 0) {
                    $hero .=
                        '<div class="slider owl-carousel" id="' . $this->postType . 'Slider">';

                        while($slide = $slides->fetch_assoc()) {
                            $hero .= 
                                '<div class="sliderItem" style="background-image: url(\'' . $slide['live_background'] . '\')">
                                    <div class="sliderContent">'
                                        . $slide['live_content'] .
                                    '</div>
                                </div>';
                        }

                    $hero .=
                        '</div>';
                    
                    if($slides->num_rows == 1) {
                        $hero .=
                            '<script>
                                $(document).ready(function() {
                                    $(".owl-carousel").owlCarousel({
                                        ' . ($settings['animation_out'] != null && $settings['animation_out'] != '' ? 'animateOut: "' . $settings['animation_out'] . '", ' : '') . ($settings['animation_in'] != null && $settings['animation_in'] != '' ? 'animateIn: "' . $settings['animation_in'] . '", ' : '') . '                                         
                                        items: 1,
                                        loop: false,
                                        ' . ($settings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $settings['speed'] . ',' : '') . 
                                        ($settings['speed'] == 0 ? 'autoplay: false,' : '') . '
                                        mouseDrag: false, 
                                        touchDrag: false,
                                        pullDrag: false,
                                        freeDrag: false
                                    });
                                });
                            </script>';
                    }
                    else {
                        $hero .=
                            '<script>
                                $(document).ready(function() {
                                    $(".slider").owlCarousel({
                                        ' . ($slider['animation_out'] != null && $slider['animation_out'] != '' ? 'animateOut: "' . $slider['animation_out'] . '", ' : '') . ($slider['animation_in'] != null && $slider['animation_in'] != '' ? 'animateIn: "' . $slider['animation_in'] . '", ' : '') . '                                         
                                        items: 1,
                                        loop: true,
                                        ' . ($slider['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $slider['speed'] . ',' : '') . 
                                        ($slider['speed'] == 0 ? 'autoplay: false,' : '') . 'autoplay: false
                                    });
                                });
                            </script>';
                    }
                }
            }
            else {
                $hero .=
                    '<div class="hero" id="' . $this->postType . 'Hero">'
                        . (isset($image) && $image != '' ? '<img class="heroImage" src="' . $image . '" alt="' . $name . '">' : '') .

                        '<div class="heroContent">'
                            . ($this->showTitle == true ? '<h1><span>' . $name . '</span></h1>' : '') 
                            . ($this->showCategory == true && $category != 0 ? '<h2><span id="label">Category: </span>' . $catName . '</h2>' : '') 
                            . ($this->showAuthor == true && $author != null && $author != '' ? '<h3><span id="label">By: </span>' . ucwords($author) . '</h3>' : '') 
                            . ($this->showPosted == true ? '<h4>' . date('d/m/Y H:i:s', strtotime($date)) . '</h4>' : '') .
                        '</div>
                    </div>';
            }

            return $hero;
        }
        
        private function sidebar() {
            if($this->postType == 'posts') {
                $this->showSidebar = true;
            }
            
            if($this->showSidebar == true) {    
                $sidebarClass = new category($_GET['postType']);
                
                $sidebar = 
                    '<div class="sidebar">
                        <div class="sidebarInner">
                            ' . $sidebarClass->sidebarTree() . '
                        </div>
                    </div>';
            }
            else {
                $sidebar = '';
            }
            
            return $sidebar;
        }
        
        private function gallery($productOptions, $id) {
            $mysqli = $GLOBALS['mysqli'];
            
            if($productOptions == true) {
                
                $galleryImages = $mysqli->query("SELECT gallery_images FROM `{$this->postType}_options` WHERE post_type_id = {$id}")->fetch_array()[0];
                $galleryImages = explode(';', rtrim($galleryImages, ';'));
                
                $gallery = 
                    '<div class="gallery owl-carousel">';
                
                foreach($galleryImages as $galleryImage) {
                    $galleryImage = ltrim($galleryImage, '"');
                    $galleryImage = rtrim($galleryImage, '"');
                    
                    if($galleryImage != '') {
                        $gallery .=
                            '<a href="/gallery/' . $this->postType . '/' . $id .'/' . $galleryImage . '" data-lightbox="gallery" data-title="' . explode('.', ucwords(str_replace('-', ' ', $galleryImage)))[0] . '">
                                <div class="galleryItem">
                                    <img src="/gallery/' . $this->postType . '/' . $id . '/' . $galleryImage . '" alt="">
                                </div>
                            </a>';
                    }
                }   
                
                $gallery .=
                    '</div>
                    
                    <script>
                        $(document).ready(function() {
                            $(".gallery").owlCarousel({
                                loop: false,
                                margin: 0,
                                nav: true,
                                dots: false,
                                responsive: {
                                    0: {
                                        items: 1
                                    },
                                    600: {
                                        items: 3
                                    },
                                    1000: {
                                        items: 8
                                    }
                                }
                            });
                        });
                    </script>';
                
                return $gallery;
            }
        }


        public function display() {
            if(isset($_GET['url']) || ($this->isHome == true && $this->postType == 'pages')) {
                $this->showSingle();
            }
            else {
                $this->showList();
            }

            echo $this->output;
        }

        private function showList() {
            $mysqli = $GLOBALS['mysqli'];
            $postCount = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}` WHERE visible = 1");

            if($postCount->num_rows > 0) {
                $postCount = $postCount->fetch_array()[0];
            }
            else {
                http_response_code(404);
                header('Location: /404');

                exit();
            }

            $postName = str_replace('_', ' ', $this->postType);

            if($mysqli->query("SHOW TABLES LIKE '{$this->postType}_options'")->num_rows > 0) {
                $hasProductOptions = true;
            }

            $pagination = new pagination($postCount);
            $pagination->setItemLimit($this->itemLimit);
            $pagination->prefix = '/post-type/' . str_replace('-', '_', $this->postType) . '/';
            $pagination->load();

            if(isset($_GET['category'])) {
                $posts = $mysqli->query("SELECT * FROM `{$this->postType}` WHERE visible = 1 AND category_id = {$_GET['category']} ORDER BY date_posted DESC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");
                
                $catName = $mysqli->query("SELECT name FROM `categories` WHERE post_type = '{$this->postType}' AND id = {$_GET['category']}")->fetch_array()[0];
            }
            else {
                $posts = $mysqli->query("SELECT * FROM `{$this->postType}` WHERE visible = 1 ORDER BY date_posted DESC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");
            }

            $postOutput = 
                '<div class="postWrap ' . ($this->isHome == true ? 'homeWrap' : '') . '" id="' . $this->postType . 'Wrap">
                    <h1 class="postTitle"><span>' . ucwords($postName) . (isset($catName) ? ': ' . $catName : '') . '</span></h1>';

            if($postCount > 0 && $posts->num_rows > 0) {
                $postOutput .= 
                    '<div class="postList" id="' . $this->postType . 'List">';

                while($row = $posts->fetch_assoc()) {
                    if(strlen($row['description']) > 500) {
                        $shortDesc = substr($row['description'], 0, 500) . '...';
                    }
                    else {
                        $shortDesc = $row['description'];
                    }

                    $missingImage = false;

                    if($row['image_url'] != null && $row['image_url'] != '') {
                        $imageSource = $row['image_url'];
                    }
                    elseif(isset($hasProductOptions) && $hasProductOptions == true) {
                        $galleryItems = $mysqli->query("SELECT gallery_images, gallery_main FROM `{$this->postType}_options` WHERE post_type_id = {$row['id']}")->fetch_assoc();

                        if($galleryItems['galleryMain'] != null && $galleryItems['gallery_main'] != '') {
                            $imageSource = '/gallery/' . $this->postType . '/' . $row['id'] . '/' . $galleryItems['gallery_main'];
                        }
                        elseif($galleryItems['gallery_images'] != null & $galleryItems['gallery_images'] != '') {
                            $imageSource = '/gallery/' . $this->postType . '/' . $row['id'] . '/' . ltrim(explode('";', $galleryItems['gallery_images'])[0], '"');
                        }
                        else {
                            $imageSource = '/admin/images/missingImage.png';
                            $missingImage = true;
                        }
                    }
                    else {
                        $imageSource = '/admin/images/missingImage.png';
                        $missingImage = true;
                    }

                    $postOutput .= 
                        '<div class="postListItem" id="' . $this->postType . 'Item">';

                            if($this->showThumb == true) {
                                $postOutput .=
                                    '<div class="imageWrap ' . ($missingImage == true ? 'missingImage' : '') . '">
                                        <img src="' . $imageSource . '" alt="' . $row['name'] .'">
                                    </div>';
                            }

                    $postOutput .=
                            '<div class="smallContent">'
                                . ($this->showTitle == true ? '<h2><a href="/post-type/' . str_replace('_', '-', $this->postType) . '/' . $row['url'] . '">' . $row['name'] . '</a></h2>' : '')
                                . ($this->showShort == true ? '<p>' . $shortDesc .'<a href="/post-type/' . str_replace('_', '-', $this->postType) . '/' . $row['url'] . '" class="readMore">Read More</a></p>' : '') .

                                '<div class="author">'
                                    . ($this->showAuthor == true ? '<h3>' . $row['author'] . '</h3>' : '')
                                    . ($this->showPosted == true ? '<h4>' . date('d/m/Y', strtotime($row['date_posted'])) . '</h4>' : '') .
                                '</div>
                            </div>
                        </div>';
                }

                $postOutput .= 
                    '</div>';
            }
            else {
                $postOutput .= '<p id="noPosts">There are currently no ' . $postName . '.</p>';
            }

            $postOutput .=
                '</div>';

            $this->output =
                '<div class="mainInner">
                    <div class="postInner">'
                        . $this->sidebar() .
                    
                          '<div class="content ' . ($this->showSidebar == true ? 'withSidebar' : '') .'">' .
                              $postOutput .
                              $pagination->display() .
                         '</div>
                     </div>
                </div>';
        }


        private function showSingle() {
            $mysqli = $GLOBALS['mysqli'];

            if($this->isHome == true) {
                $post = $mysqli->query("SELECT * FROM `{$this->postType}` WHERE url = '{$this->homeUrl}'");
            }
            else {
                $post = $mysqli->query("SELECT * FROM `{$this->postType}` WHERE url = '{$_GET['url']}'");
            }
            
            $postId = $mysqli->query("SELECT id FROM `{$this->postType}` WHERE url = '{$_GET['url']}'")->fetch_array()[0];

            if($post->num_rows > 0) {                    
                $postOutput = 
                    '<div class="postWrap postSingle ' . ($this->isHome == true ? 'homeWrap ' : '') . $this->postType . 'Wrap" id="_' . $postId . 'Wrap">';

                while($row = $post->fetch_assoc()) {
                    if($mysqli->query("SHOW TABLES LIKE '{$this->postType}_options'")->num_rows > 0) {
                        $hasProductOptions = true;
                    }

                    $missingImage = false;

                    if($row['image_url'] != null && $row['image_url'] != '') {
                        $imageSource = $row['image_url'];
                    }
                    elseif(isset($hasProductOptions) && $hasProductOptions == true) {
                        $galleryItems = $mysqli->query("SELECT gallery_images, gallery_main FROM `{$this->postType}_options` WHERE post_type_id = {$row['id']}")->fetch_assoc();

                        if($galleryItems['galleryMain'] != null && $galleryItems['gallery_main'] != '') {
                            $imageSource = '/gallery/' . $this->postType . '/' . $row['id'] . '/' . $galleryItems['gallery_main'];
                        }
                        elseif($galleryItems['gallery_images'] != null & $galleryItems['gallery_images'] != '') {
                            $imageSource = '/gallery/' . $this->postType . '/' . $row['id'] . '/' . ltrim(explode('";', $galleryItems['gallery_images'])[0], '"');
                        }
                        else {
                            $imageSource = '/admin/images/missingImage.png';
                            $missingImage = true;
                        }
                    }
                    else {
                        $imageSource = '/admin/images/missingImage.png';
                        $missingImage = true;
                    }
                    
                    
                    if(isset($this->customHero)) {
                        ob_start();
                        
                        include_once($this->customHero);
                        
                        $postOutput .= ob_get_clean();
                    }
                    else {
                        $postOutput .= $this->postHeader($row['id'], $row['name'], $row['author'], $imageSource, $row['date_posted'], $row['category_id']);
                    }                    
                    
                    $postOutput .= 
                        '<div class="postInner">' .
                            $this->sidebar();
                    
                    $postOutput .=
                        '<div class="postContent ' . ($this->showSidebar == true ? 'withSidebar' : '') . '">';
                    
                    $postOutput .= $this->gallery($hasProductOptions, $row['id']);

                    if(isset($hasProductOptions) && $hasProductOptions == true) {
                        $productOptions = $mysqli->query("SELECT * FROM `{$this->postType}_options` WHERE post_type_id = {$row['id']}");

                        if($productOptions->num_rows > 0) {
                            $productOption = $productOptions->fetch_assoc();

                            $postOutput .=
                                '<div class="sideOptions">
                                    <ul>';

                            if($productOption['features'] != null && $productOption['features'] != '') {
                                $postOutput .= 
                                    '<li class="features" id="inactive">
                                        <h3>Features</h3>

                                        <div class="sideOptionInner">
                                            <span>' . $productOption['features'] . '</span>
                                        </div>
                                    </li>';
                            }

                            if($productOption['specifications'] != null && $productOption['specifications'] != '') {
                                $specs = explode(';', rtrim($productOption['specifications'], ';'));

                                $postOutput .= 
                                    '<li class="output" id="inactive">
                                        <h3>Specifications</h3>

                                        <div class="sideOptionInner">
                                            <table>';

                                foreach($specs as $specRow) {
                                    $specRow = explode('","', $specRow);
                                    $specName = ltrim($specRow[0], '"');
                                    $specValue = rtrim($specRow[1], '"');

                                    $postOutput .=
                                        '<tr>
                                            <td>' . $specName . '</td>
                                            <td>' . $specValue . '</td>
                                        </tr>';
                                }

                                $postOutput .= 
                                            '</table>
                                        </div>
                                    </li>';
                            }

                            $postOutput .=        
                                    '</ul>
                                </div>';
                        }
                    }

                    $postOutput .=
                            ($this->showContent == true ? $row['content'] : '');
                    
                    ob_start();
                        
                    include_once($_SERVER['DOCUMENT_ROOT'] . $row['custom_content']);

                    $postOutput .= ob_get_clean();
                    
                    $postOutput .= 
                        '</div>';
                }

                $postOutput .=
                        '</div>
                    </div>';
            }
            else {
                http_response_code(404);
                header('Location: /404');

                exit();
            }           

            $this->output =
                '<div class="mainInner">
                    <div class="content">' .
                        $postOutput .
                    '</div>
                </div>';
        }

        public function debug() {
            $parameters = get_object_vars($this);

            echo '<div style="font-family: sans-serif; border: 1px solid #000; margin: 1em 0; padding: 1em; box-sizing: border-box;">' .
                 '<h3 style="margin: 0 0 0.5em 0;">Debug Info: </h3>' . 
                 '<ul style="margin: 0; list-style: none; padding-left: 1em;">';

            foreach($parameters as $parameter => $value) {
                if($value == null) {
                    $value = 'null';
                }

                echo '<li>' . $parameter . ': ' . $value . '</li>';
            }

            echo '</div>';
        }
    }

    class editor {
        private $postType;
        private $output;

        public function __construct($postType = 'posts') {
            $postType = strtolower($postType);

            if($postType != null && $postType != '') {
                $this->postType = $postType;
            }
            else {
                $this->postType = 'posts';
            }
        }

        public function display() {
            if($_GET['id'] && $_GET['id'] > 0) {
                $this->showSingle();
            }
            else {
                $this->showList();
            }

            echo $this->output;
        }

        private function getFeatured($imageUrl) {
            $featured = '';

            if($imageUrl == null || $imageUrl == '') {
                $featured = 
                    '<div class="noFeatured featuredInner">
                        <span>Select Image</span>
                        <span class="featuredDelete" style="display: none;"><span>X</span></span>
                        <img src="/admin/images/missingImage.png" id="featuredImage" style="display: none;">
                    </div>';
            }
            else {
                $featured = 
                    '<div class="featuredInner">
                        <span style="display: none;">Select Image</span>
                        <span class="featuredDelete"><span>X</span></span>
                        <img src="' . $imageUrl .'" id="featuredImage">
                    </div>';
            }

            return $featured;
        }

        private function showProductOptions($postName) {
            $mysqli = $GLOBALS['mysqli'];
            
            $productCheck = $mysqli->query("SHOW TABLES LIKE '{$this->postType}_options'");
            
            if($productCheck->num_rows > 0) {
                $checkId = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['id']}");
                
                if($checkId->num_rows > 0) {
                    $product = 
                        '<div class="productOptionsWrap">
                            <h2>' . ucwords($postName) . 's Additional Options</h2>
                            
                            <div class="formBlock productOptions ' . $this->postType . 'Options" style="width: 100%; border-bottom: 0;">
                                <form style="max-width: 100%;" enctype="multipart/formdata">';
                    
                    //Gallery
                    $images = $mysqli->query("SELECT gallery_images, gallery_main FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['id']}")->fetch_assoc();
                    $galleryImages = explode(';', rtrim($images['gallery_images'], ';'));
                    $galleryMain = $images['gallery_main'];
                    
                    $product .=
                        '<h3>Gallery</h3>
                        
                        <input type="file" name="galleryOption" multiple>
                        <input type="button" name="galleryOptionNew" value="Choose Images">
                        
                        <div class="galleryItems current">';
                    
                    foreach ($galleryImages as $galleryImage) {
                        $galleryImage = ltrim($galleryImage, '"');
                        $galleryImage = rtrim($galleryImage, '"');
                        
                        if($galleryImage != '') {
                            $product .=
                                '<div class="galleryItem"' . ($galleryImage == $galleryMain ? ' id="galleryMain"' : '') . '> 
                                    <span class="galleryDelete"><img src="/admin/images/icons/bin.png"></span>
                                    <img class="galleryImage" src="/gallery/' . $this->postType . '/' . $_GET['id'] . '/' . $galleryImage . '" alt="' . $galleryImage . '">
                                </div>';
                        }
                    }
                        
                    $product .=
                        '</div>
                        
                        <div class="galleryItems uploaded">
                        
                        </div>
                        
                        <p class="galleryMessage"></p>
                        
                        <hr>';
                    
                    //Features
                    $features = $mysqli->query("SELECT features FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['id']}")->fetch_array()[0];
                    
                    $product .=
                        '<h3>Features</h3>
                        
                        <p>
                            <textarea class="noTiny" name="featuresOption">' . $features . '</textarea>
                        </p>
                        
                        <hr>';
                    
                    //Specifications
                    $specs = $mysqli->query("SELECT specifications FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['id']}")->fetch_array()[0];
                    $specRow = 1;
                    
                    if($specs != null && $specs != '') {
                        $specsRows = explode(';', rtrim($specs, ';'));
                        
                        foreach($specsRows as $specRow) {
                            $specCols = explode('","', $specRow);
                            $specName = ltrim($specCols[0], '"');
                            $specValue = rtrim($specCols[1], '"');
                            
                            $specData .=
                                '<tr id="spec' . $specRow . '">
                                    <td>
                                        <input type="text" name="specName" value="' . htmlspecialchars($specName, ENT_COMPAT, 'UTF-8') . '">
                                    </td>
                                    
                                    <td>
                                        <input type="text" name="specValue" value="' . htmlspecialchars($specValue, ENT_COMPAT, 'UTF-8') . '">
                                    </td>
                                    
                                    <td>
                                        <input type="button" class="badButton" name="deleteSpec" value="Delete Spec">
                                    </td>
                                </tr>';
                            
                            $specRow++;
                        }
                    }
                    else {
                        $specData =
                            '<tr id="spec1">
                                <td><input type="text" name="specName" value="Height"></td>
                                <td><input type="text" name="specValue"></td>
                                <td><input class="badButton" type="button" name="deleteSpec" value="Delete Spec"></td>
                            </tr>

                            <tr id="spec2">
                                <td><input type="text" name="specName" value="Width"></td>
                                <td><input type="text" name="specValue"></td>
                                <td><input class="badButton" type="button" name="deleteSpec" value="Delete Spec"></td>
                            </tr>

                            <tr id="spec3">
                                <td><input type="text" name="specName" value="Depth"></td>
                                <td><input type="text" name="specValue"></td>
                                <td><input class="badButton" type="button" name="deleteSpec" value="Delete Spec"></td>
                            </tr>';
                    }
                    
                    $product .=
                        '<h3>Specifications</h3>
                        
                        <table class="specificationOption" style="max-width: 650px;">
                            <tr class="headers">
                                <td>Specification Name</td>
                                <td>Specification Value</td>
                                <td></td>
                            </tr>'
                            . $specData .
                        '</table>
                        
                        <p class="specActions">
                            <input type="button" name="addSpec" value="Add Spec Row">
                        </p>
                        
                        <hr>';
                    
                    $product .=    
                                '</form>
                            </div>
                        </div>
                        <script src="/admin/settings/scripts/productOptions.js"></script>';
                }
                else {
                    $product = 
                        '<div class="productOptions">
                            <h2 style="color: red;">Unable to access additional options for this ' . $postName . '</h2>
                        </div>';
                }
                
                return $product;
            }
        }
        
        private function showList() {
            $mysqli = $GLOBALS['mysqli'];

            $postCheck = $mysqli->query("SHOW TABLES LIKE '{$this->postType}'");

            if($postCheck->num_rows <= 0) {
                http_response_code(404);
                header('Location: /404');

                exit();
            }

            $postCount = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}`")->fetch_array()[0];
            $postName = str_replace('_', ' ', $this->postType);

            $pagination = new pagination($postCount);
            $pagination->prefix = '/admin/editor/' . $this->postType . '/';
            $pagination->load();

            $posts = $mysqli->query("SELECT * FROM `{$this->postType}` ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");

            $output = 
                '<div class="content">
                    <h1>' . ucwords($postName) . '</h1>

                    <div class="formBlock">
                        <form class="addContent" id="add' . ucwords($this->postType) . '">
                            <p>
                                <input type="submit" value="New ' . rtrim(ucwords($postName), 's') .'">
                            </p>

                            <p class="message"></p>
                        </form>

                        <form class="searchContent" id="search' . ucwords($this->postType) . '">
                            <p>
                                <input type="text" name="search" placeholder="Search ' . ucwords($postName) . 's..." id="' . $pagination->itemLimit . '">
                            </p>
                        </form>
                    </div>

                    <table>
                        <tr class="headers">
                            <td style="width: 50px;">ID</td>
                            <td style="text-align: left;">Details</td>
                            <td style="width: 180px;">Published</td>
                            <td style="width: 100px;">Actions</td>
                        </tr>';

            if($posts->num_rows > 0) {
                while($row = $posts->fetch_assoc()) {
                    $output .=
                        '<tr class="' . $this->postType . 'Row contentRow">
                            <td>
                                <span class="id">' . $row['id'] .'</span>
                            </td>

                            <td style="text-align: left;">
                                <h4>' . $row['name'] . '</h4>
                                <p>' . $row['description'] . '</p>
                                <p style="font-size: 0.75em;">URL: ' . $row['url'] . '</p>
                            </td>

                            <td>
                                <p>' . ucwords($row['author']) .'</p>
                                <p>' . date('d/m/Y H:i:s', strtotime($row['date_posted'])) .'</p>
                            </td>

                            <td>
                                <p class="icon" id="' . ($row['visible'] == 1 ? 'view' : 'hide') . '"><img src="/admin/images/icons/' . ($row['visible'] == 1 ? 'view' : 'hide') . '.png"></p>
                                <p class="icon" id="edit"><img src="/admin/images/icons/edit.png"></p>
                                <p class="icon" id="delete"><img src="/admin/images/icons/bin.png"></p>
                            </td>
                        </tr>';
                }
            }
            else {
                $output .=
                    '<tr>
                        <td colspan="4">There are currently no ' . $postName .'s</td>
                    </tr>';
            }

            $output .=
                    '</table>' .
                    $pagination->display();

            $output .= 
                '</div>
                <script src="/admin/settings/scripts/postPage.js"></script>';

            $this->output = $output;
        }

        private function showSingle() {
            $mysqli = $GLOBALS['mysqli'];

            $postName = str_replace('_', ' ', $this->postType);
            $post = $mysqli->query("SELECT * FROM `{$this->postType}` WHERE id = {$_GET['id']}");

            $output = 
                '<div class="content">
                    <div class="' . $this->postType . ' contentWrap">';

            if($post->num_rows > 0) {
                while($row = $post->fetch_assoc()) {
                    $output .=
                        '<form id="editContent">
                            <div class="details">
                                <div class="left">
                                    <p style="display: none;">
                                        <span class="id">' . $row['id'] . '</span>
                                    </p>

                                    <p>
                                        <label>Title: </label>
                                        <input type="text" name="title" value="' . $row['name'] . '">
                                    </p>

                                    <p>
                                        <label>Short Content: </label>
                                        <input type="text" name="description" value="' . $row['description'] . '" maxlength="500">
                                    </p>

                                    <p>
                                        <label>URL: </label>
                                        <input type="text" name="url" value="' . $row['url'] . '">
                                    </p>';

                                    if($this->postType != 'pages') {
                                        $categories = $mysqli->query("SELECT id, name FROM `categories` WHERE post_type = '{$this->postType}' ORDER BY name ASC");

                                        $output .=
                                            '<p>
                                                <label>Category: </label>
                                                <select name="categories">
                                                    <option value="" selected disabled>--Select Category--</option>';

                                                    if($categories->num_rows > 0) {
                                                        while($category = $categories->fetch_assoc()) {
                                                            $output .=
                                                                '<option value="' . $category['id'] . '" ' . ($category['id'] == $row['category_id'] ? 'selected' : '') . '>' . $category['name'] . '</option>';
                                                        }
                                                    }

                                        $output .=
                                                '</select>
                                            </p>';
                                    }

                    $output .=        
                                    '<p class="message"></p>
                                </div>

                                <div class="right">
                                    <p>
                                        <label>Author: </label>
                                        <input type="text" name="author" value="' . ucwords($row['author']) . '">
                                    </p>

                                    <p>
                                        <label>Date Posted: </label>
                                        <input type="datetime-local" name="date" step="1" value="' . str_replace(' ', 'T', $row['date_posted']) . '">
                                    </p>

                                    <div class="actions">
                                        <p class="icon" id="' . ($row['visible'] == 1 ? 'view' : 'hide') . '"><img src="/admin/images/icons/' . ($row['visible'] == 1 ? 'view.png" alt="Visible"' : 'hide.png" alt="Hidden"') . '></p>
                                        <p class="icon" id="apply"><img src="/admin/images/icons/check.png" alt="Save Changes"></p>
                                        <p class="icon" id="delete"><img src="/admin/images/icons/bin.png" alt="Delete"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="editor">
                                <textarea name="content">' . $row['content'] . '</textarea>
                            </div>

                            <div class="featuredImage">'
                                . $this->getFeatured($row['image_url']) .
                            '</div>
                            
                            <div>
                                <h3>Custom PHP File</h3>
                                <p>Enter the relative url of a PHP file you wish to include (Appears at end of page).</p>
                                <input class="customCode" name="customContent" value="' . $row['custom_content'] . '">
                            </div>
                        </form>
                        <script src="/admin/settings/scripts/postPage.js"></script>';
                    
                        $output .= $this->showProductOptions($postName);
                }
            }
            else {
                $output .= 
                    '<h1>' . ucwords($postName) . ' ' . $_GET['id'] . ' does not exist!</h1>
                    <a href="./">Return To ' . ucwords($postName) . 's</a>';
            }

            $output .=
                    '</div>
                </div>';

            $this->output = $output;
        }

        public function debug() {
            $parameters = get_object_vars($this);

            echo
                '<div style="font-family: sans-serif; border: 1px solid #000; margin: 1em 0; padding: 1em; box-sizing: border-box;">
                    <h3 style="margin: 0 0 0.5em 0;">Debug Info: </h3>
                    <ul style="margin: 0; list-style: none; padding-left: 1em;">';

            foreach($parameters as $parameter => $value) {
                if($value == null) {
                    $value = 'null';
                }

                echo '<li>' . $parameter . ': ' . $value . '</li>';
            }

            echo    
                    '</ul>
                </div>';
        }
    }

    class categoryEditor {
        public $postType;

        public function __construct($postType = 'posts') {                
            $postType = str_replace('-', '_', strtolower($postType));

            if($postType != '' && $postType != null) {
                $this->postType = $postType;
            }
            else {
                $this->postType = 'posts';
            }
        }

        private function postTypeSelector() {
            $mysqli = $GLOBALS['mysqli'];

            $postTypes = $mysqli->query("SELECT name FROM `custom_posts` ORDER BY name ASC");
            $categories = $mysqli->query("SELECT name, id FROM `categories` WHERE post_type = '{$this->postType}' AND level <= 2");
            $selector =
                '<div class="formBlock">
                    <form id="categorySelector">
                        <p>
                            <label>Edit Categories For: </label>
                            <select name="categoryType">
                                <option value="posts" ' . ($this->postType == 'posts' ? 'selected' : '') . '>Posts</option>';

                                while($row = $postTypes->fetch_assoc()) {
                                    $selector .= 
                                        '<option value="' . $row['name'] . '"' . ($this->postType == $row['name'] ? 'selected' : '') . '>' . ucwords(str_replace('_', ' ', $row['name'])) . '</option>';
                                }

                        $selector .= 
                            '</select>
                        </p>
                    </form>
                </div>

                <div class="formBlock">
                    <form id="createCategory">
                        <h3>Add New Category</h3>

                        <input type="hidden" value="' . $this->postType . '" name="catPostType">

                        <p>
                            <label>Name: </label>
                            <input type="text" name="catName">
                        </p>

                        <p>
                            <label>Description: </label>
                            <input type="text" name="catDesc">
                        </p>
                    </form>

                    <form id="createCategory">
                        <h3 id="hideSmall">&nbsp;</h3>

                        <p>
                            <label>Image: </label>
                            <input type="text" name="catImage" style="width: calc(100% - 232px - 46px);">
                            <input type="button" name="catImageSelect" value="Choose Image">
                        </p>

                        <p>
                            <label>Parent Category: </label>
                            <select name="catParent">
                                <option value="0">No Parent Category</option>';

                            while($row = $categories->fetch_assoc()) {
                                $selector .=
                                    '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }

                        $selector .=
                            '</select>
                        </p>

                        <p>
                            <input type="submit" value="Create Category">
                        </p>

                        <p class="message">' . (isset($_SESSION['categoryMessage']) ? $_SESSION['categoryMessage'] : '') . '</p>
                    </form>
                </div>';

            unset($_SESSION['categoryMessage']);

            return $selector;
        }

        private function createTree($parent = 0) {
            $mysqli = $GLOBALS['mysqli'];
            $tree = '';

            $categories = $mysqli->query("SELECT * FROM `categories` WHERE post_type = '{$this->postType}' AND parent_id = {$parent} ORDER BY name ASC");

            if($categories->num_rows > 0) {
                $tree .= 
                    '<ul class="categoryTree" id="parent' . $parent . '">';

                while($row = $categories->fetch_assoc()) {
                    $catCount = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}` WHERE category_id = {$row['id']}");

                    if($catCount->num_rows > 0) {
                        $catCount = $catCount->fetch_array()[0];
                    }
                    else {
                        $catCount = 0;
                    }

                    $tree .= 
                        '<li id="category' . $row['id'] . '">
                            <div class="categoryDetails">
                                ' . $row['name'] . ' <i style="color: #aaa;">(' . $catCount . ')</i>

                                <div class="actions formBlock">
                                    <p>
                                        <input type="button" id="category' . $row['id'] . '" name="editCategory" value="Edit">
                                        <input type="button" style="min-width: 0;" class="badButton" id="category' . $row['id'] . '" value="X" name="deleteCategory">
                                    </p>
                                </div>
                            </div>

                            ' . $this->createTree($row['id']) . '
                        </li>';
                }

                $tree .= 
                    '</ul>';
            }
            elseif($parent == 0 && $categories->num_rows <= 0) {
                $tree = '<h2>There are currently no categories.</h2>';
            }

            return $tree;
        }
        
        public function display() {
            if(isset($_GET['id']) && $_GET['id'] > 0) {
                $this->showEditor();
            }
            else {
                $this->showTree();
            }
        }
        
        private function showTree() {
            $mysqli = $GLOBALS['mysqli'];

            $output = 
                '<div class="content" style="overflow-x: auto;">
                    <div class="' . $this->postType . ' contentWrap">
                        <h1>Categories: ' . ucwords(str_replace('_', ' ', $this->postType)) . '</h1>';

            $output .= $this->postTypeSelector();

            $output .=
                '<div class="categoryTree">
                    ' . $this->createTree() . '
                </div>';

            $output .=
                    '</div>
                </div>

                <script src="/admin/settings/scripts/categories.js"></script>';

            echo $output;
        }
        
        private function showEditor() {
            $mysqli = $GLOBALS['mysqli'];
            
            $category = $mysqli->query("SELECT * FROM `categories` WHERE post_type = '{$this->postType}' AND id = {$_GET['id']}");
            $categories = $mysqli->query("SELECT name, id FROM `categories` WHERE post_type = '{$this->postType}' AND level <= 2");
            
            $output = 
                '<div class="content" style="overflow-x: auto;">
                    <div class="' . $this->postType . ' contentWrap">';
                    
            if($category->num_rows > 0) {
                $category = $category->fetch_assoc();
                
                $output .= 
                    '<h1>Categories: ' . ucwords(str_replace('_', ' ', $this->postType)) . ' ' . $_GET['id'] . '</h1>
                    <a href="./' . str_replace('_', '-', $this->postType) . '">Return to categories</a>
                    
                    <div class="formBlock">
                        <form id="updateCategory">
                            <h3>Edit Category</h3>

                            <input type="hidden" value="' . $category['post_type'] . '" name="catPostType">
                            <input type="hidden" value="' . $_GET['id'] . '" name="catId">

                            <p>
                                <label>Name: </label>
                                <input type="text" name="catName" value="' . $category['name'] . '">
                            </p>

                            <p>
                                <label>Description: </label>
                                <input type="text" name="catDesc" value="' . $category['description'] . '">
                            </p>

                            <p>
                                <label>Image: </label>
                                <input type="text" name="catImage" style="width: calc(100% - 232px - 46px);" value="' . $category['image'] . '">
                                <input type="button" name="catImageSelect" value="Choose Image">
                            </p>

                            <p>
                                <label>Parent Category: </label>
                                <select name="catParent">
                                    <option value="0">No Parent Category</option>';

                                while($row = $categories->fetch_assoc()) {
                                    $output .=
                                        '<option value="' . $row['id'] . '" ' . ($row['id'] == $category['parent_id'] ? 'selected' : '') . '>' . $row['name'] . '</option>';
                                }

                            $output .=
                                '</select>
                            </p>

                            <p>
                                <input type="submit" value="Update Category">
                            </p>

                            <p class="message">' . (isset($_SESSION['categoryMessage']) ? $_SESSION['categoryMessage'] : '') . '</p>
                        </form>
                    </div>';
            }
            else {
                $output .=
                    '<h1>Categories: ' . ucwords(str_replace('_', ' ', $this->postType)) . '</h1>
                    
                    <h2>Category ' . $_GET['id'] . ' does not exist.</h2>
                    
                    <a href="./' . str_replace('_', '-', $this->postType) . '">Return to categories</a>';
            }

            //$output .= $this->postTypeSelector();

            $output .=
                    '</div>
                </div>

                <script src="/admin/settings/scripts/categories.js"></script>';

            echo $output;
        }
    }

    class category {
        private $postType;
        private $output;
        
        public function __construct($postType = 'posts') {
            $postType = str_replace('-', '_', strtolower($postType));

            if($postType != '' && $postType != null) {
                $this->postType = $postType;
            }
            else {
                $this->postType = 'posts';
            }
        }
        
        public function display() {
            $mysqli = $GLOBALS['mysqli'];
            
            if($_GET['url']){
                $parentId = $mysqli->query("SELECT id FROM `categories` WHERE url = '{$_GET['url']}' AND post_type = '{$this->postType}' LIMIT 1");
                
                if($parentId->num_rows > 0) {
                    $parentId = $parentId->fetch_array()[0];
                }
                else {
                    http_response_code(404);
                    header('Location: /404');
                    
                    exit();
                }
            }
            else {
                $parentId = 0;
            }
            
            $categories = $mysqli->query("SELECT * FROM categories WHERE post_type = '{$this->postType}' AND parent_id = {$parentId} ORDER BY name ASC");
            
            if($categories->num_rows > 0) {
                $catList = 
                    '<div class="categoryList" id="' . $this->postType . 'CategoryList">';
                
                while($row = $categories->fetch_assoc()) {
                    $subCategories = $mysqli->query("SELECT COUNT(*) FROM `categories` WHERE parent_id = {$row['id']}");
                    
                    if($subCategories->fetch_array()[0] > 0) {
                        $catLink = '/categories/' . str_replace('_', '-', $this->postType) . '/' . $row['url'];
                    }
                    else {
                        $catLink = '/post-type/' . str_replace('_', '-', $this->postType) . '/category-' . $row['id'];
                    }
                    
                    $catList .=
                        '<div class="categoryListItem" id="' . $this->postType . 'CategoryListItem">
                            <div class="imageWrap">
                                <img src="/admin/images/missingImage.png">
                            </div>
                            
                            <div class="smallContent">
                                <h2><a href="' . $catLink . '">' . $row['name'] . '</a></h2>
                                
                                <p>' . $row['description'] . '</p>
                            </div>
                        </div>';
                }
                
                $catList .=
                    '</div>';
            }
            
            if($parentId == 0) {
                $pageTitle = ucwords(str_replace('_', ' ', $this->postType))  . ' Categories';
            }
            else {
                $pageTitle = ucwords(str_replace('_', ' ', $this->postType))  . ' ' . ucwords(str_replace('-', ' ', $_GET['url']));
            }
            
            $this->output = 
                '<div class="mainInner">
                    <div class="categoryInner" id="' . $this->postType . 'Category">
                        <h1>' . $pageTitle . '</h1>
                        ' . $catList . '
                    </div>
                </div>';
            
            echo $this->output;
        }
        
        public function sidebarTree() {
            $mysqli = $GLOBALS['mysqli'];
            
            $levels = $mysqli->query("SELECT COUNT(*) FROM `categories` WHERE post_type = '{$this->postType}'");
            
            if($levels->fetch_array()[0] > 0) {                
                $output =
                    '<div class="categories sidebarCategories">
                        
                        ' . $clearCat . $this->subCategories(0) . '
                    </div>';
            }
            
            return $output;
        }
        
        private function subCategories($parentId) {
            $mysqli = $GLOBALS['mysqli'];
            
            $categories = $mysqli->query("SELECT * FROM `categories` WHERE post_type = '{$this->postType}' AND parent_id = {$parentId} ORDER BY name ASC");
            
            if(isset($_GET['category'])) {
                $clearCat = '<a id="clearCat" href="/post-type/' . str_replace('_', '-', $this->postType) . '">Clear Category [X]</a>';
            }
            
            if($parentId = 0) {
                $output =
                    '<h3>Categories</h3>' . $clearCat;
            }
            
            if($categories->num_rows > 0) {
                $output .=
                    '<ul>';
                
                while($row = $categories->fetch_assoc()) {
                    $output .=
                        '<li ' . ($row['id'] == $_GET['category'] ? 'class="active"' : '') . '>
                            <a href="/post-type/' . str_replace('_', '-', $this->postType) . '/category-' . $row['id'] . '">' . $row['name'] . '</a>
                            ' . $this->subCategories($row['id']) . '
                        </li>';
                }
                
                $output .=
                    '</ul>';
            
                return $output;
            }
        }
    }

    class navigationEditor {                
        public function __construct($parentId = 0) {
            $this->getNavigationLevel($parentId);
        }

        private function getNavigationLevel($parentId) {
            $mysqli = $GLOBALS['mysqli'];
            $output = '';

            if(isset($_GET['menu'])) {
                $menuId = $_GET['menu'];
            }
            else {
                $menuId = 1;
            }

            $checkMenu = $mysqli->query("SELECT id FROM `menus` WHERE id = {$menuId}");

            if($parentId == 0 && $checkMenu->num_rows >= 1) {
                $output .=
                    '<div class="content" style="overflow-x: auto;">
                        <h1>Navigation</h1>

                        <div class="formBlock">
                            <form id="navigationSelector">';

                                $menus = $mysqli->query("SELECT * FROM `menus` ORDER BY menu_name ASC"); 
                                $menuId = 1;

                                if(isset($_GET['menu']) && $_GET['menu'] > 0) {
                                    $menuId = $_GET['menu'];
                                }

                                if($menus->num_rows > 0) {
                                    $output .=
                                        '<p>
                                            <label>Edit Menu: </label>
                                            <select name="menuSelect">';
                                                while($row = $menus->fetch_assoc()) {
                                                    $output .=
                                                        '<option value="' . $row['id'] . '" ' . ($menuId == $row['id'] ? 'selected' : '') . '>' . $row['menu_name'] . '</option>';
                                                }
                                    $output .=        
                                            '</select>
                                        </p>';
                                }

                        $output .=
                            '</form>

                            <form id="createMenu">                
                                <p>
                                    <label>New Menu Name:</label>
                                    <input type="text" name="menuName">
                                </p>

                                <p>
                                    <input type="submit" value="Create Menu">
                                </p>

                                <p class="message"></p>
                            </form>
                        </div>';

                    $output .=
                        '<div class="menuStructure">
                            <ul id="parent' . $parentId . '" data-level="0" style="padding: 0;">
                                ' . $this->checkChildren($parentId, $menuId) . '
                                <li id="addNav">
                                    <input type="button" name="addNav" value="Add Item">
                                    <input type="button" name="saveMenu" value="Save Menu Structure">
                                    <p class="message"></p>
                                </li>
                            </ul>
                        </div>';

                $output .=
                    '</div>

                    <script src="/admin/settings/scripts/updateNavigation.js"></script>';
            }
            else {
                $output .= '<h1>This menu does not exist.</h1>';
            }

            echo $output;
        }

        private function checkChildren($parentId, $menuId = 1) {
            $mysqli = $GLOBALS['mysqli'];

            $output = '';

            $navItems = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parentId} AND menu_id = {$menuId} ORDER BY position ASC"); 

            if($navItems->num_rows > 0) {

                if($parentId > 0) {
                    $output .= 
                        '<ul id="parent' . $parentId . '" data-level="">';
                }

                while($navItem = $navItems->fetch_assoc()) {
                    $postType = explode('/', explode('/post-type/', $navItem['page_url'])[1])[0];
                    $urlSects = explode('/', $navItem['page_url']);
                    $urlCount = count($urlSects);
                    $url = $urlSects[$urlCount - 1];

                    $output .=
                        '<li class="navItem" id="navItem' . $navItem['item_id'] . '">
                            <div>
                                <span id="id">' . $navItem['item_id'] . '</span>
                                <span id="position">' . $navItem['position'] . '</span>
                                <select name="postTypes">
                                    <option value="" selected disabled>--Select Page--</option>
                                    <option value="customUrl" ' . ($postType == '' ? 'selected' : '') . '>Custom Link</option>
                                    ' . $this->getPostTypes($postType . ';' . $navItem['display_name'] . ';' . $url) . '
                                </select>
                                <div class="hiddenValues" style="' . ($postType != '' ? 'display: none;' : '') . ' margin: 0.5em 0;">
                                    Displayed Name: <input type="text" name="displayName" value="' . $navItem['display_name'] . '">
                                    Link: <input type="text" name="postUrl" value="' . $navItem['page_url'] . '">
                                </div>
                                Image: <input type="text" name="image" value="' . $navItem['image_url'] .'"> <input type="button" name="imageSearch" value="&#128269;" title="Search Image">
                                <input type="button" name="deleteItem" data-item="' . $navItem['item_id'] . '" value="Delete">
                            </div>
                            ' . $this->checkChildren($navItem['item_id']) .  '
                        </li>';
                }

                if($parentId > 0) {
                    $output .= 
                        '</ul>';
                }
            }
            else {
                $output = 
                    '<ul id="parent' . $parentId . '" data-level=""></ul>';
            }

            return $output;
        }

        private function getPostTypes($optionValue) {
            $mysqli = $GLOBALS['mysqli'];
            $json = [];

            $pages = $mysqli->query("SELECT name, url FROM pages");

            if($pages->num_rows > 0) {
                array_push($json, 
                    '<option value="pages;Pages;" id="group" ' . ($optionValue == 'pages;Pages;' ? 'selected' : '') . '>Pages</option>'      
                );

                while($row = $pages->fetch_assoc()) {
                    array_push($json, 
                        '<option value="pages;' . $row['name'] . ';' . $row['url'] . '" ' . ($optionValue == 'pages;' . $row['name'] . ';' . $row['url'] ? 'selected' : '') . '>&nbsp;&nbsp;' . $row['name'] . '</option>'      
                    );
                }
            }

            //Get Posts
            $posts = $mysqli->query("SELECT name, url FROM pages");

            if($posts->num_rows > 0) {
                array_push($json, 
                    '<option value="posts;Posts;" id="group" ' . ($optionValue == 'posts;Posts;' ? 'selected' : '') . '>Posts</option>'      
                );

                while($row = $pages->fetch_assoc()) {
                    array_push($json, 
                        '<option value="posts;' . $row['name'] . ';' . $row['url'] . '" ' . ($optionValue == 'posts;Posts;' . $row['url'] ? 'selected' : '') . '>&nbsp;&nbsp;' . $row['name'] . '</option>'      
                    );
                }
            }

            //Get Custom
            $postTypes = $mysqli->query("SELECT name FROM `custom_posts`");

            if($postTypes->num_rows > 0) {
                while($row = $postTypes->fetch_assoc()) {
                    $postType = str_replace('_', '-', $row['name']);
                    $oPostType = $row['name'];

                    array_push($json, 
                        '<option value="' . $postType . ';' . ucwords(str_replace('-', ' ', $postType)) . ';" id="group" ' . ($optionValue == $postType . ';' . ucwords(str_replace('-', ' ', $postType)) . ';' ? 'selected' : '') . '>' . ucwords(str_replace('-', ' ', $postType)) . '</option>'      
                    );

                    $subPages = $mysqli->query("SELECT name, url FROM `{$oPostType}`");

                    if($subPages->num_rows > 0) {
                        while($row = $subPages->fetch_assoc()) {
                            array_push($json,
                                '<option value="' . $postType . ';' . $row['name'] . ';' . $row['url'] . '" ' . ($optionValue == $postType . ';' . $row['name'] . ';' . $row['url'] ? 'selected' : '') . '>&nbsp;&nbsp;' . $row['name'] . '</option>' 
                            );
                        }
                    }
                }
            }

            return implode($json);
        }
    }

?>
