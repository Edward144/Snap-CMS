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
                                    if(strpos($setting, 'custom_') == 0) {
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
        public $prefix = '?';
        
        function __construct($last) {
            if($last != null) {
                $this->lastPage = $last;
                $this->items = $last;
            }
            
            if(isset($_GET['category'])) {
                $this->prefix = '?category=' . $_GET['category'] . '&';
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
                
                echo '<div class="pagination">';
                
                    if($this->showFirst == true) {
                        echo '<a href="' . $this->prefix . 'page=' . $this->firstPage . '"><< First</a>';
                    }
                
                    if($this->showPrev == true) {
                        echo '<a href="' . $this->prefix . 'page=' . $prevPage . '">< Prev</a>';
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
                            echo '<a href="' . $this->prefix . 'page=' . $this->i . '">' . $this->i . '</a>';
                        }
                    }
                
                    if($this->showNext == true) {
                        echo '<a href="' . $this->prefix . 'page=' . $nextPage . '">Next ></a>';
                    }
                
                    if($this->showLast == true) {
                        echo '<a href="' . $this->prefix . 'page=' . $this->lastPage . '">Last >></a>';
                    }
                
                echo '</div>';
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
            
                while($item = $items->fetch_assoc()) {
                    $itemInfo = $mysqli->query("SELECT * FROM `pages` WHERE id = {$item['page_id']}")->fetch_assoc();
                        $name = $itemInfo['name'];
                        $url = 'pages/' . $itemInfo['url'];
                        $visible = $itemInfo['visible'];
                        
                    $customUrl = $item['custom_url'];
                    
                    if($item['page_id'] == -1) {
                        if($customUrl == '' || $customUrl == null) {
                            $customUrl = '/posts';
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
                        echo '</li>';

                        $this->checkChildren($item['custom_id'], $item['level']);
                    }
                }
            
            echo '</ul>';
        }
        
        public function checkChildren($parent, $level) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation` WHERE parent_id = {$parent}");
            
            if($items->num_rows > 0) {
                new navigation($parent, $level);
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

    class mediaTree {                    
        public function __construct($path = 'useruploads', $isPopup = false) {
            if($isPopup == false) {
                echo '<div class="mediaInfo">
                        <h2>Filename</h2>

                        <p><strong>URL: </strong><span id="url"></span></p>
                        <p><strong>Filesize: </strong><span id="filesize"></span></p>
                        <p id="dimensions"><strong>Dimensions: </strong><span></span></p>
                        <p id="imageLink"><a href="#" target="_blank">View Full Size</a></p>
                        <p id="download"><a href="#" download>Download</a></p>
                    </div>';
                
                echo '<div class="mediaList">';
            }
            else {                
                echo '<div class="mediaList popup"' . (isset($_GET['f']) ? 'id=""' : 'id="hidden"') . '>
                        <span class="mediaClose"><span>X</span></span>';
            }
            
            $this->findDirectories($path);
            $this->findFiles($path);
            
            if($isPopup == false) {
                echo '<div id="mediaEditOverlay">
                    <div class="formBlock" id="mediaEdit">
                        <div>
                            <span id="mediaEditClose"><span>X</span></span>

                            <form id="mediaEditInput">
                                <p>
                                    <label>Name: </label>
                                    <input type="text" name="newName">
                                </p>

                                <p>
                                    <input type="submit" value="Rename Folder">
                                </p>

                                <p class="message"></p>
                            </form>
                        </div>
                    </div>
                </div>';
            }
            
            echo '<script src="/admin/settings/scripts/mediaUploads.js"></script>';
            echo '</div>';
        }

        public function findDirectories($path) {            
            $dir = new RecursiveDirectoryIterator($path);
            $dir = new RecursiveIteratorIterator($dir);
            $dir->setMaxDepth(1);
            
            foreach($dir as $directory) {
                if(!$directory->getExtension()) {
                    $dirname = explode('/', $directory->getPathName())[1];

                    if(substr($dirname, 0, 1) !== '.' && $directory->getPathName() != $path . '/' . $dirname . '/..') {
                        $dirs = explode('/', $directory->getPathName());
                        $dirsCount = count($dirs) - 1;

                        if($dirs[$dirsCount] == '..') {
                            $url = '';
                            for($i = 0; $i <= $dirsCount - 2; $i++) {
                                $url .= $dirs[$i] . '/';
                            }

                            $url = rtrim($url, '/');

                            $dirname = '..';
                        }
                        elseif($dirs[$dirsCount] == '.') {
                            $url = explode('/.', $directory->getPathName())[0];

                            $dirname = $dirs[$dirsCount - 1];
                        }

                        if($_GET['f'] != $url) {
                            echo
                                '<div class="mediaDir">';
                                
                            if($dirname != '..') {
                                echo '<span class="mediaEdit"><img src="/admin/images/icons/edit.png"></span>
                                <span class="mediaDelete"><img src="/admin/images/icons/bin.png"></span>';
                            }
                            
                            if($_GET['p']) {
                                $query = '?p=' . $_GET['p'] . '&f=';
                            }
                            else {
                                $query = '?f=';
                            }
                            
                            echo 
                                '<a href="' . $query . $url . '">
                                    <div class="mediaImage">
                                        <img src="/admin/images/icons/folder.svg">
                                    </div>

                                    <span class="mediaName">' . $dirname . '</span>
                                </a>
                            </div>';
                        }
                    }
                }
            }
            
            echo
                '<div class="mediaDir" id="mediaAddDirectory">
                    <div class="mediaImage">
                        <img src="/admin/images/icons/folder.svg" style="-webkit-filter: hue-rotate(160deg); filter: hue-rotate(160deg);">
                    </div>

                    <span class="mediaName">Add New Folder</span>
                </div>';
            
            echo
                '<div id="mediaAddOverlay">
                    <div class="formBlock" id="mediaAddHidden">
                        <div>
                            <span id="mediaAddClose"><span>X</span></span>

                            <form id="mediaAddInput">
                                <p>
                                    <label>Folder Name: </label>
                                    <input type="text" name="directoryName">
                                </p>

                                <p>
                                    <input type="Submit" value="Create Folder">
                                </p>

                                <p class="message"></p>
                            </form>
                        </div>
                    </div>
                </div>';
        }

        public function findFiles($path) {            
            $dir = new RecursiveDirectoryIterator($path);
            $dir = new RecursiveIteratorIterator($dir);
            $dir->setMaxDepth(0);
            

            $imgs = ['jpg', 'png', 'gif', 'jpeg', 'svg'];

            foreach($dir as $file) {
                $filename = $file->getFileName();
                $extension = $file->getExtension();
                $dirname = $file->getPathName();
                
                if(in_array($extension, $imgs)) {
                    $mediaImage = $dirname;
                }
                else {
                    $mediaImage = 'images/icons/' . $extension . '.svg';

                    if(!file_exists($mediaImage)) {
                        $mediaImage = 'images/icons/unknown.svg';
                    }
                }

                if(substr($filename, 0, 1) !== '.') {
                    echo
                        '<div class="mediaFile">
                            <span class="mediaEdit"><img src="/admin/images/icons/edit.png"></span>
                            <span class="mediaDelete"><img src="/admin/images/icons/bin.png"></span>
                            
                            <div class="mediaImage">
                                <img src="/admin/' . $mediaImage . '">
                            </div>

                            <span class="mediaName">' . $filename . '</span>
                        </div>';
                }
            }
        }
    }

    class categories { 
        public $displayEmptyCount = false;
        
        public function setEmptyDisplay($value = false) {
            $this->displayEmptyCount = $value;
        }

        public function listCategories($parentId = 0, $postType = '') {
            if(isset($postType) && $postType != '') {
                $postTable = $postType;
                $postType = $postType . '_';
            }
            else {
                $postType = '';
                $postTable = 'posts';
            }
            
            $mysqli = $GLOBALS['mysqli'];

            $categories = $mysqli->query("SELECT * FROM `{$postType}categories` WHERE parent_id = {$parentId} ORDER BY position ASC");
            
            if($categories->num_rows > 0) {
                echo '<div class="categories">';

                    while($category = $categories->fetch_assoc()) {
                        $postsCheck = $mysqli->query("SELECT COUNT(*) FROM `{$postTable}` WHERE category_id = '{$category['id']}'")->fetch_array()[0];
                        $subsCheck = $mysqli->query("SELECT COUNT(*) FROM `{$postType}categories` WHERE parent_id = '{$category['id']}'")->fetch_array()[0];
                        
                        echo
                            '<div class="category" id="category' . $category['id'] . '">
                                <div class="categoryDetails">';
                                    if($postsCheck == 0 && $subsCheck > 0) {
                                        echo '<a href="?c=' . $category['id'] . '">';
                                    }
                                    elseif($postsCheck == 0 && $subsCheck == 0) {
                                        echo '<a href="">';
                                    }
                                    elseif($postsCheck > 0) {
                                        if($postTable == 'posts') {
                                            echo '<a href="/' . $postTable . '?category=' . $category['id'] . '">';
                                        }
                                        else {
                                            echo '<a href="/post-type/' . $postTable . '?category=' . $category['id'] . '">';
                                        }
                                    }
                                        if($category['image_url']) {
                                            echo '<div class="categoryImage">';
                                                echo '<img src="' . $category['image_url'] . '">';
                                            echo '</div>';
                                        }
                                        else {
                                            echo '<div class="categoryImage">';
                                                echo '<img src="/admin/images/missingImage.png">';
                                            echo '</div>';
                                        }
                        
                                   echo '<h3 class="categoryName">' . $category['name'] . '</h3>';

                                        $this->postsCount($category['id'], $postTable);

                                   echo '<p class="categoryDescription">' . $category['description'] . '</p>
                                    </a>
                                </div>';

                            $this->subCategories($category['id'], $postType);

                        echo '</div>';
                    }

                echo '</div>';
            }
            else {
                echo '<p>There are currently no categories.</p>';
            }
        }

        private function postsCount($categoryId, $postTable) {
            $mysqli = $GLOBALS['mysqli'];

            $postsCheck = $mysqli->query("SELECT COUNT(*) FROM `{$postTable}` WHERE category_id = '{$categoryId}'")->fetch_array()[0];

            if(($postsCheck == 0 && $this->displayEmptyCount == true) || ($postsCheck > 0)) {
                echo '<h5>' . $postsCheck . ' Items</h5>';
            }
        }

        private function subCategories($categoryId, $postType) {
            $mysqli = $GLOBALS['mysqli'];

            $categories = $mysqli->query("SELECT * FROM `{$postType}categories` WHERE parent_id = '{$categoryId}' ORDER BY position ASC");

            if($categories->num_rows > 0) {
                echo '<hr><ul class="subCategories">';

                    while($category = $categories->fetch_assoc()) {
                        echo
                            '<li class="subCategory" id="subCategory' . $category['id'] . '">
                                <a href="?c=' . $category['parent_id'] . '">' . $category['name'] . '</a>
                            </li>';
                    }

                echo '</ul>';
            }
        }
        
        public function sidebar($parentId = 0, $postType = null) {
            if(isset($postType) && $postType != '') {
                $postTable = $postType;
                $postType = $postType . '_';
            }
            else {
                $postType = '';
                $postTable = 'posts';
            }
            
            $mysqli = $GLOBALS['mysqli'];
            
            $parentName = $mysqli->query("SELECT name FROM `{$postType}categories` WHERE id = '{$parentId}'")->fetch_array()[0];
            $categories = $mysqli->query("SELECT * FROM `{$postType}categories` WHERE parent_id = {$parentId} ORDER BY position ASC");
            $postsWithCats = $mysqli->query("SELECT COUNT(*) FROM `{$postTable}` WHERE category_id > 0 AND visible = 1")->fetch_array()[0];
            
            if($categories->num_rows > 0 && $parentId == 0 && $postsWithCats) {
                echo '<h3>Categories</h3>';
            }
            elseif($parentId != 0) {
                if($mysqli->query("SELECT COUNT(*) FROM `{$postTable}` WHERE category_id = {$_GET['category']}")->fetch_array()[0] > 0) {
                    echo '<h3>' . $parentName . '<a href="' . $postTable . '" style="margin-left: 0.5em; text-decoration: none;">[X]</a></h3>';
                }
            }
            
            if($categories->num_rows > 0 && $postsWithCats) {                
                echo '<ul class="sidebarCategories">';
                
                    while($category = $categories->fetch_assoc()) {
                        $posts = $mysqli->query("SELECT COUNT(*) FROM `{$postTable}` WHERE visible = 1 and category_id = {$category['id']}")->fetch_array()[0];
                        
                        if($posts > 0) {
                            echo
                                '<li>
                                    <a href="?category=' . $category['id'] . '">
                                        ' . $category['name'] . '
                                    </a>
                                </li>';
                        }
                    }
                
                echo '</ul>';
            }
        }
    }

    class categoryTree {
        public function __construct($level = 0, $parent = 0, $prevLevel = 0, $postType = '') {
            if(isset($postType) && $postType != '') {
                $postType = $postType . '_';
            }
            
            $this->createLevel($level, $parent, $prevLevel, $postType);
        }
        
        public function createLevel($level, $parent, $prevLevel, $postType) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `{$postType}categories` WHERE parent_id = {$parent} AND level = {$level} ORDER BY position ASC");
            
            if($items->num_rows > 0) {
                echo '<ul class="level catEditor" id="level' . $level . '">';
                    while($item = $items->fetch_assoc()) {
                        echo
                            '<li class="catSelector">
                                <p>
                                    <span type="text" class="catId">' . ($item['id'] ? '(' . $item['id'] . ')' : '(?)') . '</span>
                                    <input type="text" name="catName" placeholder="Name" style="width: 200px;" value="' . $item['name'] . '">

                                    <input type="text" name="catDesc" placeholder="Description" style="width: 200px;" value="' . $item['description'] . '">
                                    
                                    <input type="text" name="catImage" placeholder="Image URL" style="width: 200px;" ' . ($item['image_url'] != '' && $item['custom_url'] != null ? 'value="' . $item['custom_url'] . '"' : '') . '>

                                    <input type="button" class="badButton" style="min-width: 0; width: 24px; height: 24px; border-radius: 100%;" value="X" name="delete">
                                </p>';
                            $this->checkChildren($item['custom_id'], $item['level'], $postType);

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
                    '<ul class="level catEditor" id="level' . $level . '">
                        <li id="levelAddition">
                            <p>
                                <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                            </p>
                        </li>
                    </ul>';
            }
            
            if($level == 0) {
                echo '<p>
                        <input type="submit" value="Update Categories">
                    </p>

                    <p class="message"></p>';
            
            
                //Add Hidden Selector To Be Used For Copying
                echo 
                    '<div id="catSelectorMain" style="display: none;">
                        <li class="catSelector">
                            <p>
                                <span type="text" class="catId">(-)</span>
                                <input type="text" name="catName" placeholder="Name" style="width: 200px;" value="">

                                <input type="text" name="catDesc" placeholder="Description" style="width: 200px;" value="">

                                <input type="text" name="catImage" placeholder="Image URL" style="width: 200px;">

                                <input type="button" class="badButton" style="min-width: 0; width: 24px; height: 24px; border-radius: 100%;" value="X" name="delete">
                            </p>

                            <ul class="level catEditor" id="level1">
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
        
        public function checkChildren($parent, $level, $postType) {
            $mysqli = $GLOBALS['mysqli'];
            $level++;
            
            $children = $mysqli->query("SELECT * FROM `{$postType}categories` WHERE parent_id = {$parent} AND level = {$level} ORDER BY position ASC");
            
            if(strpos($postType, '_') !== false) {
                $postType = explode('_', $postType)[0];
            }
            
            if($children->num_rows > 0) {
                new categoryTree($level, $parent, ($level - 1), $postType);
            }
            else {
                echo 
                    '<ul class="level catEditor" id="level' . $level . '">
                        <li id="levelAddition">
                            <p>
                                <input type="button" value="+" name="addNext" style="min-width: 0; border-radius: 100%; height: 24px; width: 24px;">
                            </p>
                        </li>
                    </ul>';
            }
        }
    }

    class postAdmin {
        public $postType;
        public $postTitle;
        public $categoryPre;

        public function __construct($type) {
            if($type != null) {
                $this->postType = $type;            
                $this->postTitle = ucwords(str_replace('_', ' ', $type));

                if($type != 'post') {
                    $this->categoryPre = $this->postType . 's_';
                }
            }
            else {
                echo 'Post type is not defined.';
            }
        }

        public function getPost() {
            if(isset($_GET['p'])) {
                $this->getPostSingle();
            }
            else {
                $this->getPostList();
            }
        }

        private function getFeatured($imageUrl) {
            echo '<h2>Featured Image</h2>';

            if($imageUrl == null || $imageUrl == '') {
                echo 
                    '<div class="noFeatured featuredInner">
                        <span>Select Image</span>
                        
                        <span class="featuredDelete" style="display: none;"><span>X</span></span>

                        <img src="" id="featuredImage" style="display: none">
                    </div>';
            }
            else {
                echo 
                    '<div class="featuredInner">
                        <span style="display: none;">Select Image</span>
                        
                        <span class="featuredDelete"><span>X</span></span>

                        <img src="' . $imageUrl . '" id="featuredImage">
                    </div>';
            }
        }

        public function getPostList() {
            $mysqli = $GLOBALS['mysqli'];

            echo '<h1>' . $this->postTitle . 's</h1>';

            $postCount = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}s`")->fetch_array()[0];
            $pagination = new pagination($postCount);
            $pagination->load();

            echo
                '<div class="formBlock">
                    <form class="addContent" id="add' . $this->postType . '">
                        <p>
                            <input type="submit" value="New ' . $this->postTitle . '">
                        </p>

                        <p class="message"></p>
                    </form>

                    <form id="search' . ucwords($this->postType) . '">
                        <p>
                            <input type="text" name="search" placeholder="Search..." id="' . $pagination->itemLimit .'">
                        </p>
                    </form>
                </div>';

            echo
                '<table>
                    <tr class="headers">
                        <td style="width: 40px;">ID</td>
                        <td style="text-align: left;">' . $this->postTitle . ' Details</td>
                        <td style="width: 180px;">Published</td>
                        <td style="width: 100px;">Actions</td>
                    </tr>';

                    $posts = $mysqli->query("SELECT * FROM `{$this->postType}s` ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");

                    if($posts->num_rows > 0) {
                        while($post = $posts->fetch_assoc()) {
                            echo
                                '<tr class="' . $this->postType . 'Row contentRow">
                                    <td>
                                        <span class="id">' . $post['id'] . '</span>
                                    </td>

                                    <td style="text-align: left;">
                                        <h4>' . $post['name'] . '</h4>
                                        <p>' . $post['description'] . '</p>
                                        <p style="font-size: 0.75em;">URL: ' . $post['url'] . '</p>
                                    </td>

                                    <td>
                                        <p>' . $post['author'] . '</p>
                                        <p>' . $post['date_posted'] . '</p>
                                    </td>

                                    <td>';

                                        if($post['visible'] == 1) {
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
                                <td colspan="4">There are currently no ' . $this->postTitle . 's.</td>
                            </tr>';
                    }
                echo '</table>';

            $pagination->display();
        }

        public function getPostSingle() {
            $mysqli = $GLOBALS['mysqli'];

            $post = $mysqli->prepare("SELECT * FROM `{$this->postType}s` WHERE id = ?");
            $post->bind_param('i', $_GET['p']);
            $post->execute();
            $result = $post->get_result();

            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo
                        '<div class="' . $this->postType . ' contentWrap">
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
                                            <label>Description: </label>
                                            <input type="text" name="description" value="' . $row['description'] . '">
                                        </p>

                                        <p>
                                            <label>Url: </label>
                                            <input type="text" name="url" value="' . $row['url'] . '">
                                        </p>';

                                        if($this->postType != 'page') {
                                            echo '<p>
                                                <label>Category: </label>
                                                <select name="categories">
                                                    <option value="" selected>--Select Category--</option>';

                                                    $categories = $mysqli->query("SELECT id, name FROM `{$this->categoryPre}categories` ORDER BY name ASC");

                                                    while($category = $categories->fetch_assoc()) {
                                                        echo 
                                                            '<option value="' . $category['id'] . '" ' . ($row['category_id'] == $category['id'] ? 'selected' : '') . '>' .
                                                            $category['name'] .
                                                        '</option>';
                                                    }
                                            echo '</select>
                                            </p>';
                                        }
                    
                                    echo '<p class="message"></p>                  
                                    </div>

                                    <div class="right">
                                        <p>
                                            <label>Author: </label>
                                            <select name="author">
                                                <option value="" selected disabled>--Select Author--</option>';

                                                $authors = $mysqli->query("SELECT username, first_name, last_name FROM `users` ORDER BY username ASC");

                                                while($author = $authors->fetch_assoc()) {
                                                    echo '<option value="' . $author['username'] . '" ' . ($author['username'] == $row['author'] ? 'selected' : '') . '>' . $author['username'] . ': ' . $author['first_name'] . ' ' . $author['last_name'] . '</option>';
                                                }

                                        echo '</select>
                                        </p>

                                        <p>
                                            <label>Date Posted: </label>
                                            <input type="datetime-local" step="1" name="date" value="' . str_replace(' ', 'T', $row['date_posted']) . '">
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
                                    </div>
                                </div>

                                <div class="editor">
                                    <textarea name="content">' . $row['content'] . '</textarea>
                                </div>

                                <div class="featuredImage">';
                                    $this->getFeatured($row['image_url']);
                            echo '</div>
                            </form>
                        </div>';

                }
            }
            else {
                echo '<h1>' . $this->postTitle . ' ' .  $_GET['p'] . ' does not exist</h1>';
            }
        }
    }

    class postUser {
        public $postType;
        private $postTitle;
        private $categoryPre;
        private $homepage;
        public $sideOptions;
        public $displaySidebar = 1;
        private $slider = 0;
        private $sliderOutput = [];
        
        public function __construct($type = '') {
            if($type != null) {
                $this->postType = $type;
                $this->postTitle = ucwords(str_replace('_', ' ', $type));

                if($type != 'post') {
                    $this->categoryPre = $this->postType . 's_';
                }
                
                if($type == 'page') {
                    $this->displaySidebar = 0;
                }
            }
            else {
                echo 
                    '<div class="mainInner">
                        <div class="content">
                            <h2>Post type is not defined.</h2>
                        </div>
                    </div>';

                exit();
            }

            $this->checkSettings();
        }

        private function checkSettings() {
            $mysqli = $GLOBALS['mysqli'];

            $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0];
            $hidePosts = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name ='hide_posts'")->fetch_array()[0];

            if($hidePosts == 1 && $homepage != null && $homepage != '' && $this->postType == 'post') {
                header('HTTP/1.1 301 Moved Permenantly');
                header('Location: /');

                exit();
            }
            
            if($homepage != null && $homepage != '') {
                $this->homepage = $mysqli->query("SELECT url FROM `pages` WHERE id = {$homepage}")->fetch_array()[0];
            }
        }
        
        public function sideOptions($sideOptions = false) {
            if($sideOptions == true) {
                $this->sideOptions = true;
            }
        }

        public function getPost() {
            if(isset($_GET['url'])) {
                $this->getPostSingle();
            }
            else {
                $this->getPostList();
            }
        }
        
        private function liveSlider($postId) {
            $mysqli = $GLOBALS['mysqli'];
            $settings = $mysqli->query("SELECT * FROM `banners` WHERE post_type = '{$this->postType}s' AND post_type_id = {$postId} AND visible = 1");
            
            if($settings->num_rows > 0) {
                $this->slider = 1;
                
                $settings = $settings->fetch_assoc();
                $slides = $mysqli->query("SELECT * FROM `banners_slides` WHERE banner_id = {$settings['id']} ORDER BY position ASC");
                $sliderOutput = [];
                
                if($slides->num_rows > 0) {
                    array_push($this->sliderOutput, '<div class="owl-carousel liveSlider">');
                    
                        while($slide = $slides->fetch_assoc()) {
                            array_push($this->sliderOutput,
                                '<div class="liveItem" style="background-image: url(\'' . $slide['live_background'] . '\')">
                                    <div class="liveContent">
                                        <div class="liveContentInner">' .
                                            $slide['live_content'] .
                                        '</div>
                                    </div>
                                </div>'
                            );
                        }
                    
                    array_push($this->sliderOutput, '</div>');
                    
                    array_push($this->sliderOutput, 
                        '<script>
                            $(document).ready(function() {
                                $(".liveSlider").owlCarousel({
                                    ' . ($settings['animation_out'] != null && $settings['animation_out'] != '' ? 'animateOut: "' . $settings['animation_out'] . '", ' : '') . ($settings['animation_in'] != null && $settings['animation_in'] != '' ? 'animateIn: "' . $settings['animation_in'] . '", ' : '') . '                                         
                                    items: 1,
                                    loop: true,
                                    ' . ($settings['speed'] > 0 ? 'autoplay: true, autoplayTimeout: ' . $settings['speed'] . ',' : '') . 
                                    ($settings['speed'] == 0 ? 'autoplay: false,' : '') . '
                                });
                            });
                        </script>'
                    );
                }
            }
        }

        private function getPostSingle() {
            $mysqli = $GLOBALS['mysqli'];
            
            if(isset($this->homepage) && $this->homepage == $_GET['url'] && $this->postType == 'page') {
                header('HTTP/1.1 301 Moved Permenantly');
                header('Location: /');

                exit();
            }
            
            $post = $mysqli->query("SELECT * FROM `{$this->postType}s` WHERE url = '{$_GET['url']}' AND visible = 1");

            if($post->num_rows > 0) {
                while($row = $post->fetch_assoc()) {
                    $author = $row['author'];
                    $authorF = $mysqli->query("SELECT first_name FROM `users` WHERE username = '{$author}'")->fetch_array()[0];
                    $authorL = $mysqli->query("SELECT last_name FROM `users` WHERE username = '{$author}'")->fetch_array()[0];
                    $author = $authorF . ' ' . $authorL;
                    
                    if($this->postType != 'page') {
                        $catId = $row['category_id'];
                        $category = $mysqli->query("SELECT name FROM {$this->categoryPre}categories where id = {$catId}")->fetch_array()[0];
                    }
                    
                    if($row['image_url'] != null && $row['image_url'] != '') {
                        $featuredUrl = $row['image_url'];
                    }
                    
                    $productOptions = $mysqli->query("SELECT * FROM `{$this->categoryPre}options` WHERE post_type_id = {$row['id']}");
                    
                    if($productOptions->num_rows > 0) {
                        $option = $productOptions->fetch_assoc();
                        
                        if($option['gallery_main'] != null && $option['gallery_main'] != '') {
                            $featuredUrl = '/gallery/' . $this->postType . 's/' . $row['id'] . '/' . $option['gallery_main'];
                        }
                        else if($option['gallery_images'] != null && $option['gallery_images'] != '') {
                            $featuredUrl = '/gallery/' . $this->postType . 's/' . $row['id'] . '/' . ltrim(explode('";', $option['gallery_images'])[0], '"');
                        }
                    }
                    
                    $this->liveSlider($row['id']);
                    
                    if($this->slider == 1) {
                        $postOutput .= implode($this->sliderOutput);
                    }
                    else {
                        $postOutput .= 
                            '<div class="hero ' . $this->postType . '" style="' . (isset($featuredUrl) ? '//background-image: url(\'' . $featuredUrl . '\')' : '') . '">'
                            . (isset($featuredUrl) ? '<img src="' . $featuredUrl . '" id="heroImage">' : '') . '
                            <div class="postDetails">
                                <h1>' . $row['name'] . '</h1>';

                                if($category != null && $category != '' && $this->postType != 'page') {
                                    $postOutput .= '<h3>Category: ' . $category . '</h3>';
                                }
                            if($this->postType != 'page') {
                                $postOutput .=
                                    '<div class="author">
                                        <p>
                                            <strong>By: </strong><span>' . ucwords($author) . '</span>
                                            <strong>On: </strong><span>' . date('d/m/Y - H:i:s', strtotime($row['date_posted'])) . '</span>
                                        </p>
                                    </div>';
                            }
                            $postOutput .= '</div>
                                </div>';
                    }

                    $postOutput .=
                        '<div class="mainInner">';
                        
                        if($this->displaySidebar == 1) {
                            include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php');
                        }
                    
                        $postOutput .= 
                                '<div class="content ' . $this->postType . '">';
                        
                        if($option['gallery_images'] != null) {
                            $galleryItems = explode(';', rtrim($option['gallery_images'], ';'));
                    
                            $postOutput .= '<div class="gallery owl-carousel">';
                            
                            if($row['image_url'] != null && $row['image_url'] != '') {
                                $postOutput .= 
                                    '<a href="' . $row['image_url'] . '" data-lightbox="gallery">
                                        <div class="galleryItem">
                                            <img src="' . $row['image_url'] . '">
                                        </div>
                                    </a>';
                            }
                            
                            foreach($galleryItems as $galleryItem) {
                                $galleryItem = ltrim($galleryItem, '"');
                                $galleryItem = rtrim($galleryItem, '"');
                                
                                $postOutput .=
                                    '<a href="/gallery/' . $this->postType . 's/' . $row['id'] . '/' . $galleryItem . '" data-lightbox="gallery">
                                        <div class="galleryItem">
                                            <img src="/gallery/' . $this->postType . 's/' . $row['id'] . '/' . $galleryItem . '">
                                        </div>
                                    </a>';
                            }
                            
                            $postOutput .= '</div>';
                            
                            $postOutput .= 
                                '<script>
                                    $(document).ready(function(){
                                        $(".owl-carousel").owlCarousel({
                                            loop:false,
                                            margin:0,
                                            nav:true,
                                            responsive:{
                                                0:{
                                                    items:1
                                                },
                                                600:{
                                                    items:3
                                                },
                                                1000:{
                                                    items:6
                                                }
                                            },
                                            dots: false
                                        });
                                    });
                                </script>';
                        }
                    
                        $sideOptions = '';
                    
                        if($this->sideOptions == true) {
                            $sideOptions .= 
                                '<div class="sideOptions">
                                    <ul>';
                            
                            if($option['features'] != null && $option['features'] != '') {
                                $sideOptions .= 
                                    '<li class="features" id="inactive">
                                        <h3>Features</h3>
                                        
                                        <div class="sideOptionInner">
                                            <span>' . $option['features'] . '</span>
                                        </div>
                                    </li>';
                            }
                            
                            if($option['specifications'] != null && $option['specifications'] != '') {
                                $specs = explode(';', rtrim($option['specifications'], ';'));
                                
                                $sideOptions .= 
                                        '<li class="output" id="inactive">
                                            <h3>Specifications</h3>

                                            <div class="sideOptionInner">
                                                <table>';
                                            
                                foreach($specs as $specRow) {
                                    $specRow = explode('","', $specRow);
                                    $specName = ltrim($specRow[0], '"');
                                    $specValue = rtrim($specRow[1], '"');
                                    
                                    $sideOptions .=
                                        '<tr>
                                            <td>' . $specName . '</td>
                                            <td>' . $specValue . '</td>
                                        </tr>';
                                }
                                
                                $sideOptions .= 
                                                '</table>
                                            </div>
                                        </li>';
                            }
                            
                            if($option['output'] != null && $option['output'] != '') {
                                $sideOptions .= 
                                    '<li class="output" id="inactive">
                                        <h3>Output</h3>
                                        
                                        <div class="sideOptionInner">
                                            <span>' . $option['output'] . '</span>
                                        </div>
                                    </li>';
                            }
                            
                            if($option['options'] != null && $option['options'] != '') {
                                $sideOptions .= 
                                    '<li class="output" id="inactive">
                                        <h3>Options</h3>
                                        
                                        <div class="sideOptionInner">
                                            <span>' . $option['options'] . '</span>
                                        </div>
                                    </li>';
                            }
                            
                            $sideOptions .= 
                                    '</ul>
                                </div>';
                        }
                    
                                $postOutput .=
                                        '<div class="postContent">'
                                            . $sideOptions
                                            . $row['content'] .
                                        '</div>
                                    </div>
                                </div>';
                    
                    echo $postOutput;
                }
            }
            else {
                http_response_code(404);
                header('Location: /404');

                exit();
            }
        }

        private function getPostList() {
            $mysqli = $GLOBALS['mysqli'];
            
            if(($this->homepage == null || $this->homepage == '') && $this->postType == 'post') {
                header('HTTP/1.1 301 Moved Permenantly');
                header('Location: /');

                exit();
            }

            $postCount = $mysqli->query("SELECT COUNT(*) from `{$this->postType}s` WHERE visible = 1")->fetch_array()[0];

            if(isset($_GET['category'])) {
                $postCount = $mysqli->query("SELECT COUNT(*) from `{$this->postType}s` WHERE visible = 1 AND category_id = {$_GET['category']}")->fetch_array()[0];
            }

            $pagination = new pagination($postCount);
            $pagination->load();

            $posts = $mysqli->query("SELECT * FROM `{$this->postType}s` WHERE visible = 1 ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");

            if(isset($_GET['category'])) {
                $posts = $mysqli->query("SELECT * FROM `{$this->postType}s` WHERE visible = 1 AND category_id = {$_GET['category']} ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");
            }

            if($posts->num_rows > 0) {
                $postOutput .= '<div class="' . $this->postType . 'sList">';
                
                while($row = $posts->fetch_assoc()) {
                    $postOutput .= '<div class="' . $this->postType . '">';
                    
                    if($this->postType != 'page' && $this->postType != 'post') {
                        $galleryItems = $mysqli->query("SELECT gallery_images, gallery_main FROM `{$this->postType}s_options` WHERE post_type_id = {$row['id']}");
                        
                        if($galleryItems->num_rows > 0) {
                            $galleryItem = $galleryItems->fetch_assoc();
                            if($galleryItem['gallery_main'] != null && $galleryItem['gallery_main'] != '') {
                                $postOutput .= 
                                    '<div class="imageWrap">
                                        <a href="' . $this->postType . 's/' . $row['url'] . '">
                                            <img src="/gallery/' . $this->postType . 's/' . $row['id'] . '/' . $galleryItem['gallery_main'] . '">
                                        </a>
                                    </div>';
                            }
                            else if($row['image_url'] != null && $row['image_url'] != '') {
                                $postOutput .= 
                                    '<div class="imageWrap">
                                        <a href="' . $this->postType . 's/' . $row['url'] . '">
                                            <img src="' . $row['image_url'] . '">
                                        </a>
                                    </div>';
                            }
                            else if($galleryItem['gallery_images'] != null && $galleryItem['gallery_images'] != '') {
                                $galleryFirst = ltrim(explode('";', $galleryItem['gallery_images'])[0], '"');
                                
                                $postOutput .= 
                                    '<div class="imageWrap">
                                        <a href="' . $this->postType . 's/' . $row['url'] . '">
                                            <img src="/gallery/' . $this->postType . 's/' . $row['id'] . '/' . $galleryFirst . '">
                                        </a>
                                    </div>';
                            }
                            else {
                                $postOutput .= 
                                    '<div class="imageWrap">
                                        <a href="' . $this->postType . 's/' . $row['url'] . '">
                                            <img src="/admin/images/missingImage.png">
                                        </a>
                                    </div>';
                            }
                        }
                        else {
                            if($row['image_url'] != null && $row['image_url'] != '') {
                                $postOutput .= '<div class="imageWrap">
                                    <img src="' . $row['image_url'] . '">
                                </div>';
                            }
                        }
                    }
                    else if($this->postType == 'post') {
                        if($row['image_url'] != null && $row['image_url'] != '') {
                            $postOutput .= '<div class="imageWrap">
                                <img src="' . $row['image_url'] . '">
                            </div>';
                        }
                    }
                    
                    $postOutput .= '<h2><a href="' . $this->postType . 's/' . $row['url'] . '">' . $row['name'] . '</a></h2>';

                    $length = strlen($row['description']); 

                    if($length <= 200) {
                        $postOutput .= '<p>' . $row['description'] . '<br><a href="' . $this->postType . 's/' . $row['url'] . '">View More</a></p>';
                    }
                    else {
                        $postOutput .= '<p>' . substr($row['description'], 0, 200) . '...<br><a href="' . $this->postType . 's/' . $row['url'] . '">View More</a></p>';
                    }

                    $postOutput .= '</div><hr>';
                }    
                
                $postOutput .= '</div>';
            }
            else {
                $postOutput = '<p>There are currently no ' . strtolower($this->postTitle) . 's.</p>';
            }

            echo 
                '<div class="mainInner">';
                if($this->postType != 'page') {
                    include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php');
                }
            
                echo '<div class="content">
                        <h1>' . $this->postTitle . 's</h1>'
                        . $postOutput;

                    echo $pagination->display() .
                    '</div>
                </div>';
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

            if($type == 'pages' || $type == 'comments') {
                $tableNames = [$type];
            }
            elseif($type == 'posts') {
                $tableNames = [$type, 'categories'];
            }
            else {
                $tableNames = [$type, $type . '_categories'];
            }

            $missingTable = false;

            foreach($tableNames as $tableName) {
                $checkTable->bind_param('ss', $database, $tableName);
                $checkTable->execute();
                $checkResult = $checkTable->get_result();

                if($checkResult->fetch_array()[0] == 0) {
                    $tableError .= 'Error (' . $type . '): ' . $tableName . ' does not exist.<br>';

                    $missingTable = true;
                }
            }

            if($missingTable == false) {
                $postCount = $mysqli->query("SELECT COUNT(*) FROM `{$type}`")->fetch_array()[0];
                $postLatest = $mysqli->query("SELECT name, date_posted FROM `{$type}` ORDER BY id DESC LIMIT 5");

                echo
                    '<div id="' . $id . '">
                        <div class="totalHeader">
                            <span>
                                <h2>' . $postCount . '</h2>
                                <h4>' . ucwords(str_replace('_', ' ', $type)) . '</h4>
                            </span>
                        </div>';

                        if($postLatest->num_rows > 0) {
                            echo 
                                '<div class="latest">
                                    <h4>Latest</h4>';

                                    $i = 1; 

                                    while($post = $postLatest->fetch_assoc()) {
                                        echo
                                            '<p>
                                                <strong>' . $i++ . '. </strong>' . $post['name'] . ' (' . $post['date_posted'] . ')
                                            </p>';
                                    }
                            echo 
                                '</div>';
                        }
                echo 
                    '</div>';
            }
            else {
                echo
                    '<div id="' . $id . '">'
                        . $tableError .
                    '</div>';
            }
        }
    }

    class productOptions {
        public $postType = '';

        public function __construct($postType = '') {
            $mysqli = $GLOBALS['mysqli'];
            
            if(isset($postType) && $postType != '') {
                $this->postType = $postType;
            }
            
            $checkId = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}` WHERE id = {$_GET['p']}")->fetch_array()[0];
            $checkOptions = $mysqli->query("SELECT COUNT(*) FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];
            
            if($checkId <= 0) {
                exit();
            }
            
            if($checkOptions <= 0) {
                echo '<h3>Product options are missing for this post.</h3>';
                
                exit();
            }
            
            echo '<div class="formBlock productOptions ' . $postType . 'Options" style="max-width: 100%; border-bottom: 0;">' .
                    '<form style="max-width: 100%;" enctype="multipart/formdata">';
        }

        public function __destruct() {
            echo '</form></div>';

            echo '<script src="/admin/settings/scripts/productOptions.js"></script>';
        }

        public function addFeatures() {
            $mysqli = $GLOBALS['mysqli'];

            $feature = $mysqli->query("SELECT features FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];

            echo 
                '<h3>Features</h3>
                <p>
                    <textarea class="noTiny" name="featuresOption">' . $feature .'</textarea>
                </p>
                <hr>';
        }

        public function addOutput() {
            $mysqli = $GLOBALS['mysqli'];

            $output = $mysqli->query("SELECT output FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];

            echo 
                '<h3>Output</h3>
                <p>
                    <textarea class="noTiny" name="outputOption">' . $output . '</textarea>
                </p>
                <hr>';
        }

        public function addSpecs() {
            $mysqli = $GLOBALS['mysqli'];

            $specs = $mysqli->query("SELECT specifications FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];
            $specRow = 1;

            if($specs != null && $specs != '') {
                $specRows = explode(';', rtrim($specs, ';'));

                foreach($specRows as $specRow) {
                    $specCols = explode('","', $specRow);
                    $specName = ltrim($specCols[0], '"');
                    $specValue = rtrim($specCols[1], '"');

                    $tableData .=
                        '<tr id="spec' . $specRow . '">
                            <td><input type="text" name="specName" value="' . htmlspecialchars($specName, ENT_COMPAT, 'UTF-8') . '"></td>
                            <td><input type="text" name="specValue" value="' . htmlspecialchars($specValue, ENT_COMPAT, 'UTF-8') .'"></td>
                            <td><input class="badButton" type="button" name="deleteSpec" value="Delete Spec"></td>
                        </tr>';

                    $specRow++;
                }
            }
            else {
                $tableData = 
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


            echo '<h3>Specifications</h3>';

                echo
                    '<table class="specificationOption" style="max-width: 650px;">
                        <tr class="headers">
                            <td>Specification Name</td>
                            <td>Specification Value</td>
                            <td></td>
                        </tr>' .
                        $tableData .
                    '</table>';

            echo 
                '<p class="specActions">
                    <input type="button" name="addSpec" value="Add Spec Row">
                </p>';

            echo '<hr>';
        }

        public function addGallery() {
            $mysqli = $GLOBALS['mysqli'];

            $galleryImages = $mysqli->query("SELECT gallery_images FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];
            $galleryImages = explode(';', rtrim($galleryImages, ';'));                    
            $galleryMain = $mysqli->query("SELECT gallery_main FROM `{$this->postType}_options` WHERE post_type_id = {$_GET['p']}")->fetch_array()[0];
            
            echo '<h3>Gallery</h3>';

                echo 
                    '<input type="file" name="galleryOption" multiple>
                    <div class="galleryItems current">';

                foreach($galleryImages as $image) {
                    $image = ltrim($image, '"');
                    $image = rtrim($image, '"');
                    
                    if($image != null && $image != '') {
                        echo
                            '<div class="galleryItem"' . ($galleryMain == $image ? 'id="galleryMain"' : '') . '>
                                <span class="galleryDelete"><img src="/admin/images/icons/bin.png"></span>
                                <img class="galleryImage" src="/gallery/products/1/' . $image . '" alt="' . $image . '">
                            </div>';
                    }
                }

                echo
                    '</div>

                    <div class="galleryItems uploaded">
                    </div>';

            echo 
                '<p class="galleryMessage"></p>
                <hr>';
        }

        public function addAll() {
            $this->addGallery();
            $this->addFeatures();
            $this->addSpecs();
            $this->addOutput();
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
                        <td style="width: 40px;">ID</td>
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
                                        <p>Displayed On: ' . ucwords(rtrim($banner['post_type'], 's')) . $postName  . '</p>
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

            $pagination->display();
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
                                                <option value="pages" ' . ($row['post_type'] == 'pages' ? 'selected' : '') . '>Page</options>
                                                <option value="posts" ' . ($row['post_type'] == 'posts' ? 'selected' : '') . '>Post</options>';
                                                
                                                if($postTypes->num_rows > 0) {
                                                    while($type = $postTypes->fetch_assoc()) {
                                                        echo '<option value="' . $type['name'] . 's" ' . ($type['name'] . 's' == $row['post_type'] ? 'selected' : '') . '>' . ucwords($type['name']) . 's</option>';
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

?>
