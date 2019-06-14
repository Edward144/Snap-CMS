<?php
    //Global Variable
    $baseUrl = $_SERVER['DOCUMENT_ROOT'];
    $baseName = $_SERVER['SEVER_NAME'];

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
                        $url = $itemInfo['url'];
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
                                        <option value="" selected disabled>--Select Page--</option>';
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
                                    <option value="" selected disabled>--Select Page--</option>';
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
            
            echo '<script src="settings/scripts/mediaUploads.js"></script>';
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

        public function listCategories($parentId = 0) {
            $mysqli = $GLOBALS['mysqli'];

            $categories = $mysqli->query("SELECT * FROM `categories` WHERE parent_id = {$parentId} ORDER BY position ASC");
            
            if($categories->num_rows > 0) {
                echo '<div class="categories">';

                    while($category = $categories->fetch_assoc()) {
                        $postsCheck = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE category_id = '{$category['id']}'")->fetch_array()[0];
                        $subsCheck = $mysqli->query("SELECT COUNT(*) FROM `categories` WHERE parent_id = '{$category['id']}'")->fetch_array()[0];
                        
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
                                        echo '<a href="/posts?category=' . $category['id'] . '">';
                                    }
                                        if($category['image_url']) {
                                            echo '<div class="categoryImage">';
                                                echo '<img src="' . $category['image_url'] . '">';
                                            echo '</div>';
                                        }
                                   echo '<h3 class="categoryName">' . $category['name'] . '</h3>';

                                        $this->postsCount($category['id']);

                                   echo '<p class="categoryDescription">' . $category['description'] . '</p>
                                    </a>
                                </div>';

                            $this->subCategories($category['id']);

                        echo '</div>';
                    }

                echo '</div>';
            }
            else {
                echo '<p>There are currently no categories.</p>';
            }
        }

        private function postsCount($categoryId) {
            $mysqli = $GLOBALS['mysqli'];

            $postsCheck = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE category_id = '{$categoryId}'")->fetch_array()[0];

            if(($postsCheck == 0 && $this->displayEmptyCount == true) || ($postsCheck > 0)) {
                echo '<h5>' . $postsCheck . ' Items</h5>';
            }
        }

        private function subCategories($categoryId) {
            $mysqli = $GLOBALS['mysqli'];

            $categories = $mysqli->query("SELECT * FROM `categories` WHERE parent_id = '{$categoryId}' ORDER BY position ASC");

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
        
        public function sidebar($parentId = 0) {
            $mysqli = $GLOBALS['mysqli'];
            
            $parentName = $mysqli->query("SELECT name FROM `categories` WHERE id = '{$parentId}'")->fetch_array()[0];
            $categories = $mysqli->query("SELECT * FROM `categories` WHERE parent_id = {$parentId} ORDER BY position ASC");
            $postsWithCats = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE category_id > 0")->fetch_array()[0];
            
            if($categories->num_rows > 0 && $parentId == 0 && $postsWithCats) {
                echo '<h3>Categories</h3>';
            }
            elseif($parentId != 0) {
                echo '<h3>' . $parentName . '</h3>';
            }
            
            if($categories->num_rows > 0 && $postsWithCats) {                
                echo '<ul class="sidebarCategories">';
                
                    while($category = $categories->fetch_assoc()) {
                        $posts = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE visible = 1 and category_id = {$category['id']}")->fetch_array()[0];
                        
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
        public function __construct($level = 0, $parent = 0, $prevLevel = 0) {
            $this->createLevel($level, $parent, $prevLevel);
        }
        
        public function createLevel($level, $parent, $prevLevel) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `categories` WHERE parent_id = {$parent} AND level = {$level} ORDER BY position ASC");
            
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
        
        public function checkChildren($parent, $level) {
            $mysqli = $GLOBALS['mysqli'];
            $level++;
            
            $children = $mysqli->query("SELECT * FROM `categories` WHERE parent_id = {$parent} AND level = {$level} ORDER BY position ASC");
            
            if($children->num_rows > 0) {
                new categoryTree($level, $parent, ($level - 1));
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

?>